<?php
include('calc_rev.php'); // Update guide ratings first
?>

<!-- Top Guides -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold text-center mb-4">Top Guides</h2>
        <div class="row g-4">
            <?php
      // Fetch top 3 guides by rating
      $stmt = $conn->prepare("SELECT * FROM guide ORDER BY Rating DESC LIMIT 3");
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $id = $row['Guide_id'];
          $name = $row['Name'];
          $photo = $row['Photo'];
          $rating = $row['Rating'];
          $language = $row['Language']; // Show guide languages
      ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo $photo; ?>" class="card-img-top" alt="<?php echo $name; ?>"
                        style="height:200px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $name; ?></h5>
                        <p class="card-text text-muted">Languages: <?php echo $language; ?></p>

                        <!-- Rating -->
                        <div class="mb-2">
                            <?php
                  echo " Rating: <small>$rating</small>";
                  ?>

                        </div>

                        <!-- View More Button -->
                        <a href="guide_details.php?id=<?php echo $id; ?>"
                            class="btn btn-success w-100 rounded-pill">View More</a>
                    </div>
                </div>
            </div>
            <?php
        }
      } else {
        echo "<p class='text-center'>No guides found.</p>";
      }
      $stmt->close();
      ?>
        </div>

        <!-- Button Row -->
        <div class="text-center mt-4">
            <a href="all_guides.php" class="btn btn-success px-4 rounded-pill shadow-sm">
                View All Guides
            </a>
        </div>
    </div>
</section>