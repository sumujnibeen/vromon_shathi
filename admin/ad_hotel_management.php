<?php
include 'db.php';
$conn = connect();

// Get statistics
$sql_total = "SELECT COUNT(*) as total FROM hotel";
$result_total = $conn->query($sql_total);
$total_hotels = ($result_total && $result_total->num_rows > 0) ? $result_total->fetch_assoc()['total'] : 0;

$sql_available = "SELECT SUM(Rooms_available) as available FROM hotel";
$result_available = $conn->query($sql_available);
$available_rooms = ($result_available && $result_available->num_rows > 0) ? $result_available->fetch_assoc()['available'] : 0;
if ($available_rooms === null) $available_rooms = 0;

// Count fully booked hotels (rooms = 0)
$sql_booked = "SELECT COUNT(*) as booked FROM hotel WHERE Rooms_available = 0";
$result_booked = $conn->query($sql_booked);
$fully_booked = ($result_booked && $result_booked->num_rows > 0) ? $result_booked->fetch_assoc()['booked'] : 0;

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hotel Management Dashboard</title>
<style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      background-color: #f5f7fa;
      color: #333;
    }

    .main-content {
      padding: 20px;
      max-width: 1400px;
      margin: 0 auto;
    }

    .top-bar {
      background-color: #fff;
      padding: 15px 25px;
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      margin-bottom: 20px;
    }

    .search-box {
      display: flex;
      gap: 10px;
    }

    .search-box input {
      padding: 10px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      width: 300px;
      font-size: 0.95rem;
      transition: border 0.3s;
    }

    .search-box input:focus {
      outline: none;
      border-color: #0b3d2e;
    }

    .search-box button {
      background-color: #0b3d2e;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: background 0.3s;
    }

    .search-box button:hover {
      background-color: #0a5240;
    }

    .admin-profile {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .profile-avatar {
      background: linear-gradient(135deg, #0b3d2e, #0a5240);
      color: #fff;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 1.1rem;
    }

    .page-header {
      background-color: #fff;
      padding: 20px 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      margin-bottom: 20px;
    }

    .page-title {
      color: #0b3d2e;
      font-size: 1.8rem;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .breadcrumb {
      color: #666;
      font-size: 0.9rem;
    }

    .breadcrumb a {
      color: #0b3d2e;
      text-decoration: none;
    }

    .breadcrumb a:hover {
      text-decoration: underline;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
    }

    .stat-card {
      background: linear-gradient(135deg, #fff, #f8f9fa);
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: transform 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .stat-info h3 {
      font-size: 2rem;
      color: #0b3d2e;
      margin-bottom: 5px;
    }

    .stat-info p {
      color: #666;
      font-size: 0.95rem;
    }

    .stat-icon {
      font-size: 2.5rem;
      opacity: 0.7;
    }

    .vehicle-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }

    .room-card {
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .room-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .vehicle-image {
      position: relative;
      height: 200px;
      background: linear-gradient(135deg, #0b3d2e, #0a5240);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .vehicle-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .vehicle-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      backdrop-filter: blur(10px);
    }

    .badge-available {
      background-color: rgba(139, 195, 74, 0.95);
      color: #fff;
    }

    .badge-booked {
      background-color: rgba(255, 183, 3, 0.95);
      color: #000;
    }

    .vehicle-info {
      padding: 20px;
    }

    .vehicle-name {
      font-size: 1.3rem;
      color: #0b3d2e;
      margin-bottom: 12px;
    }

    .vehicle-details {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 12px;
    }

    .detail-item {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #555;
      font-size: 0.9rem;
    }

    .detail-icon {
      font-size: 1.1rem;
    }

    .vehicle-description {
      color: #666;
      font-size: 0.88rem;
      line-height: 1.5;
      margin-bottom: 15px;
      min-height: 60px;
    }

    .vehicle-price {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0b3d2e;
      margin-bottom: 15px;
    }

    .vehicle-price span {
      font-size: 0.9rem;
      font-weight: 400;
      color: #666;
    }

    .vehicle-actions {
      display: flex;
      gap: 8px;
    }

    .action-btn {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      font-size: 0.9rem;
      transition: all 0.3s;
    }

    .btn-info {
      background-color: #17a2b8;
      color: #fff;
    }

    .btn-info:hover {
      background-color: #138496;
    }

    .btn-primary {
      background-color: #0288d1;
      color: #fff;
    }

    .btn-primary:hover {
      background-color: #0277bd;
    }

    .btn-danger {
      background-color: #dc3545;
      color: #fff;
    }

    .btn-danger:hover {
      background-color: #c82333;
    }

    .new_car_add {
      text-align: center;
      padding: 20px;
    }

    .new_car_add .btn {
      background: linear-gradient(135deg, #0b3d2e, #0a5240);
      color: #fff;
      border: none;
      padding: 15px 35px;
      border-radius: 10px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(11, 61, 46, 0.3);
      transition: all 0.3s;
    }

    .new_car_add .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(11, 61, 46, 0.4);
    }

    .no-hotels {
      grid-column: 1/-1;
      text-align: center;
      padding: 3rem;
      color: #666;
    }

    .no-hotels h3 {
      margin-bottom: 10px;
    }

    @media (max-width: 768px) {
      .top-bar {
        flex-direction: column;
        gap: 15px;
      }

      .search-box {
        width: 100%;
      }

      .search-box input {
        width: 100%;
      }

      .vehicle-grid {
        grid-template-columns: 1fr;
      }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

<div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search hotels..." value="<?php echo htmlspecialchars($search); ?>">
            <button onclick="searchHotels()">Search</button>
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
            <span>🏨</span>
            Hotel Management
        </h1>
        <div class="breadcrumb">
            <a href="dashboard.php">Home</a> / <span>Hotels</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $total_hotels; ?></h3>
                <p>Total Hotels</p>
            </div>
            <div class="stat-icon">🏨</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $available_rooms; ?></h3>
                <p>Rooms Available</p>
            </div>
            <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $fully_booked; ?></h3>
                <p>Fully Booked</p>
            </div>
            <div class="stat-icon">📅</div>
        </div>
    </div>

    <!-- Hotel Grid -->
    <div class="vehicle-grid">
<?php
// Fetch hotels with optional search
if (!empty($search)) {
    $search_param = '%' . $conn->real_escape_string($search) . '%';
    $sql = "SELECT * FROM hotel WHERE Name LIKE ? OR Location LIKE ? OR Description LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM hotel";
    $result = $conn->query($sql);
}

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status = ($row['Rooms_available'] > 0) ? 'Available' : 'Fully Booked';
        $statusClass = ($row['Rooms_available'] > 0) ? 'badge-available' : 'badge-booked';
?>
        <div class="room-card">
            <div class="vehicle-image">
                <?php if(!empty($row['Photo'])): ?>
                    <img src="<?php echo htmlspecialchars($row['Photo']); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>">
                <?php else: ?>
                    <span style="font-size: 4rem; color: #fff;">🏨</span>
                <?php endif; ?>
                <span class="vehicle-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span>
            </div>
            <div class="vehicle-info">
                <h3 class="vehicle-name"><?php echo htmlspecialchars($row['Name']); ?></h3>
                
                <div class="vehicle-details">
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span><?php echo htmlspecialchars($row['Location']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🛏️</span>
                        <span>Rooms: <?php echo $row['Rooms_available']; ?></span>
                    </div>
                </div>

                <div class="vehicle-description">
                    <?php echo htmlspecialchars(substr($row['Description'], 0, 100)); ?><?php echo strlen($row['Description']) > 100 ? '...' : ''; ?>
                </div>
                
                <div class="vehicle-price">
                    ৳<?php echo number_format($row['Price'], 2); ?> <span>/ night</span>
                </div>
                
                <div class="vehicle-actions">
                    <button class="action-btn btn-info" onclick="viewHotel(<?php echo $row['Hotel_id']; ?>)">View</button>
                    <button class="action-btn success" ><a href="ad_edit_hotel.php?id=<?php echo $row['Hotel_id']; ?>" class="action-btn btn-warning">Edit</a></button>
                    <button class="action-btn btn-danger" onclick="deleteHotel(<?php echo $row['Hotel_id']; ?>)">Delete</button>
                </div>
            </div>
        </div>
<?php
    }
} else {
    echo '<div class="no-hotels">';
    echo '<h3>No hotels found</h3>';
    if (!empty($search)) {
        echo '<p>No results for "' . htmlspecialchars($search) . '". Try a different search term.</p>';
    } else {
        echo '<p>Add your first hotel to get started!</p>';
    }
    echo '</div>';
}

$conn->close();
?>
    </div>

    <!-- Add New Hotel Button -->
    <div class="new_car_add">
        <button class="btn btn-primary" onclick="openAddModal()">
            ➕ Add New Hotel
        </button>
    </div>
</div>

<script>
    function searchHotels() {
        const searchTerm = document.getElementById('searchInput').value;
        window.location.href = '<?php echo basename($_SERVER['PHP_SELF']); ?>?search=' + encodeURIComponent(searchTerm);
    }

    function viewHotel(id) {
        window.location.href = 'hotel_view.php?id=' + id;
    }

    function editHotel(id) {
        window.location.href = 'edit_hotel.php?id=' + id;
    }

    function deleteHotel(id) {
        if (confirm('Are you sure you want to delete this hotel?')) {
            window.location.href = 'delete_hotel.php?id=' + id;
        }
    }

    function openAddModal() {
        window.location.href = 'ad_add_hotel.php';
    }

    // Enable search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchHotels();
        }
    });
</script>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>