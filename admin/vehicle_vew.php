<?php
include 'db_config.php'; 
$conn = connect();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_vhichel_booking.php');
    exit();
}

$vehicle_id = $_GET['id'];
$vehicle_data = null;

// ডেটাবেস থেকে Vehicle এর সকল তথ্য ফেচ করা
$stmt = $conn->prepare("SELECT * FROM vehicle WHERE vehicle_id = ?");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $vehicle_data = $result->fetch_assoc();
} else {
    $error_message = "Error: Vehicle not found!";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>View Vehicle - <?php echo htmlspecialchars($vehicle_data['vehicle_name'] ?? 'Not Found'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 800px; margin-top: 50px; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .vehicle-details h4 { color: #0f4d2a; margin-top: 15px; }
        .detail-row { border-bottom: 1px dashed #eee; padding: 8px 0; }
        .detail-label { font-weight: 600; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center">🚗 Vehicle Details: <?php echo htmlspecialchars($vehicle_data['vehicle_name'] ?? 'Not Found'); ?></h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif ($vehicle_data): ?>
        <div class="card p-3 vehicle-details">
            <div class="row">
                <div class="col-md-6 text-center mb-3">
                    <img src="<?php echo htmlspecialchars($vehicle_data['vehicle_image']); ?>" class="img-fluid rounded" alt="Vehicle Image" style="max-height: 200px;">
                </div>
                <div class="col-md-6">
                    <div class="detail-row"><span class="detail-label">ID:</span> <?php echo $vehicle_data['vehicle_id']; ?></div>
                    <div class="detail-row"><span class="detail-label">Type:</span> <?php echo htmlspecialchars($vehicle_data['vehicle_type']); ?></div>
                    <div class="detail-row"><span class="detail-label">Location:</span> <?php echo htmlspecialchars($vehicle_data['vehicle_location']); ?></div>
                    <div class="detail-row"><span class="detail-label">Route:</span> <?php echo htmlspecialchars($vehicle_data['vehicle_route']); ?></div>
                    <div class="detail-row"><span class="detail-label">Price:</span> ৳<?php echo number_format($vehicle_data['vehicle_price'], 2); ?> / দিন</div>
                    <div class="detail-row"><span class="detail-label">Available Units:</span> <?php echo $vehicle_data['vehicle_availability_number']; ?></div>
                    <div class="detail-row"><span class="detail-label">Created At:</span> <?php echo date('Y-m-d H:i', strtotime($vehicle_data['created_at'])); ?></div>
                </div>
            </div>
            
            <h4 class="mt-4">Description</h4>
            <p><?php echo nl2br(htmlspecialchars($vehicle_data['vehicle_description'])); ?></p>
        </div>
        
        <div class="d-grid gap-2 mt-4">
            <a href="ad_vhichel_booking.php" class="btn btn-secondary">Go Back to Vehicle List</a>
        </div>
    <?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>