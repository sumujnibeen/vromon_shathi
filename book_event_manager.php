<?php
include 'navbar.php';
include 'auth.php';
include 'is_admin.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $contact = $_POST['contact'];
    $num_seats = (int) $_POST['number_of_seats'];

    // Fetch event availability and seats
    $stmt = $conn->prepare("SELECT Seats_available, availability FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    if (!$event || $event['availability'] == 0) {
        echo "<script>alert('This event is not available.'); window.location='book_event.php';</script>";
        exit;
    }

    if ($num_seats > $event['Seats_available']) {
        echo "<script>alert('Requested number of seats exceeds available seats.'); window.location='book_event.php';</script>";
        exit;
    }

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO booking
        (user_id, service_type, event_id, booking_date, booking_time, contact_number, seats_booked)
        VALUES (?, 'event', ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssi", $user_id, $event_id, $date, $time, $contact, $num_seats);

    if ($stmt->execute()) {
        // Reduce Seats_available
        $stmt2 = $conn->prepare("UPDATE event SET Seats_available = Seats_available - ? WHERE event_id = ?");
        $stmt2->bind_param("ii", $num_seats, $event_id);
        $stmt2->execute();

        echo "<script>alert('Event booked successfully!'); window.location='booking.php';</script>";
    } else {
        echo "<script>alert('Error while booking event.'); window.location='book_event.php';</script>";
    }
}