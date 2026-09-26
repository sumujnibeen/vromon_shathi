<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Contacts - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <?php include 'global_css.php'; ?>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <!-- Page Header -->
    <header class="py-5">
        <div class="container">
            <div class="bg-danger text-white text-center p-5 rounded shadow">
                <div class="mb-2">
                    <img src="images/logo_footer.png" alt="Vromon Sathi Logo" style="max-height: 60px;">
                </div>
                <h5 class="fw-bold mb-3">Vromon Sathi</h5>
                <hr class="border-light" style="opacity: 0.3;">
                <h1 class="fw-bold">Emergency Contacts</h1>
                <p class="lead mb-0">Quick help when you need it most</p>
            </div>
        </div>
    </header>

    <!-- Emergency Numbers -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <?php
                // Fetch all emergency contacts from database
                $stmt = $conn->prepare("SELECT * FROM emergency ORDER BY Emergency_id ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $name = $row['Name'];
                        $number = $row['Contact_number'];
                        $location = $row['Location'];
                        $photo = $row['Photo'];

                ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 border-danger">
                        <div class="card-body">
                            <img src="images/emergency/<?php echo $photo; ?>" alt=" <?php echo $name; ?>" class="
                                mb-3" style="height:60px;">
                            <h5 class="card-title text-<?php echo $color; ?> fw-bold"><?php echo $name; ?></h5>
                            <p class="card-text"><?php echo $location ? $location : 'Available Nationwide'; ?></p>
                            <a href="tel:<?php echo $number; ?>"
                                class="btn btn-outline-danger btn-lg rounded-pill px-4">
                                <?php echo $number; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p class='text-center text-danger'>No emergency contacts found.</p>";
                }

                $stmt->close();
                ?>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>