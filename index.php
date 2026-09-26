<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vromon Sathi - Kishoreganj</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">

    <?php include 'global_css.php'; ?>

</head>

<body>

    <?php
    include 'db.php';
    include 'calc_rev.php';
    include 'navbar.php';
    ?>

    <!-- Main Section -->
    <section class="position-relative text-center text-white container rounded shadow-sm p-5 main-section"
        style="height: 80vh; overflow: hidden;">

        <!-- cover image ( background image) -->
        <div class="position-absolute top-0 start-0 w-100 h-100 rounded" style="background: url('images/kishoreganj.jpg') center/cover no-repeat; 
              ">
        </div>

        <!-- Darker Overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100 rounded" style="background: rgba(0, 0, 0, 0.65);">
        </div>

        <!-- text n btn -->
        <div class="position-relative d-flex flex-column justify-content-center align-items-center h-100">
            <h1 class="display-3 fw-bold text-shadow ">
                Welcome to Kishoreganj
            </h1>
            <p class="lead mb-4 text-shadow">
                Let's explore together
            </p>
            <a href="discover.php" class="btn btn-success btn-lg rounded-pill px-4">Discover Now</a>
        </div>
    </section>

    <!-- description -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold">About Kishoreganj</h2>
            <p class="text-muted mt-3">
                Kishoreganj, in the Dhaka Division of Bangladesh, is rich with natural beauty and cultural heritage.
                From the world's largest Eid congregation at Sholakia to the breathtaking views of Nikli Haor,
                Kishoreganj offers unforgettable experiences for travelers.
                Vromon Sathi brings these wonders closer to you.
            </p>
        </div>
    </section>

    <!-- top dstination for qz view -->
    <?php include 'top_destinations.php'; ?>

    <!-- qz link -->
    <section class="py-5 text-center">
        <div class="container">
            <h2 class="fw-bold">Stay Informed</h2>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="emergency.php" class="btn btn-danger px-4 rounded-pill">Emergency</a>
                <a href="announcement.php" class="btn btn-warning px-4 rounded-pill">Announcements</a>
            </div>
        </div>
    </section>

    <!-- fotter -->
    <?php include 'footer.php'; ?>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>