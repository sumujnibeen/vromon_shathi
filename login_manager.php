<?php
session_start();
include('db.php');

// Redirect if form not submitted
if (!isset($_POST['submit'])) {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// Prepare and execute query (select user by email)
$stmt = $conn->prepare("SELECT * FROM user WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if email exists
if ($result->num_rows === 0) {
    $_SESSION['login_error'] = "Invalid email.";
    header('Location: login.php');
    exit;
}

$user = $result->fetch_assoc();

// Verify hashed password
if (!password_verify($password, $user['Password'])) {
    $_SESSION['login_error'] = "Invalid password.";
    header('Location: login.php');
    exit;
}

// Set session variables
$_SESSION['status'] = 1;
$_SESSION['name'] = $user['Name'];
$_SESSION['role'] = $user['Role'];
$_SESSION['email'] = $user['Email'];
$_SESSION['user_id'] = $user['User_id']; // Correct key name
$_SESSION['loggedin'] = true;

//include("is_admin.php");
// Redirect to the previous page or homepage
//$redirect = $_SESSION['redirect_url'] ?? 'index.php';
//unset($_SESSION['redirect_url']);

header("Location: index.php");
exit;