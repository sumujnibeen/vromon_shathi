<!-- Top Hotels -->
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold text-center mb-4">Top Hotels</h2>

        <div class="row g-4 mb-4">
            <?php
            // Fetch top 3 hotels based on rating
            $stmt = $conn->prepare("SELECT * FROM hotel ORDER BY Rating DESC LIMIT 3");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['Hotel_id'];
                    $name = $row['Name'];
                    $desc = substr($row['Description'], 0, 80) . '...';
                    $photo = $row['Photo'];
                    $rating = $row['Rating'];
            ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo $photo; ?>" class="card-img-top" alt="<?php echo $name; ?>"
                        style="height:200px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $name; ?></h5>
                        <p class="card-text text-muted"><?php echo $desc; ?></p>

                        <!-- Rating -->
                        <div class="mb-2">
                            <?php
                                    echo " Rating: <small>$rating</small>";
                                    ?>
                        </div>

                        <!-- View More -->
                        <a href="hotel_details.php?id=<?php echo $id; ?>"
                            class="btn btn-success w-100 rounded-pill">View More</a>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<p class='text-center'>No hotels found.</p>";
            }

            $stmt->close();
            ?>
        </div>

        <!-- View All Hotels Button -->
        <div class="text-center">
            <a href="all_hotels.php" class="btn btn-success px-5 rounded-pill shadow-sm">
                View All Hotels
            </a>
        </div>
    </div>
</section>