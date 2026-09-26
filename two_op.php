<?php
include 'admin_auth.php';
include 'global_css.php'; // Include global CSS
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Choice - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
    body {
        background-color: black;
        color: white;
        text-align: center;
        font-family: 'Poppins', sans-serif;
    }

    .choice-container {
        position: relative;
        display: inline-block;
        margin-top: 30px;
    }

    .choice-container img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .btn-choice {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 2px solid white;
        border-radius: 50px;
        padding: 10px 20px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-choice:hover {
        background-color: silver;
        color: black;
    }

    /* Positioning buttons on the hands */
    .btn-left {
        left: 10%;
        bottom: 30%;
    }

    .btn-right {
        right: 10%;
        bottom: 30%;
    }

    @media (max-width: 768px) {
        .btn-left {
            left: 20%;
            bottom: 25%;
        }

        .btn-right {
            right: 20%;
            bottom: 25%;
        }
    }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <h3 class="fw-bold mb-4 ">This page is only for Users </h3>
        <p class="text-secondary">You can go...</p>

        <div class="choice-container">
            <img src="images/two_op.jpg" alt="Choose your path">
            <a href="index.php" class="btn-choice btn-left text-danger border-danger ">Home Page</a>
            <a href="admin/admin_pannel.php" class="btn-choice btn-right text-primary border-primary">Admin Panel</a>
        </div>
    </div>

    <?php

    include 'footer.php'; ?>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>