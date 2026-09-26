<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking - Vromon Sathi</title>
  <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="icon" type="image/png" href="images/logo.png">
  <?php include 'global_css.php'; ?>
</head>
<body>

<?php include 'navbar.php'; ?>  
<!-- Page Header -->
<header class="py-5">
  <div class="container">
    <div class="bg-success text-white text-center p-5 rounded shadow">
      <!-- Logo -->
      <div class="mb-2">
        <img src="images/logo_footer.png" alt="Vromon Sathi Logo" style="max-height: 60px;">
      </div>

      <!-- Brand Name -->
      <h5 class="fw-bold mb-3">Vromon Sathi</h5>

      <!-- Divider -->
      <hr class="border-light" style="opacity: 0.3;">

      <!-- Page Title -->
      <h1 class="fw-bold">Booking</h1>
      <p class="lead mb-0">Choose what you want to book?</p>
    </div>
  </div>
</header>

<!-- Booking Options -->
<section class="py-5 bg-light">
  <div class="container">
    <h3 class="fw-bold text-center mb-5">What to Book?</h3>
    <div class="row g-4">

      <!-- Vehicle -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm text-center p-4">
          <img src="images/vehicle_booking.jpg" class="card-img-top mb-3" alt="Vehicle" style="height:180px; object-fit:cover;">
          <h5 class="card-title">Vehicle</h5>
          <p class="card-text text-muted">Book cars, microbuses, and boats for your trip.</p>
          <a href="book_vehicle.php" class="btn btn-success">Book Now</a>
        </div>
      </div>

      <!-- Hotel -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm text-center p-4">
          <img src="images/hotel_booking.jpg" class="card-img-top mb-3" alt="Hotel" style="height:180px; object-fit:cover;">
          <h5 class="card-title">Hotel</h5>
          <p class="card-text text-muted">Reserve the best hotels and resorts in Kishoreganj.</p>
          <a href="book_hotel.php" class="btn btn-success">Book Now</a>
        </div>
      </div>

      <!-- Train -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm text-center p-4">
          <img src="images/train_booking.jpg" class="card-img-top mb-3" alt="Train" style="height:180px; object-fit:cover;">
          <h5 class="card-title">Train</h5>
          <p class="card-text text-muted">Book your train tickets online.</p>
          <a href="https://eticket.railway.gov.bd/" target="_blank" class="btn btn-outline-success">Go to Railway Website</a>
        </div>
      </div>

      <!-- Guide -->
      <div class="col-md-6">
        <div class="card h-100 shadow-sm text-center p-4">
          <img src="images/guide_booking.jpg" class="card-img-top mb-3" alt="Guide" style="height:200px; object-fit:cover;">
          <h5 class="card-title">Guide</h5>
          <p class="card-text text-muted">Hire an experienced local guide for your journey.</p>
          <a href="book_guide.php" class="btn btn-success">Hire Guide</a>
        </div>
      </div>

      <!-- Event -->
      <div class="col-md-6">
        <div class="card h-100 shadow-sm text-center p-4">
          <img src="images/event.jpg" class="card-img-top mb-3" alt="Event" style="height:200px; object-fit:cover;">
          <h5 class="card-title">Event</h5>
          <p class="card-text text-muted">Book tickets for cultural and local events.</p>
          <a href="book_event.php" class="btn btn-success">Book Event</a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
