<?php
session_start();
include('db.php');
$conn = connect();

// Redirect if form not submitted
if (!isset($_POST['submit'])) {
    header('Location: register.php');
    exit;
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Check if passwords match
if ($password !== $confirm_password) {
    $_SESSION['register_error'] = "Passwords do not match.";
    header('Location: register.php');
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT * FROM user WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['register_error'] = "Email already registered.";
    header('Location: register.php');
    exit;
}

// Hash password securely
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Default role for new users
$role = 'user';

// Insert new user record
$stmt = $conn->prepare("INSERT INTO user (Name, Email, Password, Role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

if ($stmt->execute()) {
    // Get the inserted user ID
    $user_id = $stmt->insert_id;

    // Optional: auto-login user after registration
    $_SESSION['status'] = 1;
    $_SESSION['name'] = $name;
    $_SESSION['role'] = $role;
    $_SESSION['email'] = $email;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['loggedin'] = true;

    // Redirect to homepage (or admin panel if admin)

    header("Location: login.php");
    exit;
} else {
    $_SESSION['register_error'] = "Something went wrong. Please try again.";
    header('Location: register.php');
    exit;
}
