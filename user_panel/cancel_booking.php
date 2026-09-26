<?php

include('controller.php');
$db = connect();
session_start();
include 'is_admin.php';

//  লগইন চেক
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = $_POST['booking_id'] ?? null;

if ($booking_id) {
    //without changing db,cancel houya id session e store krbo
    $_SESSION['cancelled_booking'][$booking_id] = true;
}

//  user_bookings.php  redirect
header("Location: user_bookings.php");
exit;