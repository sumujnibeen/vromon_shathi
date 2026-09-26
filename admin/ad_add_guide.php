<?php
include('db.php');
$conn = connect();

// Handle form submission
if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $language = trim($_POST['language']);
    $price = trim($_POST['price']);
    $rating = trim($_POST['rating']);
    $availability = isset($_POST['availability']) ? 1 : 0;
    $created_at = date('Y-m-d H:i:s');

    // Handle photo upload
    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "images/guides/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $photoPath = $targetFile;
        }
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO guide (Name, Phone, Email, Language, Price, Photo, Rating, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdsdi", $name, $phone, $email, $language, $price, $photoPath, $rating, $availability);

    if ($stmt->execute()) {
        $success = "✅ Guide added successfully!";
    } else {
        $error = "❌ Failed to add guide: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Guide</title>
    <style>
        body {
            background-color: #f5f7fa;
            font-family: "Poppins", sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #16a085;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 600;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }
        input[type="file"] {
            border: none;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn {
            display: block;
            width: 100%;
            background-color: #16a085;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #13856b;
        }
        .message {
            text-align: center;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧍‍♂️ Add New Guide</h2>

        <?php if (isset($success)): ?>
            <p class="message success"><?= $success ?></p>
        <?php elseif (isset($error)): ?>
            <p class="message error"><?= $error ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Guide Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Languages (e.g., Bangla, English)</label>
                <input type="text" name="language" required>
            </div>

            <div class="form-group">
                <label>Price (৳)</label>
                <input type="number" step="0.01" name="price" required>
            </div>

            <div class="form-group">
                <label>Photo</label>
                <input type="file" name="photo" accept="image/*">
            </div>

            <div class="form-group">
                <label>Rating</label>
                <input type="number" step="0.1" name="rating" min="0" max="5" required>
            </div>

            <div class="form-group">
                <label>Availability</label>
                <input type="checkbox" name="availability"> Available
            </div>

            <button type="submit" name="submit" class="btn">➕ Add Guide</button>
        </form>
    </div>
</body>
</html>
