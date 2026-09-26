<?php
include('db.php');
$conn = connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations - Vromon Sathi</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        body { background: #f5f7fa; font-family: "Poppins", sans-serif; }
        .card { border-radius: 15px; overflow: hidden; transition: transform 0.3s; }
        .card:hover { transform: scale(1.03); }
        .card-img-top { height: 200px; object-fit: cover; }
        .card-title { font-size: 1.2rem; font-weight: 600; }
        .card-text { font-size: 0.95rem; }
        .btn { font-weight: 500; }
        .rating span { font-size: 1rem; }
        .category-badge { font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold text-success">All Destinations</h1>
    <div class="row g-4">

        <?php
        $stmt = $conn->prepare("SELECT * FROM destination ORDER BY Rating DESC");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['Destination_id'];
                $name = $row['Name'];
                $description = substr($row['Description'], 0, 100) . '...';
                $photo = $row['Photo'];
                $rating = $row['Rating'];
                $category = $row['Category'];
        ?>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <img src="<?= $photo ?>" alt="<?= htmlspecialchars($name) ?>" class="card-img-top">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($name) ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars($description) ?></p>

                    <!-- Rating -->
                    <div class="rating mb-2">
                        <?php
                        $fullStars = floor($rating);
                        $halfStar = ($rating - $fullStars >= 0.5) ? 1 : 0;
                        for ($i = 0; $i < $fullStars; $i++) echo '<span class="text-warning">&#9733;</span>';
                        if ($halfStar) echo '<span class="text-warning">&#189;</span>';
                        $emptyStars = 5 - $fullStars - $halfStar;
                        for ($i = 0; $i < $emptyStars; $i++) echo '<span class="text-secondary">&#9734;</span>';
                        echo " <small>($rating)</small>";
                        ?>
                    </div>

                    <!-- Category -->
                    <span class="badge bg-info text-dark mb-3 category-badge"><?= htmlspecialchars($category) ?></span>

                    <!-- Buttons -->
                    <div class="mt-auto d-flex gap-2">
                        <a href="destination_details.php?id=<?= $id ?>" class="btn btn-success flex-fill rounded-pill">View More</a>
                       <a href="ad_edit_destination.php?id=<?php echo $row['Destination_id']; ?>" class="btn btn-success flex-fill rounded-pill">Edit</a>
                        <form method="POST" action="admin_delete_destination.php" class="flex-fill m-0 p-0">
                            <input type="hidden" name="destination_id" value="<?= $id ?>">
                            <button type="submit" class="btn btn-danger w-100 rounded-pill"
                                    onclick="return confirm('Are you sure you want to delete this destination?')">
                                Delete
                            </button>
                        </form>
                    </div>
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

<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
