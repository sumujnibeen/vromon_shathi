<?php
include 'navbar.php';
include 'auth.php';
include 'is_admin.php';

// STEP 1: Handle Booking Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pickup_location'])) {
    $user_id = $_SESSION['user_id'];
    $pickup = $_POST['pickup_location'];
    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $contact = $_POST['contact'];
    $num_vehicles = (int) $_POST['number_of_vehicles'];

    // Fetch vehicle info
    $stmt = $conn->prepare("SELECT vehicle_id, vehicle_availability_number, vehicle_price 
                            FROM vehicle 
                            WHERE vehicle_route = ? 
                            ORDER BY vehicle_availability_number DESC 
                            LIMIT 1");
    $stmt->bind_param("s", $pickup);
    $stmt->execute();
    $vehicle = $stmt->get_result()->fetch_assoc();

    if (!$vehicle) {
        echo "<script>alert('No vehicle found for this route.'); window.location='book_vehicle.php';</script>";
        exit;
    }

    // Check availability
    if ($num_vehicles > $vehicle['vehicle_availability_number']) {
        echo "<script>alert('Requested number exceeds available vehicles.'); window.location='book_vehicle.php';</script>";
        exit;
    }

    // Calculate total price
    $total_price = $num_vehicles * $vehicle['vehicle_price'];

    // Insert booking (status = 0)
    $stmt = $conn->prepare("INSERT INTO booking 
        (user_id, service_type, vehicle_id, booking_date, booking_time, destination, contact_number, booking_status, pay_amount) 
        VALUES (?, 'vehicle', ?, ?, ?, ?, ?, 0, ?)");
    $stmt->bind_param("iissssd", $user_id, $vehicle['vehicle_id'], $date, $time, $destination, $contact, $total_price);

    if ($stmt->execute()) {
        // *Do NOT reduce availability here anymore*
        // Redirect to checkout with amount
        header("Location: checkout.php?amount=$total_price&booking_id=" . $stmt->insert_id);
        exit;
    } else {
        echo "<script>alert('Error while booking vehicle.'); window.location='book_vehicle.php';</script>";
    }
}

// STEP 2: After Payment (Check Booking Status)
if (isset($_GET['booking_id'])) {
    $booking_id = (int) $_GET['booking_id'];

    $check = $conn->prepare("SELECT booking_status, pay_amount, vehicle_id FROM booking WHERE booking_id = ?");
    $check->bind_param("i", $booking_id);
    $check->execute();
    $booking = $check->get_result()->fetch_assoc();

    if (!$booking) {
        echo "<script>alert('Booking not found.'); window.location='book_vehicle.php';</script>";
        exit;
    }

    if ($booking['booking_status'] == 1 && $booking['pay_amount'] > 0) {
        // *Reduce vehicle availability here AFTER booking_status = 1*
        if ($booking['vehicle_id']) {
            $stmt2 = $conn->prepare("UPDATE vehicle 
                                     SET vehicle_availability_number = vehicle_availability_number - 1 
                                     WHERE vehicle_id = ?");
            $stmt2->bind_param("i", $booking['vehicle_id']);
            $stmt2->execute();
        }

        echo "<script>alert('Booking confirmed. Payment received.'); window.location='booking.php';</script>";
    } else {
        echo "<script>alert('Payment pending or booking not confirmed.'); window.location='booking.php';</script>";
    }
}