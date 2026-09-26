<?php
// add_admin.php
session_start();
include('controller.php');

// Check if admin logged in
//if (!isset($_SESSION['admin_id'])) {
 //   header("Location: admin_login.php");
 //   exit;
//}

$db = connect();
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $role = $_POST['role'];
    
    // Validation
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = "All required fields must be filled!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        // Check if username or email already exists
        $check = $db->prepare("SELECT * FROM admin WHERE Username = ? OR Email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username or Email already exists!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Handle photo upload
            $photo = 'assets/default-admin.png';
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['photo']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = 'admin_' . time() . '.' . $ext;
                    $upload_path = 'assets/admin_photos/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                        $photo = $upload_path;
                    }
                }
            }
            
            // Insert into database
            $stmt = $db->prepare("INSERT INTO admin (Name, Username, Email, Password, Phone, Photo, Date_of_Birth, Gender, Address, City, Role, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
            $stmt->bind_param("sssssssssss", $name, $username, $email, $hashed_password, $phone, $photo, $date_of_birth, $gender, $address, $city, $role);
            
            if ($stmt->execute()) {
                $success = "Admin added successfully!";
                // Clear form
                $_POST = array();
            } else {
                $error = "Error adding admin: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin - ভ্রমণ সাথী</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e8f5e9 0%, #f5f6fa 100%);
            color: #333;
            padding: 20px;
        }

        .container-custom {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0f4d2a;
        }

        .page-header h1 {
            color: #0f4d2a;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .page-header .icon {
            font-size: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #0f4d2a;
            margin-bottom: 8px;
        }

        .form-label .required {
            color: #e74c3c;
        }

        .form-control, .form-select {
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0f4d2a;
            box-shadow: 0 0 0 0.2rem rgba(15, 77, 42, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .photo-upload {
            border: 2px dashed #0f4d2a;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
        }

        .photo-upload:hover {
            background: #e8f5e9;
            border-color: #1a6d3f;
        }

        .photo-upload i {
            font-size: 3rem;
            color: #0f4d2a;
            margin-bottom: 10px;
        }

        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 15px;
            display: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #0f4d2a, #1a6d3f);
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 77, 42, 0.3);
        }

        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: #5a6268;
            color: white;
        }

        .alert {
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .section-divider {
            margin: 30px 0;
            border-top: 2px solid #e1e8ed;
            padding-top: 20px;
        }

        .section-title {
            color: #0f4d2a;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .password-strength {
            height: 5px;
            background: #e1e8ed;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
        }

        .password-strength-weak { 
            background: #e74c3c; 
            width: 33%;
        }
        .password-strength-medium { 
            background: #f39c12; 
            width: 66%;
        }
        .password-strength-strong { 
            background: #27ae60; 
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container-custom">
    <!-- Header -->
    <div class="page-header">
        <h1>
            <span class="icon">👤</span>
            Add New Admin
        </h1>
        <p style="color: #666; margin-top: 10px;">Create a new administrator account</p>
    </div>

    <!-- Alert Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" enctype="multipart/form-data" id="adminForm">
        
        <!-- Personal Information -->
        <div class="section-title">
            <i class="fas fa-user"></i> Personal Information
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Full Name <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g., Mohammad Karim" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Username <span class="required">*</span></label>
                <input type="text" name="username" class="form-control" placeholder="e.g., admin_karim" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="section-divider"></div>
        <div class="section-title">
            <i class="fas fa-address-book"></i> Contact Information
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email <span class="required">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="admin@bhromonsathi.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="+880 1712-345678" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" placeholder="e.g., Gulshan, Dhaka"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" placeholder="e.g., Dhaka" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
            </div>
        </div>

        <!-- Account Information -->
        <div class="section-divider"></div>
        <div class="section-title">
            <i class="fas fa-shield-alt"></i> Account Information
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Password <span class="required">*</span></label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 6 characters" required>
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <small id="strengthText" style="color: #666;"></small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm Password <span class="required">*</span></label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Role <span class="required">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="Super Admin">Super Admin</option>
                    <option value="Admin">Admin</option>
                    <option value="Manager">Manager</option>
                </select>
            </div>
        </div>

        <!-- Photo Upload -->
        <div class="section-divider"></div>
        <div class="section-title">
            <i class="fas fa-camera"></i> Profile Photo
        </div>

        <div class="mb-4">
            <label for="photoUpload" class="photo-upload">
                <i class="fas fa-cloud-upload-alt"></i>
                <p style="margin: 10px 0 5px 0; font-weight: 600;">Click to upload photo</p>
                <small style="color: #666;">JPG, PNG or GIF (Max 2MB)</small>
                <input type="file" name="photo" id="photoUpload" accept="image/*" style="display: none;">
            </label>
            <img id="photoPreview" class="photo-preview" alt="Preview">
        </div>

        <!-- Buttons -->
        <div class="row mt-4">
            <div class="col-md-6 mb-2">
                <a href="admin_dashboard.php" class="btn-back w-100">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            <div class="col-md-6 mb-2">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Add Admin
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Photo Preview
document.getElementById('photoUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Password Strength Check
document.getElementById('password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    strengthBar.className = 'password-strength-bar';
    
    if (strength <= 2) {
        strengthBar.classList.add('password-strength-weak');
        strengthText.textContent = 'Weak password';
        strengthText.style.color = '#e74c3c';
    } else if (strength <= 4) {
        strengthBar.classList.add('password-strength-medium');
        strengthText.textContent = 'Medium strength';
        strengthText.style.color = '#f39c12';
    } else {
        strengthBar.classList.add('password-strength-strong');
        strengthText.textContent = 'Strong password';
        strengthText.style.color = '#27ae60';
    }
});

// Form validation
document.getElementById('adminForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters!');
        return false;
    }
});
</script>

</body>
</html>