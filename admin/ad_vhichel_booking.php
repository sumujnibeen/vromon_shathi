<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ভ্রমণ সাথী - Vehicle Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            color: #333;
        }

        .menu-item {
            padding: 0.9rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: rgba(255,255,255,0.9);
            cursor: pointer;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255,255,255,0.1);
            border-left-color: #ff6b35;
            color: white;
        }

        .menu-icon {
            font-size: 1.3rem;
            width: 25px;
        }

        .main-content {
           /* margin-left: 260px;*/
            padding: 2rem;
            min-height: 100vh;
        }

        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .search-box {
            display: flex;
            gap: 0.5rem;
            flex: 1;
            max-width: 500px;
        }

        .search-box input {
            flex: 1;
            padding: 0.7rem 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .search-box button {
            padding: 0.7rem 1.5rem;
            background: #0f4d2a;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f4d2a, #1a6d3f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .page-title {
            font-size: 2rem;
            color: #0f4d2a;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .breadcrumb {
            color: #666;
            font-size: 0.95rem;
        }

        .breadcrumb a {
            color: #0f4d2a;
            text-decoration: none;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-info h3 {
            font-size: 2rem;
            color: #0f4d2a;
            margin-bottom: 0.3rem;
        }

        .stat-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        .vehicle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .vehicle-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #0f4d2a;
        }

        .vehicle-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #0f4d2a, #1a6d3f);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .vehicle-image img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .vehicle-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-available {
            background: #d4edda;
            color: #155724;
        }

        .badge-booked {
            background: #fff3cd;
            color: #856404;
        }

        .vehicle-info {
            padding: 1.5rem;
        }

        .vehicle-type {
            color: #ff6b35;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .vehicle-name {
            font-size: 1.3rem;
            color: #0f4d2a;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .vehicle-details {
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .detail-icon {
            color: #0f4d2a;
        }

        .vehicle-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .vehicle-price {
            font-size: 1.5rem;
            color: #0f4d2a;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .vehicle-price span {
            font-size: 0.9rem;
            color: #666;
            font-weight: 400;
        }

        .vehicle-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.6rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
            font-weight: 500;
            color: white;
        }

        .btn-info {
            background: #3498db;
        }

        .btn-info:hover {
            background: #2980b9;
        }

        .btn-primary {
            background: #0f4d2a;
        }

        .btn-primary:hover {
            background: #1a6d3f;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .new_car_add {
            text-align: center;
            padding: 2rem;
        }

        .btn {
            padding: 0.8rem 1.8rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-260px);
            }

            .main-content {
                margin-left: 0;
            }

            .vehicle-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php
include 'db.php';
$conn = connect();

// Get statistics
$sql_total = "SELECT COUNT(*) as total FROM vehicle";
$result_total = $conn->query($sql_total);
$total_vehicles = ($result_total && $result_total->num_rows > 0) ? $result_total->fetch_assoc()['total'] : 0;

$sql_available = "SELECT SUM(vehicle_availability_number) as available FROM vehicle";
$result_available = $conn->query($sql_available);
$available_now = ($result_available && $result_available->num_rows > 0) ? $result_available->fetch_assoc()['available'] : 0;
if ($available_now === null) $available_now = 0;

// Count booked vehicles
$sql_booked = "SELECT COUNT(DISTINCT vehicle_id) as booked FROM booking WHERE service_type = 'vehicle'";
$result_booked = $conn->query($sql_booked);
$currently_booked = ($result_booked && $result_booked->num_rows > 0) ? $result_booked->fetch_assoc()['booked'] : 0;
?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search vehicles...">
                <button onclick="searchVehicles()">Search</button>
            </div>
            <div class="admin-profile">
                <div class="profile-avatar">A</div>
                <div>
                    <div style="font-weight: 600;">Admin</div>
                    <div style="font-size: 0.85rem; color: #666;">Super Admin</div>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <span>🚗</span>
                Vehicle Management
            </h1>
            <div class="breadcrumb">
                <a href="dashboard.php">Home</a> / <span>Vehicles</span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $total_vehicles; ?></h3>
                    <p>Total Vehicles</p>
                </div>
                <div class="stat-icon">🚙</div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $available_now; ?></h3>
                    <p>Available Now</p>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $currently_booked; ?></h3>
                    <p>Currently Booked</p>
                </div>
                <div class="stat-icon">📅</div>
            </div>
        </div>

        <!-- Vehicle Grid -->
        <div class="vehicle-grid">
<?php
// Fetch all vehicles
$sql = "SELECT 
            v.vehicle_id,
            v.vehicle_name,
            v.vehicle_type,
            v.vehicle_price,
            v.vehicle_image,
            v.vehicle_location,
            v.vehicle_description,
            v.vehicle_availability_number,
            CASE 
                WHEN b.vehicle_id IS NOT NULL THEN 'Booked'
                ELSE 'Available'
            END AS status
        FROM vehicle v
        LEFT JOIN booking b ON v.vehicle_id = b.vehicle_id AND b.service_type = 'vehicle'
        GROUP BY v.vehicle_id";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $statusClass = ($row['status'] == 'Available') ? 'badge-available' : 'badge-booked';
?>
            <div class="vehicle-card">
                <div class="vehicle-image">
                    <?php if(!empty($row['vehicle_image'])): ?>
                        <img src="<?php echo htmlspecialchars($row['vehicle_image']); ?>" alt="<?php echo htmlspecialchars($row['vehicle_image']); ?>">
                    <?php else: ?>
                        <span style="font-size: 4rem;">🚗</span>
                    <?php endif; ?>
                    <span class="vehicle-badge <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span>
                </div>
                <div class="vehicle-info">
                    <div class="vehicle-type"><?php echo htmlspecialchars($row['vehicle_type']); ?></div>
                    <h3 class="vehicle-name"><?php echo htmlspecialchars($row['vehicle_name']); ?></h3>
                    
                    <div class="vehicle-details">
                        <div class="detail-item">
                            <span class="detail-icon">📍</span>
                            <span><?php echo htmlspecialchars($row['vehicle_location']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">📦</span>
                            <span>Available: <?php echo $row['vehicle_availability_number']; ?> units</span>
                        </div>
                    </div>

                    <div class="vehicle-description">
                        <?php echo htmlspecialchars(substr($row['vehicle_description'], 0, 100)); ?><?php echo strlen($row['vehicle_description']) > 100 ? '...' : ''; ?>
                    </div>
                    
                    <div class="vehicle-price">
                        ৳<?php echo number_format($row['vehicle_price'], 2); ?> <span>/ day</span>
                    </div>
                    
                    <div class="vehicle-actions">
                        <button class="action-btn btn-info"><a href="vehicle_vew.php?id=<?php echo $row['vehicle_id']; ?>" class="action-btn btn-warning">View</a></button>
                        <button class="action-btn btn-primary"; ><a href="ad_edit_vehicle.php?id=<?php echo $row['vehicle_id']; ?>" class="action-btn btn-warning">Edit</a></button>
                        <button class="action-btn btn-danger" onclick="deleteVehicle(<?php echo $row['vehicle_id']; ?>)">Delete</button>
                    </div>
                </div>
            </div>
<?php
    }
} else {
    echo '<div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #666;">';
    echo '<h3>No vehicles found</h3>';
    echo '<p>Add your first vehicle to get started!</p>';
    echo '</div>';
}

$conn->close();
?>
        </div>

        <!-- Add New Vehicle Button -->
        <div class="new_car_add">
            <button class="btn btn-primary" onclick="openAddModal()">
                ➕ Add New Vehicle
            </button>
        </div>
    </div>

    <script>
        function searchVehicles() {
            const searchTerm = document.getElementById('searchInput').value;
            window.location.href = 'vehicle_management.php?search=' + encodeURIComponent(searchTerm);
        }

        function viewVehicle(id) {
            window.location.href = 'view_vehicle.php?id=' + id;
        }

        function editVehicle(id) {
            window.location.href = 'edit_vehicle.php?id=' + id;
        }

        function deleteVehicle(id) {
            if (confirm('Are you sure you want to delete this vehicle?')) {
                window.location.href = 'delete_vehicle.php?id=' + id;
            }
        }

        function openAddModal() {
            window.location.href = 'ad_add_vehicle.php';
        }
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
<?php 
   include 'footer.php';
?>