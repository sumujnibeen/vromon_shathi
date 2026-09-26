<?php

include 'navbar.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Destinations - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <?php include 'global_css.php'; ?>
</head>

<body>




    <!-- Page Header -->
    <header class="py-5">
        <div class="container">
            <div class="bg-success text-white text-center p-5 rounded shadow">
                <!-- Logo -->
                <div class="mb-2">
                    <img src="images/logo_footer.png" alt="Vromon Sathi Logo" style="max-height: 60px;">
                </div>

                <!--Name -->
                <h5 class="fw-bold mb-3">Vromon Sathi</h5>

                <!-- Divider -->
                <hr class="border-light" style="opacity: 0.3;">

                <h1 class="fw-bold">All Destinations</h1>
                <p class="lead mb-0">Explore every beautiful spot in Kishoreganj</p>

            </div>
        </div>
    </header>




    <!-- All Destinations -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">

                <?php

                $stmt = $conn->prepare("SELECT * FROM destination ORDER BY Rating DESC"); // রেটিং অনুযায়ী সর্ট করা 
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['Destination_id'];
                        $name = $row['Name'];
                        $des_crp = substr($row['Description'], 0, 80) . '...'; // ৮০ অক্ষর পর্যন্ত লিমিট করা
                        $photo = $row['Photo'];
                        $rating = $row['Rating'];

                ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo $photo; ?>" class="card-img-top" alt="<?php echo $name; ?>"
                            style="height:200px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $name; ?></h5>
                            <p class="card-text text-muted"><?php echo $des_crp; ?></p>

                            <!-- Rating -->
                            <div class="mb-2 ">
                                <?php
                                        echo " Rating: <small >$rating</small>";
                                        ?>
                            </div>

                            <!-- View More-->
                            <a href="destination_details.php?id=<?php echo $id; ?>"
                                class="btn btn-success w-100 rounded-pill">View More</a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p class='text-center'>No destinations found.</p>";
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