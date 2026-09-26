<?php
include('controller.php');
$db = connect();
session_start();
include 'is_admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $name = trim($_POST['full_name']); //  form er name match
    $current_password = $_POST['current_password'];

    // 🔹 bring password from database
    $stmt = $db->prepare("SELECT password FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("<script>alert('User not found!'); window.history.back();</script>");
    }

    // password verify
    if (!password_verify($current_password, $user['password'])) {
        die("<script>alert('Incorrect password!'); window.history.back();</script>");
    }

    // ✅ name update koro
    $stmt = $db->prepare("UPDATE user SET name = ? WHERE user_id = ?");
    $stmt->bind_param("si", $name, $user_id);

    if ($stmt->execute()) {
        // ✅ success message with redirect
        $_SESSION['update_success'] = "Profile updated successfully!";
        header("Location: user_panel.php");
        exit;
    } else {
        echo "<script>alert('Update failed! Please try again.'); window.history.back();</script>";
    }
}