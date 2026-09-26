<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement Details - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <?php include 'global_css.php'; ?>
</head>

<body>

    <?php include 'navbar.php'; ?>
    <?php include 'db.php'; ?>

    <?php
    // Get the announcement ID from GET
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        // Fetch the announcement
        $stmt = $conn->prepare("SELECT * FROM announcement WHERE Announcement_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $title = $row['Title'];
            $message = $row['Message'];
            $pdf = $row['PDF_File'];
            $type = $row['Type'];
            $link = $row['Link'];
            $start_date = $row['Start_date'];
            $end_date = $row['End_date'];
        } else {
            echo "<div class='container py-5'><p class='text-center text-danger'>Announcement not found.</p></div>";
            exit;
        }

        $stmt->close();
    } else {
        echo "<div class='container py-5'><p class='text-center text-danger'>Invalid announcement ID.</p></div>";
        exit;
    }
    ?>

    <!-- Announcement Details Section -->
    <section class="py-5">
        <div class="container">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h2 class="fw-bold text-success mb-3"><?php echo $title; ?></h2>
                    <p class="text-muted mb-3"><?php echo $message; ?></p>

                    <ul class="list-group mb-3">
                        <li class="list-group-item"><strong>Type:</strong> <?php echo $type; ?></li>
                        <li class="list-group-item"><strong>Start Date:</strong> <?php echo $start_date; ?></li>
                        <li class="list-group-item"><strong>End Date:</strong> <?php echo $end_date; ?></li>
                        <?php if (!empty($pdf)) { ?>
                        <li class="list-group-item"><strong>PDF:</strong> <a href="<?php echo $pdf; ?>" target="_blank"
                                class="btn btn-outline-success btn-sm">Download PDF</a></li>
                        <?php } ?>
                    </ul>

                    <a href="announcement.php" class="btn btn-success">Back to Announcements</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>