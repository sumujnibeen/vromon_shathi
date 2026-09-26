<?php
// admin_register.php

// 1. Error Reporting (Debugging purposes)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
include('db_config.php'); // ডাটাবেস সংযোগ ফাইল

$error = '';
$success = '';

// Check if an admin is already logged in (optional, depends on policy)
if (isset($_SESSION['Admin_id'])) {
    // header("Location: admin_profile.php"); // Uncomment if registration is restricted to Super Admins/Logged in users
    // exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Data Sanitization & Retrieval
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $dob = $_POST['dob'] ?? NULL;
    $gender = $_POST['gender'] ?? NULL;
    $address = trim($_POST['address'] ?? NULL);
    $city = trim($_POST['city'] ?? NULL);
    $role = $_POST['role'] ?? 'Admin'; // Default role
    $status = 'Active'; // Default status for new registration

    // 3. Simple Validation
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in the required fields (Name, Username, Email, Password).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // 4. Password Hashing (Crucial Security Step)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 5. Database Connection
        $db = connect();

        // 6. Check if Username or Email already exists
        $stmt_check = $db->prepare("SELECT Admin_id FROM admin WHERE Username = ? OR Email = ?");
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            // 7. Insert Data using Prepared Statement
            $query = "INSERT INTO admin (Name, Username, Email, Password, Phone, Date_of_Birth, Gender, Address, City, Role, Status, Created_at, Updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $db->prepare($query);
            
            // Bind parameters (s=string, i=integer, d=double, b=blob)
            $stmt->bind_param("sssssssssss", 
                $name, 
                $username, 
                $email, 
                $hashed_password, 
                $phone, 
                $dob, 
                $gender, 
                $address, 
                $city, 
                $role, 
                $status
            );
            
            if ($stmt->execute()) {
                $success = "Admin '{$name}' registered successfully! You can now log in.";
                // Clear input fields after success (optional)
                $_POST = []; 
            } else {
                $error = "Registration failed: " . $stmt->error;
            }
            
            $stmt->close();
        }
        $stmt_check->close();
        $db->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f6fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .register-container { background: white; padding: 2.5rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
        h2 { text-align: center; color: #0f4d2a; margin-bottom: 1.5rem; font-size: 2rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.4rem; font-weight: 600; color: #333; }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"], input[type="date"], select { 
            width: 100%; 
            padding: 0.8rem; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box; 
        }
        .form-row { display: flex; gap: 1rem; }
        .form-row > .form-group { flex: 1; }
        .form-row > .form-group.full-width { flex: none; width: 100%; }
        button { width: 100%; padding: 1rem; background-color: #0f4d2a; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1.1rem; margin-top: 1.5rem; }
        button:hover { background-color: #1a6d3f; }
        .error { background: #fee; color: #c0392b; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #e74c3c; }
        .success { background: #e6ffe6; color: #27ae60; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #2ecc71; }
        .login-link { text-align: center; margin-top: 1rem; font-size: 0.95rem; }
        .login-link a { color: #0f4d2a; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2><i class="fas fa-user-plus"></i> Admin Registration</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post" action="admin_register.php">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                        <option value="Super Admin">Super Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
            </div>
            
            <hr style="margin: 1.5rem 0; border-top: 1px solid #eee;">

            <div class="form-row">
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo (($_POST['gender'] ?? '') == 'Male' ? 'selected' : ''); ?>>Male</option>
                        <option value="Female" <?php echo (($_POST['gender'] ?? '') == 'Female' ? 'selected' : ''); ?>>Female</option>
                        <option value="Other" <?php echo (($_POST['gender'] ?? '') == 'Other' ? 'selected' : ''); ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
            </div>

            <button type="submit">
                <i class="fas fa-save"></i> Register Admin
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="admin_login.php">Log in here</a>
        </div>
    </div>
</body>
</html>