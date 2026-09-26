<?php
include('controller.php');

$db = connect();

session_start();

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields'); window.location.href='login.php';</script>";
        exit;
    }

    // DB Query
    $stmt = $db->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ Session set s
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];

        // Redirect to user panel
        header("Location: user_panel.php");
        exit;
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}