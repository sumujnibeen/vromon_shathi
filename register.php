<?php
session_start();

// Save referring page to redirect back after successful registration
if (!isset($_SESSION['redirect_url']) && isset($_SERVER['HTTP_REFERER'])) {
  $previous = basename($_SERVER['HTTP_REFERER']);
  if ($previous !== 'login.php' && $previous !== 'register.php') {
    $_SESSION['redirect_url'] = $_SERVER['HTTP_REFERER'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Vromon Sathi</title>
  <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="icon" type="image/png" href="images/logo.png">
  <?php include 'global_css.php'; ?>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg p-4 rounded-4">

          <!-- Logo -->
          <div class="text-center mb-3">
            <img src="images/logo.png" alt="Vromon Sathi Logo" style="max-height: 60px;">
          </div>

          <!-- Title -->
          <h4 class="fw-bold text-center mb-4 text-success">Create Account</h4>

          <!-- Error Message -->
          <?php if (isset($_SESSION['register_error'])): ?>
            <div class="alert alert-danger text-center py-2">
              <?= $_SESSION['register_error']; ?>
            </div>
            <?php unset($_SESSION['register_error']); ?>
          <?php endif; ?>

          <!-- Register Form -->
          <form action="register_manager.php" method="POST">
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input name="name" type="text" class="form-control" id="name" placeholder="Enter full name"
                required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input name="email" type="email" class="form-control" id="email" placeholder="Enter email"
                required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input name="password" type="password" class="form-control" id="password"
                placeholder="Enter password" required>
            </div>

            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirm Password</label>
              <input name="confirm_password" type="password" class="form-control" id="confirmPassword"
                placeholder="Confirm password" required>
            </div>

            <button name="submit" type="submit" class="btn btn-success w-100 rounded-pill">Register</button>
          </form>

          <!-- Extra Links -->
          <div class="text-center mt-3">
            <a href="login.php" class="btn btn-outline-success w-100 rounded-pill">
              Already have an account? Login
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>