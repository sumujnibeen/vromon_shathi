<?php
// ধরে নিলাম আপনার connect() ফাংশনটি এই ফাইলে আছে (db_config.php)
include 'db_config.php';

// ফাংশনটি কল করে সংযোগকারী অবজেক্টটি $conn ভেরিয়েবলে সংরক্ষণ করা হলো
$conn = connect();

// যদি সংযোগ ব্যর্থ হয় (যা connect() ফাংশনের মধ্যে die() দ্বারা ধরা হয়, তবুও একটি চেক রাখা ভালো)
if (!$conn) {
    die("Database connection error after calling connect().");
}

// --- A. Total Bookings (booking টেবিলের মোট Row সংখ্যা)
$sql_bookings = "SELECT COUNT(booking_id) AS total_bookings FROM booking";
// $db-এর পরিবর্তে $conn ব্যবহার করা হলো
$result_bookings = $conn->query($sql_bookings);
// কোয়েরি ব্যর্থ হলে সেটির জন্য নিরাপত্তা চেক যোগ করা হলো
$stats = $result_bookings ? $result_bookings->fetch_assoc() : ['total_bookings' => 0];
$total_bookings = $stats['total_bookings'] ?? 0;

// --- B. Total Vehicles (vehicle টেবিলের মোট সংখ্যা)
$sql_vehicles = "SELECT COUNT(vehicle_id) AS total_vehicles FROM vehicle";
$result_vehicles = $conn->query($sql_vehicles);
$stats = $result_vehicles ? $result_vehicles->fetch_assoc() : ['total_vehicles' => 0];
$total_vehicles = $stats['total_vehicles'] ?? 0;

// --- C. Total Hotels (hotel টেবিলের মোট সংখ্যা)
$sql_hotels = "SELECT COUNT(hotel_id) AS total_hotels FROM hotel";
$result_hotels = $conn->query($sql_hotels);
$stats = $result_hotels ? $result_hotels->fetch_assoc() : ['total_hotels' => 0];
$total_hotels = $stats['total_hotels'] ?? 0;


// =========================================================
?>
<!DOCTYPE html>

<html lang="bn">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ভ্রমণ সাথী - Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
    /* ... আপনার সম্পূর্ণ CSS কোড এখানে ছিল ... */
    /*
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
        .sidebar { ... }
        ... অন্যান্য সমস্ত CSS ...
        */

    /* আপনার দেওয়া সম্পূর্ণ CSS কোডটি নিচে রাখলাম */
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

    /* Sidebar */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 260px;
        height: 100vh;
        background: linear-gradient(180deg, #0f4d2a 0%, #1a6d3f 100%);
        color: white;
        overflow-y: auto;
        transition: all 0.3s;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h2 {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 1.3rem;
    }

    .sidebar-menu {
        padding: 1rem 0;
    }

    .menu-item {
        padding: 0.9rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        color: rgba(255, 255, 255, 0.9);
        cursor: pointer;
        transition: all 0.3s;
        border-left: 3px solid transparent;
    }

    .menu-item:hover,
    .menu-item.active {
        background: rgba(255, 255, 255, 0.1);
        border-left-color: #ff6b35;
        color: white;
    }

    .menu-icon {
        font-size: 1.3rem;
        width: 25px;
    }

    /* Main Content */
    .main-content {
        margin-left: 260px;
        padding: 2rem;
        min-height: 100vh;
    }

    /* Top Bar */
    .top-bar {
        background: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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

    .search-box button:hover {
        background: #1a6d3f;
    }

    .admin-profile {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .notification-icon {
        position: relative;
        font-size: 1.5rem;
        cursor: pointer;
        color: #0f4d2a;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff6b35;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-info {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        cursor: pointer;
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

    /* Stats Cards */

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-info h3 {
        font-size: 2rem;
        color: #0f4d2a;
        margin-bottom: 0.3rem;
    }

    .stat-info p {
        color: #666;
        font-size: 0.95rem;
    }

    .stat-icon {
        font-size: 3rem;
        opacity: 0.3;
    }

    .stat-card.bookings .stat-icon {
        color: #0f4d2a;
    }

    .stat-card.vehicles .stat-icon {
        color: #ff6b35;
    }

    .stat-card.hotels .stat-icon {
        color: #3498db;
    }

    .stat-card.pending .stat-icon {
        color: #f39c12;
    }

    /* Content Section */

    .content-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-title {
        font-size: 1.5rem;
        color: #0f4d2a;
        font-weight: 600;
    }

    .btn {
        padding: 0.7rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: #0f4d2a;
        color: white;
    }

    .btn-primary:hover {
        background: #1a6d3f;
    }

    .btn-secondary {
        background: #e1e8ed;
        color: #333;
    }

    .btn-secondary:hover {
        background: #d1d8dd;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-warning {
        background: #f39c12;
        color: white;
    }

    .btn-success {
        background: #27ae60;
        color: white;
    }

    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: #f8f9fa;
    }

    .data-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #0f4d2a;
        border-bottom: 2px solid #e1e8ed;
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .action-btn {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s;
    }

    /* Charts Section */

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .chart-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .chart-placeholder {
        height: 250px;
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f4d2a;
        font-size: 3rem;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }


    .quick-action-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        /* Added for alignment */
        align-items: center;
        /* Added for alignment */
        gap: 12px;
        /* Added for alignment */
        background: #ffffff;
        padding: 15px 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: 0.3s ease;
    }

    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
    }


    .quick-action-icon {
        font-size: 28px;
        color: #28a745;
        transition: 0.3s;
    }

    .add-vehicle-link {
        font-size: 18px;
        font-weight: 600;
        color: #28a745;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .add-vehicle-link::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -3px;
        width: 0%;
        height: 3px;
        background: #28a745;
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .add-vehicle-link:hover::after {
        width: 100%;
    }

    .add-vehicle-link:hover {
        color: #1d7c34;
        letter-spacing: 0.5px;
    }


    .quick-action-icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    /* Filters */

    .filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .filter-group label {
        font-size: 0.85rem;
        color: #666;
    }

    .filter-group select,
    .filter-group input {
        padding: 0.6rem;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    /* Responsive */

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-260px);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .top-bar {
            flex-direction: column;
            gap: 1rem;
        }

        .search-box {
            max-width: 100%;
        }
    }

    .page-content {
        display: none;
    }

    .page-content.active {
        display: block;
    }

    /* Footer */

    footer {
        background: white;
        padding: 2rem;
        text-align: center;
        border-radius: 12px;
        margin-top: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    footer p {
        margin: 0.5rem 0;
        color: #666;
    }

    footer strong {
        color: #0f4d2a;
    }
    </style>

</head>

<body>

    <div class="sidebar">

        <div class="sidebar-header">

            <h2><img src="header logo.png" height="60 px"></h2>

            <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 0.3rem;">Admin Panel</p>

        </div>

        <div class="sidebar-menu">

            <div class="menu-item active" onclick="showPage('dashboard')">

                <span class="menu-icon">📊</span>

                <span>Dashboard</span>

            </div>

            <div class="menu-item">
                <a href="ad_booking.php" class="d-flex align-items-center text-white text-decoration-none"
                    style="gap: 1rem;">
                    <span class="me-2">📅</span>
                    <span>Total Bookings</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="ad_vhichel_booking.php" class="d-flex align-items-center text-white text-decoration-none"
                    style="gap: 1rem;">
                    <span class="me-2">🚗</span>
                    <span>Vehicle Management</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="ad_hotel_management.php" class="d-flex align-items-center text-white text-decoration-none"
                    style="gap: 1rem;">
                    <span class="me-2">🏨</span>
                    <span>Hotel Management</span>
                </a>
            </div>


            <div class="menu-item">
                <a href="ad_event.php" class="d-flex align-items-center text-white text-decoration-none"
                    style="gap: 1rem;">
                    <span class="me-2">🗓️</span>
                    <span>Event management</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="ad_profile.php" class="d-flex align-items-center text-white text-decoration-none"
                    style="gap: 1rem;">
                    <span class="me-2">👤</span>
                    <span>Profile</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="ad_destinition.php" class="d-flex align-items-center text-white text-decoration-none"
                    style="gap: 1rem;">
                    <span class="me-2"> 🗺️</span>
                    <span>Destination Management</span>
                </a>
            </div>


            <div class="menu-item" onclick="showPage('reports')">
                <span class="menu-icon">💰</span>
                <span>Revenue</span>
            </div>

            <div class="menu-item">
                <a href="ad_guid.php" class="d-flex align-items-center text-white text-decoration-none"
                    style="gap: 1rem;">
                    <span class="me-2">🧍‍♂️</span>
                    <span> Guide Management</span>
                </a>
            </div>
        </div>
        <div class="menu-item">
            <a href="ad_Announcement.php" class="d-flex align-items-center text-white text-decoration-none"
                style="gap: 1rem;">
                <span class="me-2"></span>
                <span> Announcement Management</span>
            </a>
        </div>

        <div class="menu-item">
            <a href="logout_manager.php" class="d-flex align-items-center text-white text-decoration-none"
                style="gap: 1rem;">
                <span class="me-2">🚪</span>
                <span> Logout</span>
            </a>
        </div>
    </div>
    </div>

    <div class="main-content">

        <div class="top-bar">

            <div class="search-box">

                <input type="text" placeholder="Search by name, hotel, destination or booking ID...">

                <button>Search</button>

            </div>

            <div class="admin-profile">

                <a href="ad_profile.php" style="text-decoration: none; color: inherit;">
                    <div class="profile-info">
                        <div class="profile-avatar">A</div>
                        <div>
                            <div style="font-weight: 600; color: #333;">Admin</div>
                            <div style="font-size: 0.85rem; color: #666;">Super Admin</div>
                        </div>
                    </div>
                </a>

            </div>

        </div>

        <div id="dashboard" class="page-content active">

            <div class="stats-grid">

                <div class="stat-card bookings">
                    <div class="stat-info">
                        <h3><?php echo number_format($total_bookings ?? 0); ?></h3>
                        <p>Total Bookings</p>

                    </div>
                    <div class="stat-icon">📅</div>
                </div>

                <div class="stat-card vehicles">
                    <div class="stat-info">
                        <h3><?php echo number_format($total_vehicles ?? 0); ?></h3>
                        <p>Vehicles</p>
                    </div>
                    <div class="stat-icon">🚗</div>
                </div>

                <div class="stat-card hotels">
                    <div class="stat-info">
                        <h3><?php echo number_format($total_hotels ?? 0); ?></h3>
                        <p>Hotels</p>

                    </div>
                    <div class="stat-icon">🏨</div>
                </div>

                <div class="stat-card pending">
                    <div class="stat-info">
                        <h3><?php echo number_format($total_pending ?? 0); ?></h3>
                        <p>Pending Applications</p>

                    </div>
                    <div class="stat-icon">⏳</div>
                </div>

            </div>
        </div>


        <div class="content-section">

            <div class="section-header">

                <h2 class="section-title">Quick Actions</h2>

            </div>

            <div class="quick-actions">

                <div class="quick-action-card">
                    <div class="quick-action-icon">🚙</div>
                    <a href="ad_add_vehicle.php" class="add-vehicle-link">Add Vehicle</a>
                </div>

                <div class="quick-action-card">
                    <div class="quick-action-icon">🏨</div>
                    <a href="ad_add_hotel.php" class="add-vehicle-link">Add hotel</a>
                </div>

                <div class="quick-action-card">
                    <div class="quick-action-icon">👤</div>
                    <a href="ad_add_user.php" class="add-vehicle-link">Add User</a>
                </div>

                <div class="quick-action-card">
                    <div class="quick-action-icon">👤</div>
                    <a href="ad_add_guide.php" class="add-vehicle-link">Add Guide</a>
                </div>

                <div class="quick-action-card">
                    <div class="quick-action-icon">🗓️</div>
                    <a href="ad_add_event.php" class="add-vehicle-link">Add Event</a>
                </div>

                <div class="quick-action-card">
                    <div class="quick-action-icon">🗺️</div>
                    <a href="ad_add_destination.php" class="add-vehicle-link">Add Destination</a>
                </div>

            </div>
        </div>




    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>

<?php
// Footer inclusion at the very end
include '../footer.php';
?>