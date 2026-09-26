<?php
include('db.php');
$conn = connect();

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']); // optional
    $photo = trim($_POST['photo']); // URL or uploaded path
    $rating = trim($_POST['rating']);
    $category = trim($_POST['category']);
    $created_at = date('Y-m-d H:i:s');

    try {
        $stmt = $conn->prepare("INSERT INTO destination (Name, Description, Location, Photo, Rating, Category) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $name, $description, $location, $photo, $rating, $category);

        if ($stmt->execute()) {
            $success = "✅ Destination added successfully!";
        } else {
            $error = "❌ Failed to add destination: " . $conn->error;
        }

        $stmt->close();
    } catch (Exception $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Destination - Vromon Sathi</title>
<link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
<style>
body { background: #f5f7fa; font-family: "Poppins", sans-serif; }
.container { max-width: 600px; margin: 50px auto; }
.card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.btn { font-weight: 500; }
.message { font-weight: 600; text-align: center; margin-bottom: 15px; }
.success { color: #27ae60; }
.error { color: #e74c3c; }
</style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h2 class="text-center text-success mb-4">➕ Add New Destination</h2>

        <?php if ($success): ?>
            <p class="message success"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="message error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label>Location (optional, iframe or text)</label>
                <textarea name="location" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label>Photo URL</label>
                <input type="text" name="photo" class="form-control" placeholder="images/destinations/filename.jpg" required>
            </div>

            <div class="mb-3">
                <label>Rating</label>
                <input type="number" step="0.1" min="0" max="5" name="rating" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <input type="text" name="category" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100 rounded-pill">Add Destination</button>
        </form>
    </div>
</div>

<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
