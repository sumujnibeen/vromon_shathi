<?php
// db_config.php ফাইলে আপনার connect() ফাংশন থাকতে হবে
include 'db_config.php'; 
$conn = connect();

// 1. ভেরিফিকেশন ও ID নেওয়া
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_hotel_management.php'); // Hotel Management Page-এ রিডাইরেক্ট
    exit();
}

$hotel_id = $_GET['id'];
$hotel_data = null;
$message = '';
$message_type = '';

// 2. ডেটা ফেচ করা: এডিটের জন্য বর্তমান ডেটা লোড করা
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare("SELECT * FROM hotel WHERE Hotel_id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $hotel_data = $result->fetch_assoc();
    } else {
        $message = "Error: Hotel not found!";
        $message_type = 'danger';
    }
    $stmt->close();
}

// 3. ডেটা আপডেট করা: ফর্ম সাবমিট হলে
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ফর্ম থেকে ডেটা স্যানিটাইজ করে নেওয়া
    $name = mysqli_real_escape_string($conn, $_POST['Name']);
    $description = mysqli_real_escape_string($conn, $_POST['Description']);
    $location = mysqli_real_escape_string($conn, $_POST['Location']);
    $rating = (float)$_POST['Rating'];
    $price = (float)$_POST['Price'];
    $rooms_available = (int)$_POST['Rooms_available'];
    $destination_id = (int)$_POST['Destination_id']; // যদি গন্তব্য (destination) লিস্ট থেকে আসে

    // SQL UPDATE কোয়েরি (Photo এখানে আপডেট করা হয়নি)
    $update_sql = "UPDATE hotel SET 
                    Name = ?, 
                    Description = ?, 
                    Location = ?, 
                    Rating = ?, 
                    Price = ?, 
                    Rooms_available = ?, 
                    Destination_id = ? 
                    WHERE Hotel_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    // প্যারামিটারের ধরন: s, s, s, d (double), d (double), i (integer), i (integer), i (integer)
    $stmt->bind_param("sssdiiii", $name, $description, $location, $rating, $price, $rooms_available, $destination_id, $hotel_id);

    if ($stmt->execute()) {
        $message = "Hotel updated successfully!";
        $message_type = 'success';
        // ৩ সেকেন্ড পর Hotel Management Page-এ রিডাইরেক্ট
        header("Refresh:3; url=ad_hotel_management.php"); 
        
        // পুনরায় ডেটা ফেচ করুন যাতে ফর্মে আপডেট হওয়া ডেটা দেখায়
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM hotel WHERE Hotel_id = ?");
        $stmt->bind_param("i", $hotel_id);
        $stmt->execute();
        $hotel_data = $stmt->get_result()->fetch_assoc();
        
    } else {
        $message = "Error updating hotel: " . $conn->error;
        $message_type = 'danger';
    }
    // $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Edit Hotel - <?php echo htmlspecialchars($hotel_data['Name'] ?? 'Loading...'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 700px; margin-top: 50px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .form-group label { font-weight: 600; color: #0f4d2a; }
        .btn-primary { background: #0f4d2a; border-color: #0f4d2a; }
        .btn-primary:hover { background: #1a6d3f; border-color: #1a6d3f; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center" style="color: #0f4d2a;">🏨 Edit Hotel Details</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($hotel_data): ?>
        <form method="POST" action="ad_edit_hotel.php?id=<?php echo $hotel_id; ?>">
            
            <div class="mb-3 form-group">
                <label for="Name" class="form-label">Hotel Name</label>
                <input type="text" class="form-control" id="Name" name="Name" value="<?php echo htmlspecialchars($hotel_data['Name']); ?>" required>
            </div>
            
            <div class="mb-3 form-group">
                <label for="Description" class="form-label">Description</label>
                <textarea class="form-control" id="Description" name="Description" rows="3" required><?php echo htmlspecialchars($hotel_data['Description']); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="Location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="Location" name="Location" value="<?php echo htmlspecialchars($hotel_data['Location']); ?>" required>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="Destination_id" class="form-label">Destination ID</label>
                    <input type="number" class="form-control" id="Destination_id" name="Destination_id" value="<?php echo htmlspecialchars($hotel_data['Destination_id']); ?>" required min="1">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3 form-group">
                    <label for="Rooms_available" class="form-label">Rooms Available</label>
                    <input type="number" class="form-control" id="Rooms_available" name="Rooms_available" value="<?php echo htmlspecialchars($hotel_data['Rooms_available']); ?>" required min="0">
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label for="Price" class="form-label">Price (৳)</label>
                    <input type="number" step="0.01" class="form-control" id="Price" name="Price" value="<?php echo htmlspecialchars($hotel_data['Price']); ?>" required min="0">
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label for="Rating" class="form-label">Rating (1.0 - 5.0)</label>
                    <input type="number" step="0.1" class="form-control" id="Rating" name="Rating" value="<?php echo htmlspecialchars($hotel_data['Rating']); ?>" required min="1.0" max="5.0">
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Update Hotel</button>
                <a href="ad_hotel_management.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Could not retrieve hotel data.
        </div>
        <div class="d-grid">
            <a href="ad_hotel_management.php" class="btn btn-secondary">Go Back</a>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>