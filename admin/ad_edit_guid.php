<?php
// db_config.php ফাইলে আপনার connect() ফাংশন থাকতে হবে
include 'db_config.php'; 
$conn = connect();

// 1. ভেরিফিকেশন ও ID নেওয়া
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_guide_management.php'); // Guide Management Page-এ রিডাইরেক্ট
    exit();
}

$guide_id = $_GET['id'];
$guide_data = null;
$message = '';
$message_type = '';

// 2. ডেটা ফেচ করা: এডিটের জন্য বর্তমান ডেটা লোড করা
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare("SELECT * FROM guide WHERE Guide_id = ?");
    $stmt->bind_param("i", $guide_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $guide_data = $result->fetch_assoc();
    } else {
        $message = "Error: Guide not found!";
        $message_type = 'danger';
    }
    $stmt->close();
}

// 3. ডেটা আপডেট করা: ফর্ম সাবমিট হলে
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ফর্ম থেকে ডেটা স্যানিটাইজ করে নেওয়া
    $name = mysqli_real_escape_string($conn, $_POST['Name']);
    $phone = mysqli_real_escape_string($conn, $_POST['Phone']);
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $language = mysqli_real_escape_string($conn, $_POST['Language']);
    $price = (float)$_POST['Price'];
    $rating = (float)$_POST['Rating'];
    $availability = (int)$_POST['availability'];

    // SQL UPDATE কোয়েরি (Photo বাদ দেওয়া হলো)
    $update_sql = "UPDATE guide SET 
                    Name = ?, 
                    Phone = ?, 
                    Email = ?, 
                    Language = ?, 
                    Price = ?, 
                    Rating = ?, 
                    availability = ? 
                    WHERE Guide_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    // প্যারামিটারের ধরন: s, s, s, s, d (double), d (double), i, i
    $stmt->bind_param("ssssddii", 
                      $name, $phone, $email, $language, $price, 
                      $rating, $availability, $guide_id);

    if ($stmt->execute()) {
        $message = "Guide updated successfully!";
        $message_type = 'success';
        // ৩ সেকেন্ড পর Guide Management Page-এ রিডাইরেক্ট
        header("Refresh:3; url=ad_guide_management.php"); 
        
        // পুনরায় ডেটা ফেচ করুন যাতে ফর্মে আপডেট হওয়া ডেটা দেখায়
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM guide WHERE Guide_id = ?");
        $stmt->bind_param("i", $guide_id);
        $stmt->execute();
        $guide_data = $stmt->get_result()->fetch_assoc();
        
    } else {
        $message = "Error updating guide: " . $conn->error;
        $message_type = 'danger';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Edit Guide - <?php echo htmlspecialchars($guide_data['Name'] ?? 'Loading...'); ?></title>
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
    <h2 class="mb-4 text-center" style="color: #0f4d2a;">🧍 Edit Guide Details</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($guide_data): ?>
        <form method="POST" action="ad_edit_guide.php?id=<?php echo $guide_id; ?>">
            
            <div class="mb-3 form-group">
                <label for="Name" class="form-label">Guide Name</label>
                <input type="text" class="form-control" id="Name" name="Name" value="<?php echo htmlspecialchars($guide_data['Name']); ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="Phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="Phone" name="Phone" value="<?php echo htmlspecialchars($guide_data['Phone']); ?>" required>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="Email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="Email" name="Email" value="<?php echo htmlspecialchars($guide_data['Email']); ?>" required>
                </div>
            </div>

            <div class="mb-3 form-group">
                <label for="Language" class="form-label">Languages Spoken</label>
                <input type="text" class="form-control" id="Language" name="Language" value="<?php echo htmlspecialchars($guide_data['Language']); ?>" required>
                <small class="form-text text-muted">Separate multiple languages with a comma (e.g., Bangla, English, Hindi)</small>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3 form-group">
                    <label for="Price" class="form-label">Price (Per Day/Hour) (৳)</label>
                    <input type="number" step="0.01" class="form-control" id="Price" name="Price" value="<?php echo htmlspecialchars($guide_data['Price']); ?>" required min="0">
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label for="Rating" class="form-label">Rating (1.0 - 5.0)</label>
                    <input type="number" step="0.1" class="form-control" id="Rating" name="Rating" value="<?php echo htmlspecialchars($guide_data['Rating']); ?>" required min="1.0" max="5.0">
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label for="availability" class="form-label">Availability (1=Yes, 0=No)</label>
                    <select class="form-select" id="availability" name="availability" required>
                        <option value="1" <?php echo ($guide_data['availability'] == 1) ? 'selected' : ''; ?>>1 - Available</option>
                        <option value="0" <?php echo ($guide_data['availability'] == 0) ? 'selected' : ''; ?>>0 - Not Available</option>
                    </select>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Update Guide</button>
                <a href="ad_guide_management.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Could not retrieve guide data.
        </div>
        <div class="d-grid">
            <a href="ad_guide_management.php" class="btn btn-secondary">Go Back</a>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>