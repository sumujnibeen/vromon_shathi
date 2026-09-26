<?php
// db_config.php ফাইলে আপনার connect() ফাংশন থাকতে হবে
include 'db_config.php'; 
$conn = connect();

// 1. ভেরিফিকেশন ও ID নেওয়া
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_destination.php'); // Destination Management Page-এ রিডাইরেক্ট
    exit();
}

$destination_id = $_GET['id'];
$destination_data = null;
$message = '';
$message_type = '';

// 2. ডেটা ফেচ করা: এডিটের জন্য বর্তমান ডেটা লোড করা
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare("SELECT * FROM destination WHERE Destination_id = ?");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $destination_data = $result->fetch_assoc();
    } else {
        $message = "Error: Destination not found!";
        $message_type = 'danger';
    }
    $stmt->close();
}

// 3. ডেটা আপডেট করা: ফর্ম সাবমিট হলে
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ফর্ম থেকে ডেটা স্যানিটাইজ করে নেওয়া
    $name = mysqli_real_escape_string($conn, $_POST['Name']);
    $description = mysqli_real_escape_string($conn, $_POST['Description']);
    // Location ফিল্ডে Google Maps iframe এর URL আছে, তাই এটি বড় এবং স্ট্রিং হিসেবে নেওয়া হলো
    $location = mysqli_real_escape_string($conn, $_POST['Location']); 
    $rating = (float)$_POST['Rating'];
    $category = mysqli_real_escape_string($conn, $_POST['Category']);

    // SQL UPDATE কোয়েরি (Photo বাদ দেওয়া হলো)
    $update_sql = "UPDATE destination SET 
                    Name = ?, 
                    Description = ?, 
                    Location = ?, 
                    Rating = ?, 
                    Category = ? 
                    WHERE Destination_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    // প্যারামিটারের ধরন: s, s, s, d (double), s, i
    $stmt->bind_param("ssdsi", $name, $description, $location, $rating, $category, $destination_id);

    if ($stmt->execute()) {
        $message = "Destination updated successfully!";
        $message_type = 'success';
        // ৩ সেকেন্ড পর Destination Management Page-এ রিডাইরেক্ট
        header("Refresh:3; url=ad_destination.php"); 
        
        // পুনরায় ডেটা ফেচ করুন যাতে ফর্মে আপডেট হওয়া ডেটা দেখায়
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM destination WHERE Destination_id = ?");
        $stmt->bind_param("i", $destination_id);
        $stmt->execute();
        $destination_data = $stmt->get_result()->fetch_assoc();
        
    } else {
        $message = "Error updating destination: " . $conn->error;
        $message_type = 'danger';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Edit Destination - <?php echo htmlspecialchars($destination_data['Name'] ?? 'Loading...'); ?></title>
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
    <h2 class="mb-4 text-center" style="color: #0f4d2a;">🗺️ Edit Destination Details</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($destination_data): ?>
        <form method="POST" action="ad_edit_destination.php?id=<?php echo $destination_id; ?>">
            
            <div class="mb-3 form-group">
                <label for="Name" class="form-label">Destination Name</label>
                <input type="text" class="form-control" id="Name" name="Name" value="<?php echo htmlspecialchars($destination_data['Name']); ?>" required>
            </div>
            
            <div class="mb-3 form-group">
                <label for="Description" class="form-label">Description</label>
                <textarea class="form-control" id="Description" name="Description" rows="3" required><?php echo htmlspecialchars($destination_data['Description']); ?></textarea>
            </div>

            <div class="mb-3 form-group">
                <label for="Location" class="form-label">Location (Google Maps Iframe URL)</label>
                <textarea class="form-control" id="Location" name="Location" rows="3" required><?php echo htmlspecialchars($destination_data['Location']); ?></textarea>
                <small class="form-text text-muted">Paste the full iframe code or URL here.</small>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="Category" class="form-label">Category</label>
                    <input type="text" class="form-control" id="Category" name="Category" value="<?php echo htmlspecialchars($destination_data['Category']); ?>" required>
                    <small class="form-text text-muted">e.g., Natural Beauty, Historical Place, Religious Place</small>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="Rating" class="form-label">Rating (1.0 - 5.0)</label>
                    <input type="number" step="0.1" class="form-control" id="Rating" name="Rating" value="<?php echo htmlspecialchars($destination_data['Rating']); ?>" required min="1.0" max="5.0">
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Update Destination</button>
                <a href="ad_destination.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Could not retrieve destination data.
        </div>
        <div class="d-grid">
            <a href="ad_destination.php" class="btn btn-secondary">Go Back</a>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>