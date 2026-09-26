<?php
include 'db.php';
$conn = connect(); // MySQLi connection

// -------------------- GET STATISTICS --------------------

// Total events
$sql_total = "SELECT COUNT(*) as total FROM event";
$result_total = $conn->query($sql_total);
$total_events = ($result_total) ? $result_total->fetch_assoc()['total'] : 0;

// Total available seats
$sql_available = "SELECT SUM(Seats_available) as available FROM event";
$result_available = $conn->query($sql_available);
$available_seats = ($result_available) ? $result_available->fetch_assoc()['available'] : 0;
if ($available_seats === null) $available_seats = 0;

// Fully booked events (Seats_available = 0)
$sql_booked = "SELECT COUNT(*) as booked FROM event WHERE Seats_available = 0";
$result_booked = $conn->query($sql_booked);
$fully_booked = ($result_booked) ? $result_booked->fetch_assoc()['booked'] : 0;

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Management Dashboard</title>
<style>
/* -------------------- STYLES -------------------- */
* { margin:0; padding:0; box-sizing:border-box; font-family:Poppins,sans-serif; }
body { background:#f5f7fa; color:#333; }
.main-content { padding:20px; max-width:1400px; margin:0 auto; }
.top-bar { background:#fff; padding:15px 25px; border-radius:10px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.08); margin-bottom:20px; }
.search-box { display:flex; gap:10px; }
.search-box input { padding:10px 15px; border:2px solid #e0e0e0; border-radius:8px; width:300px; font-size:0.95rem; transition:border 0.3s; }
.search-box input:focus { outline:none; border-color:#0b3d2e; }
.search-box button { background:#0b3d2e; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:500; transition:background 0.3s; }
.search-box button:hover { background:#0a5240; }
.admin-profile { display:flex; align-items:center; gap:12px; }
.profile-avatar { background:linear-gradient(135deg,#0b3d2e,#0a5240); color:#fff; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:1.1rem; }
.page-header { background:#fff; padding:20px 25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); margin-bottom:20px; }
.page-title { color:#0b3d2e; font-size:1.8rem; margin-bottom:8px; display:flex; align-items:center; gap:10px; }
.breadcrumb { color:#666; font-size:0.9rem; }
.breadcrumb a { color:#0b3d2e; text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin-bottom:25px; }
.stat-card { background:linear-gradient(135deg,#fff,#f8f9fa); padding:25px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); display:flex; justify-content:space-between; align-items:center; transition:transform 0.3s; }
.stat-card:hover { transform:translateY(-5px); box-shadow:0 4px 12px rgba(0,0,0,0.12); }
.stat-info h3 { font-size:2rem; color:#0b3d2e; margin-bottom:5px; }
.stat-info p { color:#666; font-size:0.95rem; }
.stat-icon { font-size:2.5rem; opacity:0.7; }
.event-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:25px; margin-bottom:30px; }
.Seats-card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); transition:transform 0.3s,box-shadow 0.3s; }
.Seats-card:hover { transform:translateY(-8px); box-shadow:0 6px 20px rgba(0,0,0,0.15); }
.event-image { position:relative; height:200px; background:linear-gradient(135deg,#0b3d2e,#0a5240); display:flex; align-items:center; justify-content:center; }
.event-image img { width:100%; height:100%; object-fit:cover; }
.event-badge { position:absolute; top:12px; right:12px; padding:6px 14px; border-radius:20px; font-size:0.8rem; font-weight:600; backdrop-filter:blur(10px); }
.badge-available { background:rgba(139,195,74,0.95); color:#fff; }
.badge-booked { background:rgba(255,183,3,0.95); color:#000; }
.event-info { padding:20px; }
.event-name { font-size:1.3rem; color:#0b3d2e; margin-bottom:12px; }
.event-details { display:flex; flex-direction:column; gap:8px; margin-bottom:12px; }
.detail-item { display:flex; align-items:center; gap:8px; color:#555; font-size:0.9rem; }
.event-description { color:#666; font-size:0.88rem; line-height:1.5; margin-bottom:15px; min-height:60px; }
.Ticket-price { font-size:1.5rem; font-weight:700; color:#0b3d2e; margin-bottom:15px; }
.Ticket-price span { font-size:0.9rem; font-weight:400; color:#666; }
.event-actions { display:flex; gap:8px; }
.action-btn { flex:1; padding:10px; border:none; border-radius:8px; cursor:pointer; font-weight:500; font-size:0.9rem; transition:all 0.3s; }
.btn-info { background:#17a2b8; color:#fff; }
.btn-info:hover { background:#138496; }
.btn-primary { background:#0288d1; color:#fff; }
.btn-primary:hover { background:#0277bd; }
.btn-danger { background:#dc3545; color:#fff; }
.btn-danger:hover { background:#c82333; }
.new_event_add { text-align:center; padding:20px; }
.new_event_add .btn { background:linear-gradient(135deg,#0b3d2e,#0a5240); color:#fff; border:none; padding:15px 35px; border-radius:10px; cursor:pointer; font-size:1rem; font-weight:600; box-shadow:0 4px 12px rgba(11,61,46,0.3); transition:all 0.3s; }
.new_event_add .btn:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(11,61,46,0.4); }
.no-Seats { grid-column:1/-1; text-align:center; padding:3rem; color:#666; }
.no-Seats h3 { margin-bottom:10px; }
@media (max-width:768px) { .top-bar { flex-direction:column; gap:15px; } .search-box { width:100%; } .search-box input { width:100%; } }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>">
            <button onclick="searchEvent()">Search</button>
        </div>
        <div class="admin-profile">
            <div class="profile-avatar">A</div>
            <div>
                <div style="font-weight:600;">Admin</div>
                <div style="font-size:0.85rem; color:#666;">Super Admin</div>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">🗓️ Event Management</h1>
        <div class="breadcrumb">
            <a href="dashboard.php">Home</a> / <span>Events</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $total_events; ?></h3>
                <p>Total Events</p>
            </div>
            <div class="stat-icon">🗓️</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?php echo $available_seats; ?></h3>
                <p>Seats Available</p>
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

    <!-- Event Grid -->
    <div class="event-grid">
<?php
// Fetch events with optional search
if (!empty($search)) {
    $search_param = "%" . $conn->real_escape_string($search) . "%";
    $stmt = $conn->prepare("SELECT * FROM event WHERE Name LIKE ? OR Location LIKE ? OR Description LIKE ?");
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM event");
}

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status = ($row['Seats_available'] > 0) ? 'Available' : 'Fully Booked';
        $statusClass = ($row['Seats_available'] > 0) ? 'badge-available' : 'badge-booked';
?>
        <div class="Seats-card">
            <div class="event-image">
                <?php if(!empty($row['Photo'])): ?>
                    <img src="<?php echo htmlspecialchars($row['Photo']); ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>">
                <?php else: ?>
                    <span style="font-size:4rem; color:#fff;">🏨</span>
                <?php endif; ?>
                <span class="event-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span>
            </div>
            <div class="event-info">
                <h3 class="event-name"><?php echo htmlspecialchars($row['Name']); ?></h3>
                <div class="event-details">
                    <div class="detail-item">
                        <span class="detail-icon">📍</span>
                        <span><?php echo htmlspecialchars($row['Location']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon">🛏️</span>
                        <span>Seats: <?php echo $row['Seats_available']; ?></span>
                    </div>
                </div>
                <div class="event-description">
                    <?php echo htmlspecialchars(substr($row['Description'], 0, 100)); ?><?php echo strlen($row['Description'])>100?'...':''; ?>
                </div>
                <div class="Ticket-price">
                    ৳<?php echo number_format($row['Ticket_price'], 2); ?> <span>/ ticket</span>

                </div>
                <div class="event-actions">
                    <button class="action-btn btn-info" onclick="viewEvent(<?php echo $row['Event_id']; ?>)">View</button>
                    <button class="action-btn btn-success"><a href="ad_edit_event.php?id=<?php echo $row['Event_id']; ?>" class="action-btn btn-warning">Edit</a></button>
                    <button class="action-btn btn-danger" onclick="deleteEvent(<?php echo $row['Event_id']; ?>)">Delete</button>
                </div>
            </div>
        </div>
<?php
    }
} else {
    echo '<div class="no-Seats"><h3>No events found</h3>';
    if(!empty($search)){
        echo '<p>No results for "'.htmlspecialchars($search).'". Try another search.</p>';
    } else {
        echo '<p>Add your first event to get started!</p>';
    }
    echo '</div>';
}
$conn->close();
?>
    </div>

    <!-- Add New Event Button -->
    <div class="new_event_add">
        <button class="btn btn-primary" onclick="window.location.href='ad_add_event.php'">➕ Add New Event</button>
    </div>
</div>

<script>
function searchEvent() {
    const searchTerm = document.getElementById('searchInput').value;
    window.location.href = '<?php echo basename($_SERVER['PHP_SELF']); ?>?search=' + encodeURIComponent(searchTerm);
}

function viewEvent(id) {
    window.location.href = 'Event_view.php?id=' + id;
}

function editEvent(id) {
    window.location.href = 'edit_event.php?id=' + id;
}

function deleteEvent(id) {
    if(confirm('Are you sure you want to delete this event?')) {
        window.location.href = 'delete_event.php?id=' + id;
    }
}

// Enable Enter key search
document.getElementById('searchInput').addEventListener('keypress', function(e){
    if(e.key === 'Enter') searchEvent();
});
</script>
</body>
</html>
