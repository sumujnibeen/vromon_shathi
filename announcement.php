<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Vromon Sathi</title>
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
                <div class="mb-2">
                    <img src="images/logo_footer.png" alt="Vromon Sathi Logo" style="max-height: 60px;">
                </div>
                <h5 class="fw-bold mb-3">Vromon Sathi</h5>
                <hr class="border-light" style="opacity: 0.3;">
                <h1 class="fw-bold">Announcements</h1>
                <p class="lead mb-0">Stay updated with the latest news and alerts</p>
            </div>
        </div>
    </header>

    <!-- Announcements Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <?php
        // Fetch all announcements
        $stmt = $conn->prepare("SELECT * FROM announcement ORDER BY Start_date DESC");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $id = $row['Announcement_id'];
            $title = $row['Title'];
            $message = $row['Message'];
            $type = $row['Type'];
            $color = ($type == 'alert') ? 'danger' : 'success';
        ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-<?php echo $color; ?>">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-<?php echo $color; ?>">
                                <?php echo $title; ?></h5>
                            <p class="card-text text-muted"><?php echo $message; ?></p>
                            <!-- Pass Announcement_id via GET to announcement_details.php -->
                            <a href="announcement_details.php?id=<?php echo $id; ?>"
                                class="btn btn-outline-<?php echo $color; ?> btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php
          }
        } else {
          echo "<p class='text-center text-muted'>No announcements available at the moment.</p>";
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