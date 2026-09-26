<?php
include('controller.php');
session_start();
$db = connect();

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) == 'admin') {
    header('Location: ../admin/admin_pannel.php');
    exit;
}



// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $db->prepare("SELECT * FROM user WHERE User_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel</title>
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">



    <style>
    body {
        background: linear-gradient(to right, #e8f5e9, #ffffff);
        font-family: 'Poppins', sans-serif;
    }

    .sidebar {
        background-color: #198754;
        height: 100vh;
        padding-top: 30px;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 12px 20px;
        margin: 8px 0;
        border-radius: 8px;
        transition: 0.3s;
    }

    .sidebar a:hover {
        background-color: #157347;
    }

    .profile-box {
        background: #ffffff;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        margin-top: 40px;
        text-align: center;
        transition: all 0.3s ease-in-out;
    }

    .profile-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .profile-photo {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #198754;
        margin: 20px auto;
    }

    .user-info h4 {
        color: #198754;
        font-weight: 600;
    }

    .user-info p {
        color: #555;
        font-size: 15px;
    }

    .btn-custom {
        background-color: #198754;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 25px;
        font-size: 15px;
        transition: 0.3s;
    }

    .btn-custom:hover {
        background-color: #146c43;
    }

    @media (max-width: 768px) {
        .sidebar {
            height: auto;
            padding: 10px;
        }
    }
    </style>
</head>

<body>



    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 p-0">
                <div class="sidebar">
                    <?php include('sidebar.php'); ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 d-flex justify-content-center align-items-start">
                <div class="profile-box">
                    <h3 class="fw-bold mb-3"><i class="fa-solid fa-user-circle me-2"></i>Profile Overview</h3>
                    <p class="text-muted">Welcome back, <strong><?php echo htmlspecialchars($user['Name']); ?></strong>!
                    </p>

                    <img src="<?php echo htmlspecialchars($user['Photo']); ?>" alt="Profile Photo"
                        class="profile-photo">

                    <div class="user-info mt-3">
                        <h4><?php echo htmlspecialchars($user['Name']); ?></h4>
                        <p><i class="fa-solid fa-envelope me-2"></i><?php echo htmlspecialchars($user['Email']); ?></p>
                        <p><i class="fa-solid fa-user-tag me-2"></i>Role:
                            <?php echo htmlspecialchars(ucfirst($user['Role'])); ?></p>
                        <p><i class="fa-solid fa-calendar me-2"></i>Joined on:
                            <?php echo date('F j, Y', strtotime($user['Created_at'])); ?></p>
                    </div>

                    <div class="mt-4">
                        <a href="update_profile.php" class="btn btn-custom">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Update Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>