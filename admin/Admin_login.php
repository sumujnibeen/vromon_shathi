<?php
// admin_login.php

session_start();

// যদি db_config.php ফাইলটি অন্য ফোল্ডারে থাকে, তবে পাথ পরিবর্তন করুন
include('db_config.php');

$error = '';

// যদি অ্যাডমিন ইতিমধ্যেই লগইন করে থাকে, তাহলে প্রোফাইলে রিডাইরেক্ট করো
if (isset($_SESSION['Admin_id'])) {
    header("Location: admin_profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ইনপুট থেকে ইউজারনেম ও পাসওয়ার্ড নেওয়া
    $username_input = $_POST['username'] ?? '';
    $password_input = $_POST['password'] ?? '';

    // ইনপুট খালি কিনা চেক করা
    if (empty($username_input) || empty($password_input)) {
        $error = "Please enter both username and password.";
    } else {
        
        $db = connect(); // db_config.php থেকে ডাটাবেস সংযোগ করা
        
        // --- ডাটাবেস থেকে তথ্য আনা (Prepared Statement) ---
        $stmt = $db->prepare("SELECT Admin_id, Password FROM admin WHERE Username = ? AND Status = 'Active'");
        $stmt->bind_param("s", $username_input);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();
        $db->close();

        // পাসওয়ার্ড যাচাই করা (Hash করা পাসওয়ার্ডের জন্য)
        if ($admin && password_verify($password_input, $admin['Password'])) {
            
            // ✅ লগইন সফল: সেশন সেট করা
            $_SESSION['Admin_id'] = $admin['Admin_id'];
            
            // প্রোফাইল পেজে রিডাইরেক্ট করা
            header("Location: admin_profile.php");
            exit();

        } else {
            $error = "Invalid username/password or inactive account.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ভ্রমণ সাথী</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #1a6d3f 0%, #0f4d2a 100%); /* সবুজ ব্যাকগ্রাউন্ড */
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .login-container { 
            background: white; 
            padding: 2.5rem; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.3); 
            width: 350px; 
            text-align: center;
        }
        h2 { 
            color: #0f4d2a; 
            margin-bottom: 2rem; 
            font-size: 1.8rem;
        }
        .logo-icon {
            font-size: 3rem;
            color: #1a6d3f;
            margin-bottom: 1rem;
        }
        label { 
            display: block; 
            text-align: left;
            margin-bottom: 0.5rem; 
            font-weight: 600; 
            color: #555;
        }
        input[type="text"], input[type="password"] { 
            width: 100%; 
            padding: 0.9rem; 
            margin-bottom: 1.5rem; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box; 
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #0f4d2a;
            outline: none;
        }
        button { 
            width: 100%; 
            padding: 1rem; 
            background-color: #0f4d2a; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 1rem; 
            font-weight: 700;
            transition: background-color 0.3s, transform 0.1s;
        }
        button:hover { 
            background-color: #1a6d3f; 
            transform: translateY(-2px);
        }
        .error { 
            background: #ffe6e6; 
            color: #c0392b; 
            padding: 0.8rem; 
            border-radius: 6px; 
            margin-bottom: 1.5rem; 
            border: 1px solid #e74c3c;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-icon"><i class="fas fa-route"></i></div>
        <h2>Admin Panel Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="admin_login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">
                <i class="fas fa-sign-in-alt"></i> Log In
            </button>
        </form>
    </div>
</body>
</html>