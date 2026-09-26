<?php
include 'auth.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header('Location: index.php');
    exit;
}
?>
<!-- ignore -->