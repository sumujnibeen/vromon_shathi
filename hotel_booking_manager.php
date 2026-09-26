<?php
include 'auth.php';
include 'db.php';
$conn = connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $hotel_id = $_POST['hotel_id'];
    $rooms = (int) $_POST['rooms'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $contact = $_POST['contact'];

    // Fetch hotel info
    $stmt = $conn->prepare("SELECT rooms_available FROM hotel WHERE hotel_id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $hotel = $stmt->get_result()->fetch_assoc();

    if (!$hotel) {
        echo "<script>alert('Invalid hotel selected.'); window.location='book_hotel.php';</script>";
        exit;
    }

    // Check availability
    if ($rooms > $hotel['rooms_available']) {
        echo "<script>alert('Requested rooms exceed available rooms.'); window.location='book_hotel.php';</script>";
        exit;
    }

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO booking 
        (user_id, service_type, hotel_id, booking_date, booking_time, destination, contact_number) 
        VALUES (?, 'hotel', ?, ?, '00:00:00', '', ?)");
    $today = date('Y-m-d');
    $stmt->bind_param("iiss", $user_id, $hotel_id, $today, $contact);

    if ($stmt->execute()) {
        // Update available rooms
        $stmt2 = $conn->prepare("UPDATE hotel SET rooms_available = rooms_available - ? WHERE hotel_id = ?");
        $stmt2->bind_param("ii", $rooms, $hotel_id);
        $stmt2->execute();

        echo "<script>alert('Hotel booked successfully!'); window.location='booking.php';</script>";
    } else {
        echo "<script>alert('Error while booking hotel.'); window.location='book_hotel.php';</script>";
    }
}