<?php
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 1) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}
?>
<!--    ignore -->