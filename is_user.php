<?php


// Check if someone is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {

    // Check if the logged-in person is an admin
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin_panel.php"); // Redirect to admin panel
        exit();
    }
    // If user is logged in but not admin, just continue
}
// If not logged in, also allow viewing (no redirect)