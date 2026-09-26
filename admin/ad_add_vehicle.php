<?php
include('db.php');
$conn = connect();

$success = $error = "";

// Handle form submission
if (isset($_POST['submit'])) {
    $Vehicle_name = trim($_POST['Vehicle_name']);
    $vehicle_type = trim($_POST['vehicle_type']);
    $Vehicle_price = trim($_POST['Vehicle_price']);
    $Vehicle_availability_number = trim($_POST['Vehicle_availability_number']);
    $vehicle_description = trim($_POST['vehicle_description']);
    $vehicle_location = trim($_POST['vehicle_location']);
    $vehicle_route = trim($_POST['vehicle_route']);
    $created_at = date('Y-m-d H:i:s');

    // Handle photo upload
    $Vehicle_img = null;
    if (!empty($_FILES['Vehicle_img']['name'])) {
        $targetDir = "images/vehicle/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["Vehicle_img"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["Vehicle_img"]["tmp_name"], $targetFile)) {
            $Vehicle_img = $targetFile;
        }
    }

    // Insert into database (corrected column names and bindings)
    $stmt = $conn->prepare("INSERT INTO vehicle (name, type, price, availability, photo, description, location, route, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsssss", 
        $Vehicle_name, 
        $vehicle_type, 
        $Vehicle_price, 
        $Vehicle_availability_number, 
        $Vehicle_img, 
        $vehicle_description, 
        $vehicle_location, 
        $vehicle_route, 
        $created_at
    );

    if ($stmt->execute()) {
        $success = "✅ Vehicle added successfully!";
    } else {
        $error = "❌ Failed to add vehicle: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Vehicle</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #075628ff;
            margin-bottom: 25px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-top: 15px;
            color: #333;
        }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        input[type="file"] {
            margin-top: 10px;
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
            background: #096b2bff;
            color: white;
            transition: 0.3s;
        }
        button:hover {
            background: #0a6c2bff;
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
        <h2>🚗 Add New Vehicle</h2>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Vehicle Name:</label>
            <input type="text" name="Vehicle_name" required>

            <label>Vehicle Type:</label>
            <select name="vehicle_type" required>
                <option value="">-- Select Type --</option>
                <option value="Car">Car</option>
                <option value="Bus">Bus</option>
                <option value="Bike">Bike</option>
                <option value="Van">Van</option>
            </select>

            <label>Price per Day ($):</label>
            <input type="number" step="0.01" name="Vehicle_price" required>

            <label>Available Units:</label>
            <input type="number" name="Vehicle_availability_number" required>

            <label>Vehicle Image:</label>
            <input type="file" name="Vehicle_img" accept="image/*">

            <label>Description:</label>
            <textarea name="vehicle_description" rows="3" required></textarea>

            <label>Location:</label>
            <input type="text" name="vehicle_location" placeholder="e.g., Dhaka, Bangladesh" required>

            <label>Route:</label>
            <input type="text" name="vehicle_route" placeholder="e.g., Dhaka to Cox’s Bazar" required>

            <button type="submit" name="submit">Add Vehicle</button>
        </form>
    </div>
</body>
</html>
