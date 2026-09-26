<?php
// admin_profile.php

// Note: Assuming 'admin_auth.php' handles session_start() and initial login check.
include 'admin_auth.php'; 

// --- 🔑 Standardize Session Key ---
$session_key = 'user_id'; 

// --- Check if admin is logged in (Authentication logic from admin_auth.php should enforce this) ---
if (!isset($_SESSION[$session_key])) {
    header("Location: admin_login.php");
    exit();
}

include('db_config.php');

// Get user ID from session
$user_id = $_SESSION[$session_key];

// --- Database Connection ---
$db = connect();

// Fetch admin info from the 'user' table where Role is administrative
$stmt = $db->prepare("SELECT * FROM user WHERE User_id = ? AND Role IN ('admin', 'Super Admin', 'Manager')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc(); 
$stmt->close(); 

if (!$admin) {
    // Clear session and redirect if ID is invalid or role is not administrative
    session_unset();
    session_destroy();
    die("Account not authorized for admin access! Please login again. <a href='admin_login.php'>Login</a>");
}

// 🚫 --- Get statistics: ENTIRE STATS LOGIC REMOVED as per user request ---

// Format photo path 
$admin['Photo'] = !empty($admin['Photo']) ? $admin['Photo'] : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($admin['Name']) . '&backgroundColor=c0aede';

// --- Close DB Connection ---
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - ভ্রমণ সাথী</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS অংশ আগের মতোই থাকবে */
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

        /* Main Content */
        .main-content {
            padding: 2rem;
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .page-title {
            font-size: 1.8rem;
            color: #0f4d2a;
            font-weight: 600;
        }

        .admin-profile-info {
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

        .profile-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0f4d2a;
        }

        /* Profile Section */
        .profile-container {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 2rem;
        }

        .profile-photo-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #0f4d2a;
            box-shadow: 0 5px 15px rgba(15, 77, 42, 0.3);
        }

        .profile-details h2 {
            color: #0f4d2a;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .profile-badge {
            display: inline-block;
            background: linear-gradient(135deg, #0f4d2a, #1a6d3f);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .profile-meta {
            display: flex;
            gap: 2rem;
            color: #666;
            font-size: 0.95rem;
            flex-wrap: wrap;
        }

        .profile-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .profile-meta i {
            color: #0f4d2a;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #0f4d2a;
        }

        .info-card h4 {
            color: #0f4d2a;
            font-size: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e1e8ed;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        /* Stats Cards - Removed from HTML */

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: #0f4d2a;
            color: white;
        }

        .btn-primary:hover {
            background: #1a6d3f;
            transform: scale(1.05);
            color: white;
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

        .btn-danger:hover {
            background: #c0392b;
            color: white;
        }

        /* Recent Activity */
        .activity-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .activity-title {
            color: #0f4d2a;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-left: 3px solid #0f4d2a;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .activity-icon {
            font-size: 1.5rem;
            color: #0f4d2a;
        }

        .activity-content h5 {
            color: #333;
            font-size: 1rem;
            margin-bottom: 0.3rem;
        }

        .activity-time {
            color: #999;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
            }

            .main-content {
                padding: 1rem;
            }

            .top-bar {
                flex-direction: column;
                gap: 1rem;
            }

            .profile-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="main-content">
        <div class="top-bar">
            <h1 class="page-title">
                <i class="fas fa-user-shield"></i> Admin Profile
            </h1>
            <div class="admin-profile-info">
                <img src="<?php echo htmlspecialchars($admin['Photo']); ?>" 
                    alt="Admin" class="profile-avatar">
                <div>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($admin['Name']); ?></div>
                    <div style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($admin['Role'] ?? 'Admin'); ?></div>
                </div>
            </div>
        </div>
        
        <div class="profile-container">
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($admin['Photo']); ?>" 
                    alt="Admin Profile" class="profile-photo-large">
                <div class="profile-details">
                    <h2><?php echo htmlspecialchars($admin['Name']); ?></h2>
                    <span class="profile-badge">
                        <i class="fas fa-shield-alt"></i> <?php echo htmlspecialchars($admin['Role'] ?? 'Admin'); ?>
                    </span>
                    <div class="profile-meta">
                        <span>
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($admin['Email']); ?>
                        </span>
                        <span>
                            <i class="fas fa-phone"></i>
                            <?php echo htmlspecialchars($admin['Phone'] ?? 'N/A'); ?>
                        </span>
                        <span>
                            <i class="fas fa-calendar"></i>
                            Joined: <?php echo date('M d, Y', strtotime($admin['Created_at'])); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="ad_update_profile.php?id=<?php echo $user_id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="admin_change_password.php" class="btn btn-secondary">
                    <i class="fas fa-lock"></i> Change Password
                </a>
                <a href="admin_logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <hr style="margin-top: 2rem;"/>
            
            <div class="info-grid">
                <div class="info-card">
                    <h4><i class="fas fa-user"></i> Personal Information</h4>
                    <div class="info-item">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['Name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Username:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['Username'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value">
                            <?php echo $admin['Date_of_Birth'] ? date('F j, Y', strtotime($admin['Date_of_Birth'])) : 'N/A'; ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gender:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['Gender'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <div class="info-card">
                    <h4><i class="fas fa-map-marker-alt"></i> Contact Details</h4>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['Email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['Phone'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['Address'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">City:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['City'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <div class="info-card">
                    <h4><i class="fas fa-shield-alt"></i> Account Information</h4>
                    <div class="info-item">
                        <span class="info-label">Admin ID:</span>
                        <span class="info-value">#ADMIN<?php echo str_pad($admin['User_id'], 3, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Role:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admin['Role'] ?? 'Admin'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value" style="color: <?php echo $admin['Status'] == 'Active' ? '#27ae60' : '#e74c3c'; ?>;">
                            <i class="fas fa-<?php echo $admin['Status'] == 'Active' ? 'check-circle' : 'times-circle'; ?>"></i> 
                            <?php echo htmlspecialchars($admin['Status']); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Login:</span>
                        <span class="info-value">
                            <?php 
                            if (!empty($admin['Last_Login'])) {
                                $lastLogin = strtotime($admin['Last_Login']);
                                $now = time();
                                $diff = $now - $lastLogin;
                                
                                if ($diff < 3600) {
                                    echo floor($diff / 60) . ' minutes ago';
                                } elseif ($diff < 86400) {
                                    echo floor($diff / 3600) . ' hours ago';
                                } else {
                                    echo date('M d, Y h:i A', $lastLogin);
                                }
                            } else {
                                echo 'First login';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="activity-section">
                <div class="activity-header">
                    <div class="activity-title"><i class="fas fa-chart-line"></i> Recent Admin Activity</div>
                    <a href="#" class="btn btn-secondary">View All</a>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-check-double"></i></div>
                    <div class="activity-content">
                        <h5>Confirmed Booking #1021</h5>
                        <div class="activity-time">5 minutes ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-plus-circle"></i></div>
                    <div class="activity-content">
                        <h5>Added new Package: Sundarbans Deluxe</h5>
                        <div class="activity-time">2 hours ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-comment-dots"></i></div>
                    <div class="activity-content">
                        <h5>Responded to Support Ticket #45</h5>
                        <div class="activity-time">1 day ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>