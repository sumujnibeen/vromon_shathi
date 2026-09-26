<?php
session_start();
include 'navbar.php';
include 'auth.php';
include 'is_admin.php';

if (!isset($_GET['type']) || !isset($_GET['id'])) {
  die("<p class='text-center text-danger mt-5'>Invalid request.</p>");
}

$type = $_GET['type'];
$id = intval($_GET['id']);
?>

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

    <div class="container py-5 text-center">
        <div class="card shadow mx-auto" style="max-width:600px;">
            <div class="card-body">
                <h3 class="fw-bold text-success mb-3">Rate This <?php echo ucfirst($type); ?></h3>

                <form action="rating_update.php" method="POST">
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <!-- Numeric Rating Input -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-warning">Your Rating :</label>
                        <input type="number" name="rating" class="form-control text-center" min="0" max="5" step="0.1"
                            placeholder="Enter rating between 0 and 5" required>
                    </div>

                    <div class="mb-4">
                        <textarea name="comment" class="form-control" rows="3"
                            placeholder="Write your comment..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold">
                        Submit Rating
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>