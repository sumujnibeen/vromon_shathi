<?php
include 'db.php';
$conn = connect();

// Get statistics
$sql_total = "SELECT COUNT(*) as total FROM guide";
$result_total = $conn->query($sql_total);
$total_guides = ($result_total && $result_total->num_rows > 0) ? $result_total->fetch_assoc()['total'] : 0;

$sql_available = "SELECT COUNT(*) as available FROM guide WHERE availability = 1";
$result_available = $conn->query($sql_available);
$available_guides = ($result_available && $result_available->num_rows > 0) ? $result_available->fetch_assoc()['available'] : 0;

$sql_unavailable = "SELECT COUNT(*) as unavailable FROM guide WHERE availability = 0";
$result_unavailable = $conn->query($sql_unavailable);
$unavailable_guides = ($result_unavailable && $result_unavailable->num_rows > 0) ? $result_unavailable->fetch_assoc()['unavailable'] : 0;

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Guide Management Dashboard</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 20px; }
.main-content { padding: 20px;
      max-width: 1400px;
      margin: 0 auto;
      display: inline; }
.search-box { margin-bottom: 20px; }
.page-header {
    background: linear-gradient(135deg, #0b3d2e, #0a5240);
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    color: #fff;
    margin-bottom: 25px;
}

.page-header .page-title {
    font-size: 2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header .page-title span {
    font-size: 2.5rem; /* icon size */
}

.page-header .breadcrumb {
    margin-top: 8px;
    font-size: 0.95rem;
    color: #d1e0d1;
}

.page-header .breadcrumb a {
    color: #cfe3cf;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.page-header .breadcrumb a:hover {
    color: #fff;
    text-decoration: underline;
}

.page-header .breadcrumb span {
    color: #b8d1b8;
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
.search-box input { padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc; }
.search-box button { padding: 10px 15px; border-radius: 5px; background: #007bff; color: #fff; border: none; cursor: pointer; }
.stats { display: flex; gap: 20px; margin-bottom: 20px; }
.stat-card { background: #fff; padding: 20px; border-radius: 10px; flex: 1; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
.guide-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px,1fr)); gap: 20px; }
.guide-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
.guide-image { height: 200px; background: #ddd; display: flex; align-items: center; justify-content: center; }
.guide-image img { width: 100%; height: 100%; object-fit: cover; }
.guide-info { padding: 15px; }
.guide-info h3 { margin: 0 0 10px 0; }
.guide-info p { margin: 5px 0; }
.badge { padding: 5px 10px; border-radius: 20px; color: #fff; font-size: 0.8rem; }
.badge-available { background: #28a745; }
.badge-unavailable { background: #dc3545; }
.action-btn { padding: 8px 12px; margin-right: 5px; border: none; border-radius: 5px; cursor: pointer; color: #fff; }
.btn-view { background: #17a2b8; }
.btn-edit { background: #007bff; }
.btn-delete { background: #dc3545; }
.new_guide_add {
    text-align: center;
    padding: 30px 0; /* উপরের এবং নিচের স্পেসিং */
}

.new_guide_add .btn {
    background: linear-gradient(135deg, #0b3d2e, #0a5240); /* গ্রেডিয়েন্ট */
    color: #fff;
    border: none;
    padding: 15px 40px; /* বাটনের সাইজ */
    border-radius: 12px; /* রাউন্ড কর্নার */
    cursor: pointer;
    font-size: 1.1rem; /* ফন্ট সাইজ */
    font-weight: 600; /* ফন্ট ওয়েট */
    box-shadow: 0 4px 12px rgba(11, 61, 46, 0.3); /* হালকা শ্যাডো */
    transition: all 0.3s ease-in-out; /* হোভার এফেক্টের ট্রানজিশন */
}

.new_guide_add .btn:hover {
    transform: translateY(-4px) scale(1.05); /* হোভার এফেক্ট: হালকা উঠানো + বড় করা */
    box-shadow: 0 8px 20px rgba(11, 61, 46, 0.4); /* হোভার এফেক্টের শ্যাডো */
    background: linear-gradient(135deg, #0a5240, #0b3d2e); /* হালকা কালার পরিবর্তন */
}

</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

<div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search Guides..." value="<?php echo htmlspecialchars($search); ?>">
            <button onclick="searchGuides()">Search</button>
        </div>
        <div class="admin-profile">
            <div class="profile-avatar">A</div>
            <div>
                <div style="font-weight: 600;">Admin</div>
                <div style="font-size: 0.85rem; color: #666;">Super Admin</div>
            </div>
        </div>
    </div> <!-- end top-bar -->

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <span>🧍‍♂️</span>
            Guide Management
        </h1>
        <div class="breadcrumb">
            <a href="dashboard.php">Home</a> / <span>Guides</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats">
        <div class="stat-card">
            <h2><?= $total_guides ?></h2>
            <p>Total Guides</p>
        </div>
        <div class="stat-card">
            <h2><?= $available_guides ?></h2>
            <p>Available Guides</p>
        </div>
        <div class="stat-card">
            <h2><?= $unavailable_guides ?></h2>
            <p>Unavailable Guides</p>
        </div>
    </div>
</div> <!-- end main-content -->

<div class="guide-grid">
<?php
if (!empty($search)) {
    $search_param = '%' . $conn->real_escape_string($search) . '%';
    $sql = "SELECT * FROM guide WHERE Name LIKE ? OR Email LIKE ? OR Language LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM guide";
    $result = $conn->query($sql);
}

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status = ($row['availability'] == 1) ? 'Available' : 'Unavailable';
        $statusClass = ($row['availability'] == 1) ? 'badge-available' : 'badge-unavailable';
?>
    <div class="guide-card">
        <div class="guide-image">
            <?php if(!empty($row['Photo'])): ?>
                <img src="<?= htmlspecialchars($row['Photo']) ?>" alt="<?= htmlspecialchars($row['Name']) ?>">
            <?php else: ?>
                <span style="font-size: 4rem;">👤</span>
            <?php endif; ?>
            <span class="badge <?= $statusClass ?>"><?= $status ?></span>
        </div>
        <div class="guide-info">
            <h3><?= htmlspecialchars($row['Name']) ?></h3>
            <p>Phone: <?= htmlspecialchars($row['Phone']) ?></p>
            <p>Email: <?= htmlspecialchars($row['Email']) ?></p>
            <p>Language: <?= htmlspecialchars($row['Language']) ?></p>
            <p>Price: ৳<?= number_format($row['Price'],2) ?></p>
            <p>Rating: <?= $row['Rating'] ?></p>
            <div>
                <button class="action-btn btn-view" ><a href="guid_view.php?id=<?php echo $row['Guide_id'];?>" > View</button>
                <button class="action-btn btn-view"> <a href="ad_edit_guide.php?id=<?php echo $row['Guide_id']; ?>" >Edit</a></button> 
                <button class="action-btn btn-delete" onclick="deleteGuide(<?= $row['Guide_id'] ?>)">Delete</button>
            </div>
        </div>
    </div>
<?php
    }
} else {
    echo '<p>No guides found.</p>';
}

$conn->close();
?>
</div>
 </div>

    <!-- Add New guide Button -->
    <div class="new_guide_add">
        <button class="btn btn-primary" >
            <a href="ad_add_guide.php">add new guide</a> 
        </button>
    </div>
</div>

<script>
function searchGuide() {
    const searchTerm = document.getElementById('searchInput').value;
    window.location.href = '<?= basename($_SERVER['PHP_SELF']) ?>?search=' + encodeURIComponent(searchTerm);
}


function deleteGuide(id) {
    if(confirm('Are you sure you want to delete this guide?')) {
        window.location.href = 'delete_guide.php?id=' + id;
    }
}
</script>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
