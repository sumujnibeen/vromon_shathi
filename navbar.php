<?php

include 'db.php';

if (!isset($_SESSION)) {
    session_start();
}



$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
    <div class="container">
        <!-- Logo + Brand -->
        <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="index.php">
            <img src="images/logo.png" alt="Vromon Sathi Logo" height="40" class="me-2">
            Vromon Sathi
        </a>

        <!-- Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark link-success <?php if ($current_page == 'discover.php') echo 'border-bottom border-2 border-success active'; ?>"
                        href="discover.php">Discover</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark link-success <?php if ($current_page == 'booking.php') echo 'border-bottom border-2 border-success active'; ?>"
                        href="booking.php">Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark link-success <?php if ($current_page == 'emergency.php') echo 'border-bottom border-2 border-success active'; ?>"
                        href="emergency.php">Emergency</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark link-success <?php if ($current_page == 'announcement.php') echo 'border-bottom border-2 border-success active'; ?>"
                        href="announcement.php">Announcement</a>
                </li>
            </ul>

            <!-- Dynamic Button -->
            <?php if (isset($_SESSION['status']) && $_SESSION['status'] == 1): ?>
            <a href="user_panel/user_panel.php" class="btn btn-success rounded-pill px-3 fw-semibold">
                Profile
            </a>
            <a href="logout_manager.php" class="btn btn-danger rounded-pill px-3 fw-semibold">
                Logout
            </a>

            <?php else: ?>
            <a href="login.php" class="btn btn-success rounded-pill px-3 fw-semibold">
                Login
            </a>
            <?php endif; ?>
        </div>
    </div>
</nav>