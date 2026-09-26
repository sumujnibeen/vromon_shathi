<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vromon Sathi</title>
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
                    <div class="text-center mb-3">
                        <img src="images/logo.png" alt="Vromon Sathi Logo" style="max-height: 60px;">
                    </div>

                    <h4 class="fw-bold text-center mb-4 text-success">Login</h4>

                    <!-- Error Message -->
                    <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger text-center py-2">
                        <?= $_SESSION['login_error']; ?>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form action="login_manager.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password"
                                placeholder="Password" required>
                        </div>



                        <button type="submit" name="submit" class="btn btn-success w-100 rounded-pill">Login</button>
                    </form>



                    <div class="text-center mt-3">
                        <a href="register.php" class="btn btn-outline-success w-100 rounded-pill">Create an Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>