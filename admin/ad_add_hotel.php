<?php
include('db.php');
$conn = connect();

$success = $error = "";

// Handle form submission
if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $rating = trim($_POST['rating']);
    $price = trim($_POST['price']);
    $rooms_available = trim($_POST['rooms_available']);
    $destination_id = trim($_POST['destination_id']);
    $created_at = date('Y-m-d H:i:s');

    // Handle photo upload
    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "images/hotels/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $photo = $targetFile;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO hotel (Name, Description, Location, Photo, Rating, Price, Rooms_available, Destination_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdiii", 
        $name, 
        $description, 
        $location, 
        $photo, 
        $rating, 
        $price, 
        $rooms_available, 
        $destination_id
    );

    if ($stmt->execute()) {
        $success = "✅ Hotel added successfully!";
    } else {
        $error = "❌ Failed to add hotel: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Hotel</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 35px 40px;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #075628;
            margin-bottom: 25px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-top: 15px;
            color: #333;
        }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        input[type="file"] {
            margin-top: 8px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            background: #0a6c2b;
            color: white;
            transition: 0.3s;
        }
        button:hover {
            background: #0c8336;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🏨 Add New Hotel</h2>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Hotel Name:</label>
            <input type="text" name="name" required>

            <label>Description:</label>
            <textarea name="description" rows="3" required></textarea>

            <label>Location:</label>
            <input type="text" name="location" required>

            <label>Rating (out of 5):</label>
            <input type="number" step="0.1" max="5" min="0" name="rating" required>

            <label>Price (৳):</label>
            <input type="number" step="0.01" name="price" required>

            <label>Rooms Available:</label>
            <input type="number" name="rooms_available" required>

            <label>Destination ID:</label>
            <input type="number" name="destination_id" required>

            <label>Hotel Photo:</label>
            <input type="file" name="photo" accept="image/*">

            <button type="submit" name="submit">Add Hotel</button>
        </form>
    </div>
</body>
</html>
