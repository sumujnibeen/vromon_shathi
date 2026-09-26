<?php
include 'db_config.php'; 
$conn = connect();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ad_guide_management.php');
    exit();
}

$guide_id = $_GET['id'];
$guide_data = null;

$stmt = $conn->prepare("SELECT * FROM guide WHERE Guide_id = ?");
$stmt->bind_param("i", $guide_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $guide_data = $result->fetch_assoc();
} else {
    $error_message = "Error: Guide not found!";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>View Guide - <?php echo htmlspecialchars($guide_data['Name'] ?? 'Not Found'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 800px; margin-top: 50px; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .guide-details h4 { color: #0f4d2a; margin-top: 15px; }
        .detail-row { border-bottom: 1px dashed #eee; padding: 8px 0; }
        .detail-label { font-weight: 600; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center">🧍 Guide Details: <?php echo htmlspecialchars($guide_data['Name'] ?? 'Not Found'); ?></h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif ($guide_data): ?>
        <div class="card p-3 guide-details">
            <div class="row">
                <div class="col-md-6 text-center mb-3">
                    <img src="<?php echo htmlspecialchars($guide_data['Photo']); ?>" class="img-fluid rounded" alt="Guide Photo" style="max-height: 200px;">
                </div>
                <div class="col-md-6">
                    <div class="detail-row"><span class="detail-label">ID:</span> <?php echo $guide_data['Guide_id']; ?></div>
                    <div class="detail-row"><span class="detail-label">Phone:</span> <?php echo htmlspecialchars($guide_data['Phone']); ?></div>
                    <div class="detail-row"><span class="detail-label">Email:</span> <?php echo htmlspecialchars($guide_data['Email']); ?></div>
                    <div class="detail-row"><span class="detail-label">Languages:</span> <?php echo htmlspecialchars($guide_data['Language']); ?></div>
                    <div class="detail-row"><span class="detail-label">Price:</span> ৳<?php echo number_format($guide_data['Price'], 2); ?></div>
                    <div class="detail-row"><span class="detail-label">Rating:</span> <?php echo $guide_data['Rating']; ?> / 5.0</div>
                    <div class="detail-row"><span class="detail-label">Status:</span> <?php echo ($guide_data['availability'] == 1) ? 'Available' : 'Not Available'; ?></div>
                </div>
            </div>
        </div>
        
        <div class="d-grid gap-2 mt-4">
            <a href="ad_guide_management.php" class="btn btn-secondary">Go Back to Guide List</a>
        </div>
    <?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>