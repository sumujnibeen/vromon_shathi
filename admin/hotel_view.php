<?php
include 'db_config.php'; 
$conn = connect();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_hotel_management.php');
    exit();
}

$hotel_id = $_GET['id'];
$hotel_data = null;

$stmt = $conn->prepare("SELECT * FROM hotel WHERE Hotel_id = ?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $hotel_data = $result->fetch_assoc();
} else {
    $error_message = "Error: Hotel not found!";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>View Hotel - <?php echo htmlspecialchars($hotel_data['Name'] ?? 'Not Found'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 800px; margin-top: 50px; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .hotel-details h4 { color: #0f4d2a; margin-top: 15px; }
        .detail-row { border-bottom: 1px dashed #eee; padding: 8px 0; }
        .detail-label { font-weight: 600; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center">🏨 Hotel Details: <?php echo htmlspecialchars($hotel_data['Name'] ?? 'Not Found'); ?></h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif ($hotel_data): ?>
        <div class="card p-3 hotel-details">
            <div class="row">
                <div class="col-md-6 text-center mb-3">
                    <img src="<?php echo htmlspecialchars($hotel_data['Photo']); ?>" class="img-fluid rounded" alt="Hotel Photo" style="max-height: 200px;">
                </div>
                <div class="col-md-6">
                    <div class="detail-row"><span class="detail-label">ID:</span> <?php echo $hotel_data['Hotel_id']; ?></div>
                    <div class="detail-row"><span class="detail-label">Location:</span> <?php echo htmlspecialchars($hotel_data['Location']); ?></div>
                    <div class="detail-row"><span class="detail-label">Destination ID:</span> <?php echo $hotel_data['Destination_id']; ?></div>
                    <div class="detail-row"><span class="detail-label">Price:</span> ৳<?php echo number_format($hotel_data['Price'], 2); ?> / রাত</div>
                    <div class="detail-row"><span class="detail-label">Rooms Available:</span> <?php echo $hotel_data['Rooms_available']; ?></div>
                    <div class="detail-row"><span class="detail-label">Rating:</span> <?php echo $hotel_data['Rating']; ?> / 5.0</div>
                </div>
            </div>
            
            <h4 class="mt-4">Description</h4>
            <p><?php echo nl2br(htmlspecialchars($hotel_data['Description'])); ?></p>
        </div>
        
        <div class="d-grid gap-2 mt-4">
            <a href="ad_hotel_management.php" class="btn btn-secondary">Go Back to Hotel List</a>
        </div>
    <?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>