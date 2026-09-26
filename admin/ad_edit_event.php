<?php
// db_config.php ফাইলে আপনার connect() ফাংশন থাকতে হবে
include 'db_config.php'; 
$conn = connect();

// 1. ভেরিফিকেশন ও ID নেওয়া
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_event.php'); // Event Management Page-এ রিডাইরেক্ট
    exit();
}

$event_id = $_GET['id'];
$event_data = null;
$message = '';
$message_type = '';

// 2. ডেটা ফেচ করা: এডিটের জন্য বর্তমান ডেটা লোড করা
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare("SELECT * FROM event WHERE Event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $event_data = $result->fetch_assoc();
    } else {
        $message = "Error: Event not found!";
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
    $start_date = mysqli_real_escape_string($conn, $_POST['Start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['End_date']);
    $destination_id = (int)$_POST['Destination_id'];
    $ticket_price = (float)$_POST['Ticket_price'];
    $seats_available = (int)$_POST['Seats_available'];
    $rating = (float)$_POST['Rating'];
    $availability = (int)$_POST['availability'];

    // SQL UPDATE কোয়েরি (Photo এবং Created_at বাদ দেওয়া হলো)
    $update_sql = "UPDATE event SET 
                    Name = ?, 
                    Description = ?, 
                    Location = ?, 
                    Start_date = ?, 
                    End_date = ?, 
                    Destination_id = ?, 
                    Ticket_price = ?, 
                    Seats_available = ?, 
                    Rating = ?, 
                    availability = ? 
                    WHERE Event_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    // প্যারামিটারের ধরন: s, s, s, s, s, i, d (double), i, d (double), i, i
    $stmt->bind_param("sssssididii", 
                      $name, $description, $location, $start_date, $end_date, 
                      $destination_id, $ticket_price, $seats_available, 
                      $rating, $availability, $event_id);

    if ($stmt->execute()) {
        $message = "Event updated successfully!";
        $message_type = 'success';
        // ৩ সেকেন্ড পর Event Management Page-এ রিডাইরেক্ট
        header("Refresh:3; url=ad_event.php"); 
        
        // পুনরায় ডেটা ফেচ করুন যাতে ফর্মে আপডেট হওয়া ডেটা দেখায়
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM event WHERE Event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $event_data = $stmt->get_result()->fetch_assoc();
        
    } else {
        $message = "Error updating event: " . $conn->error;
        $message_type = 'danger';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Edit Event - <?php echo htmlspecialchars($event_data['Name'] ?? 'Loading...'); ?></title>
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
    <h2 class="mb-4 text-center" style="color: #0f4d2a;">🗓️ Edit Event Details</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($event_data): ?>
        <form method="POST" action="ad_edit_event.php?id=<?php echo $event_id; ?>">
            
            <div class="mb-3 form-group">
                <label for="Name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="Name" name="Name" value="<?php echo htmlspecialchars($event_data['Name']); ?>" required>
            </div>
            
            <div class="mb-3 form-group">
                <label for="Description" class="form-label">Description</label>
                <textarea class="form-control" id="Description" name="Description" rows="3" required><?php echo htmlspecialchars($event_data['Description']); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="Location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="Location" name="Location" value="<?php echo htmlspecialchars($event_data['Location']); ?>" required>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="Destination_id" class="form-label">Destination ID</label>
                    <input type="number" class="form-control" id="Destination_id" name="Destination_id" value="<?php echo htmlspecialchars($event_data['Destination_id']); ?>" required min="1">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3 form-group">
                    <label for="Start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="Start_date" name="Start_date" value="<?php echo htmlspecialchars(substr($event_data['Start_date'], 0, 10)); ?>" required>
                </div>
                <div class="col-md-6 mb-3 form-group">
                    <label for="End_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="End_date" name="End_date" value="<?php echo htmlspecialchars(substr($event_data['End_date'], 0, 10)); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3 form-group">
                    <label for="Ticket_price" class="form-label">Ticket Price (৳)</label>
                    <input type="number" step="0.01" class="form-control" id="Ticket_price" name="Ticket_price" value="<?php echo htmlspecialchars($event_data['Ticket_price']); ?>" required min="0">
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label for="Seats_available" class="form-label">Seats Available</label>
                    <input type="number" class="form-control" id="Seats_available" name="Seats_available" value="<?php echo htmlspecialchars($event_data['Seats_available']); ?>" required min="0">
                </div>
                <div class="col-md-4 mb-3 form-group">
                    <label for="Rating" class="form-label">Rating (1.0 - 5.0)</label>
                    <input type="number" step="0.1" class="form-control" id="Rating" name="Rating" value="<?php echo htmlspecialchars($event_data['Rating']); ?>" required min="1.0" max="5.0">
                </div>
            </div>
            
            <div class="mb-3 form-group">
                <label for="availability" class="form-label">Availability (1=Available, 0=Sold Out)</label>
                <select class="form-select" id="availability" name="availability" required>
                    <option value="1" <?php echo ($event_data['availability'] == 1) ? 'selected' : ''; ?>>1 - Available</option>
                    <option value="0" <?php echo ($event_data['availability'] == 0) ? 'selected' : ''; ?>>0 - Sold Out</option>
                </select>
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Update Event</button>
                <a href="ad_event.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            Could not retrieve event data.
        </div>
        <div class="d-grid">
            <a href="ad_event.php" class="btn btn-secondary">Go Back</a>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>