<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Discover - Vromon Sathi</title>
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

        <!-- Name -->
        <h5 class="fw-bold mb-3">Vromon Sathi</h5>

        <!-- Divider line -->
        <hr class="border-light" style="opacity: 0.3;">

        <h1 class="fw-bold">Discover Kishoreganj</h1>
        <p class="lead mb-0">Explore top destinations, hotels, and guides</p>
      </div>
    </div>
  </header>


  <?php include 'top_destinations.php'; ?>
  <?php include 'top_hotels.php'; ?>
  <?php include 'top_guides.php'; ?>


  <!-- footer -->
  <?php include 'footer.php'; ?>

  <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>