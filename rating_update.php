<?php
session_start();
include 'db.php';
include 'auth.php';
include 'is_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type'];
    $id = intval($_POST['id']);
    $rating = floatval($_POST['rating']); // Use float for decimal ratings
    $comment = trim($_POST['comment']);

    // Validate input
    $validTypes = ['hotel', 'guide', 'destination', 'event'];
    if (!in_array($type, $validTypes)) {
        die("Invalid type");
    }

    if ($rating < 0 || $rating > 5) {
        die("Invalid rating value. Must be between 0 and 5.");
    }

    // Check if user already rated
    $check = $conn->prepare("SELECT * FROM review WHERE User_id=? AND Target_type=? AND Target_id=?");
    $check->bind_param("isi", $user_id, $type, $id);
    $check->execute();
    $existing = $check->get_result();;
    if ($existing->num_rows > 0) {
        // Update existing review
        $stmt = $conn->prepare("
        UPDATE review 
        SET Rating=?, Comment=? 
        WHERE User_id=? AND Target_type=? AND Target_id=?");
        $stmt->bind_param("dsisi", $rating, $comment, $user_id, $type, $id);
        $stmt->execute();

        $stmt->close();
    }
} else {
    // Insert new review
    $stmt = $conn->prepare("
            INSERT INTO review (User_id, Target_type, Target_id, Rating, Comment, Created_at)
            VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isids", $user_id, $type, $id, $rating, $comment);
    $stmt->execute();

    $stmt->close();
}

// Recalculate average rating
$avg = $conn->prepare("SELECT AVG(Rating) AS avg FROM review WHERE Target_type=? AND Target_id=?");
$avg->bind_param("si", $type, $id);
$avg->execute();
$res = $avg->get_result()->fetch_assoc();
$newRating = round($res['avg'], 1);

// Update the related table's rating
$update = $conn->prepare("UPDATE $type SET Rating=? WHERE {$type}_id=?");
$update->bind_param("di", $newRating, $id);
$update->execute();

// Close all
$check->close();
$avg->close();
$update->close();
$conn->close();

echo "<script>
        alert('Thank you for your feedback!');</script>";
exit;