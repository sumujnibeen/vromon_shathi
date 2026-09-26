<?php include 'navbar.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM hotel WHERE Hotel_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $hotel = $result->fetch_assoc();
    } else {
        die("<p class='text-center text-danger mt-5'>Hotel not found.</p>");
    }
    $stmt->close();
} else {
    die("<p class='text-center text-danger mt-5'>No hotel ID provided.</p>");
}
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



<!-- Main Section -->
<section class="position-relative text-center text-white container rounded shadow-sm p-5 main-section"
    style="height: 80vh; overflow: hidden;">

    <!-- Background -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('<?php echo $hotel['Photo']; ?>') center/cover no-repeat;
              filter: brightness(65%); z-index: -2;">
    </div>

    <!-- Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50" style="z-index: -1;"></div>

    <!-- Content -->
    <div class="d-flex flex-column justify-content-center align-items-center h-100 px-3">
        <h1 class="display-3 fw-bold mb-3"><?php echo $hotel['Name']; ?></h1>
        <p class="lead mb-4">Stay near <?php echo $hotel['Location']; ?></p>

        <h2 class="fw-bold mb-3 text-warning"> Rating:<?php echo $hotel['Rating']; ?></h2>


    </div>
</section>

<!-- Details Section -->
<section class="py-5">
    <div class="container">

        <!-- Description -->
        <div class="mb-4">
            <h4 class="fw-bold text-secondary mb-2">About this Hotel</h4>
            <p class="text-muted" style="line-height:1.7;"><?php echo $hotel['Description']; ?></p>
        </div>


        <!-- Location -->
        <?php if (!empty($hotel['Location'])): ?>
        <div class="mb-4">
            <h5 class="fw-bold text-secondary mb-2">Location:</h5>
            <p class="text-muted"><?php echo $hotel['Location']; ?></p>
        </div>
        <?php endif; ?>

        <!-- Rate Button -->
        <div class="text-end mt-5">
            <a href="rating.php?type=hotel&id=<?php echo $hotel['Hotel_id']; ?>"
                class="btn btn-warning btn-lg rounded-pill px-5 shadow">
                Rate This Hotel
            </a>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>
<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>