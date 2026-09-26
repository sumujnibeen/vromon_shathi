<?php
// db_config.php ফাইলে আপনার connect() ফাংশন থাকতে হবে
include 'db_config.php'; 
$conn = connect();

// 1. ভেরিফিকেশন ও ID নেওয়া
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_vhichel_booking.php'); // Vehicle Management Page-এ রিডাইরেক্ট
    exit();
}

$vehicle_id = $_GET['id'];
$vehicle_data = null;
$message = '';
$message_type = '';

// 2. ডেটা ফেচ করা: এডিটের জন্য বর্তমান ডেটা লোড করা
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // লক্ষ্য করুন কলাম নামগুলো আপনার তালিকা অনুযায়ী ব্যবহার করা হয়েছে
    $stmt = $conn->prepare("SELECT 
                                vehicle_id, vehicle_name, vehicle_type, vehicle_price, 
                                vehicle_availability_number, vehicle_image, vehicle_description, 
                                vehicle_location, vehicle_route 
                            FROM vehicle 
                            WHERE vehicle_id = ?");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $vehicle_data = $result->fetch_assoc();
    } else {
        $message = "Error: Vehicle not found!";
        $message_type = 'danger';
    }
    $stmt->close();
}

// 3. ডেটা আপডেট করা: ফর্ম সাবমিট হলে
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ফর্ম থেকে ডেটা স্যানিটাইজ করে নেওয়া
    $name = mysqli_real_escape_string($conn, $_POST['vehicle_name']);
    $type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $price = (float)$_POST['vehicle_price'];
    $availability = (int)$_POST['vehicle_availability_number'];
    // $image = mysqli_real_escape_string($conn, $_POST['vehicle_image']); // ইমেজ আপলোড অন্যভাবে হ্যান্ডেল করা ভালো
    $description = mysqli_real_escape_string($conn, $_POST['vehicle_description']);
    $location = mysqli_real_escape_string($conn, $_POST['vehicle_location']);
    $route = mysqli_real_escape_string($conn, $_POST['vehicle_route']);

    // SQL UPDATE কোয়েরি (vehicle_image এখানে আপডেট করা হয়নি, কারণ সেটি ফাইল আপলোডের মাধ্যমে হয়)
    $update_sql = "UPDATE vehicle SET 
                    vehicle_name = ?, 
                    vehicle_type = ?, 
                    vehicle_price = ?, 
                    vehicle_availability_number = ?, 
                    vehicle_description = ?, 
                    vehicle_location = ?, 
                    vehicle_route = ? 
                    WHERE vehicle_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    // প্যারামিটারের ধরন: s (string), s (string), d (double), i (integer), s, s, s, i (integer)
    $stmt->bind_param("ssdissi", $name, $type, $price, $availability, $description, $location, $route, $vehicle_id);

    if ($stmt->execute()) {
        $message = "Vehicle updated successfully!";
        $message_type = 'success';
        // ৩ সেকেন্ড পর Vehicle Management Page-এ রিডাইরেক্ট
        header("Refresh:3; url=ad_vhichel_booking.php"); 
        
        // পুনরায় ডেটা ফেচ করুন যাতে ফর্মে আপডেট হওয়া ডেটা দেখায়
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM vehicle WHERE vehicle_id = ?");
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();
        $vehicle_data = $stmt->get_result()->fetch_assoc();
        
    } else {
        $message = "Error updating vehicle: " . $conn->error;
        $message_type = 'danger';
    }
    // $stmt->close(); // যদি সফলভাবে আপডেট হয়, তবে উপরে close() করা হয়েছে
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Edit Vehicle - <?php echo htmlspecialchars($vehicle_data['vehicle_name'] ?? 'Loading...'); ?></title>
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
    <h2 class="mb-4 text-center" style="color: #0f4d2a;">🚗 Edit Vehicle Details</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($vehicle_data): ?>
        <form method="POST" action="ad_edit_vehicle.php?id=<?php echo $vehicle_id; ?>">
            
            <div class="mb-3 form-group">
                <label for="vehicle_name" class="form-label">Vehicle Name</label>
                <input type="text" class="form-control" id="vehicle_name" name="vehicle_name" value="<?php echo htmlspecialchars($vehicle_data['vehicle_name']); ?>" required>
            </div>
            
            <div class="mb-3 form-group">
                <label for="vehicle_type" class="form-label">Vehicle Type (e.g., Boat, Car, Bus)</label>
                <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" value="<?php echo htmlspecialchars($vehicle_data['vehicle_type']); ?>" required>
            </div>

            <div class="mb-3 form-group">
                <label for="vehicle_location" class="form-label">Location</label>
                <input type="text" class="form-control" id="vehicle_location" name="vehicle_location" value="<?php echo htmlspecialchars($vehicle_data['vehicle_location']); ?>" required>
            </div>
            
            <div class="mb-3 form-group">
                <label for="vehicle_route" class="form-label">Route</label>
                <input type="text" class="form-control" id="vehicle_route" name="vehicle_route" value="<?php echo htmlspecialchars($vehicle_data['vehicle_route']); ?>" required>
            </div>

            <div class="mb-3 form-group">
                <label for="vehicle_availability_number" class="form-label">Available Units</label>
                <input type="number" class="form-control" id="vehicle_availability_number" name="vehicle_availability_number" value="<?php echo htmlspecialchars($vehicle_data['vehicle_availability_number']); ?>" required min="0">
            </div>

            <div class="mb-3 form-group">
                <label for="vehicle_price" class="form-label">Price (৳)</label>
                <input type="number" step="0.01" class="form-control" id="vehicle_price" name="vehicle_price" value="<?php echo htmlspecialchars($vehicle_data['vehicle_price']); ?>" required min="0">
            </div>
            
            <div class="mb-3 form-group">
                <label for="vehicle_description" class="form-label">Description</label>
                <textarea class="form-control" id="vehicle_description" name="vehicle_description" rows="3" required><?php echo htmlspecialchars($vehicle_data['vehicle_description']); ?></textarea>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Update Vehicle</button>
                <a href="ad_vhichel_booking.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Could not retrieve vehicle data.
        </div>
        <div class="d-grid">
            <a href="ad_vhichel_booking.php" class="btn btn-secondary">Go Back</a>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>