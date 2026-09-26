<?php
include 'navbar.php';
include 'auth.php';
include 'is_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guide_id'])) {

    $user_id = $_SESSION['user_id'];
    $guide_id = (int) $_POST['guide_id'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $contact = $_POST['contact'];

    // STEP 1: Verify guide availability and get rate
    $stmt = $conn->prepare("SELECT availability, Price FROM guide WHERE guide_id = ?");
    $stmt->bind_param("i", $guide_id);
    $stmt->execute();
    $guide = $stmt->get_result()->fetch_assoc();

    if (!$guide || $guide['availability'] == 0) {
        echo "<script>alert('This guide is not available.'); window.location='book_guide.php';</script>";
        exit;
    }

    // STEP 2: Calculate pay amount
    $pay_amount = $guide['Price'];

    // STEP 3: Insert booking with booking_status = 0
    $stmt = $conn->prepare("INSERT INTO booking 
        (user_id, service_type, guide_id, booking_date, booking_time, destination, contact_number, booking_status, pay_amount) 
        VALUES (?, 'guide', ?, ?, ?, ?, ?, 0, ?)");
    $stmt->bind_param("iissssd", $user_id, $guide_id, $date, $time, $destination, $contact, $pay_amount);

    if ($stmt->execute()) {

        // STEP 4: Update guide availability
        $stmt2 = $conn->prepare("UPDATE guide SET availability = 0 WHERE guide_id = ?");
        $stmt2->bind_param("i", $guide_id);
        $stmt2->execute();

        // STEP 5: Redirect to checkout
        $booking_id = $stmt->insert_id;
        header("Location: checkout.php?amount=$pay_amount&booking_id=$booking_id");
        exit;
    } else {
        echo "<script>alert('Error while booking guide.'); window.location='book_guide.php';</script>";
    }
}