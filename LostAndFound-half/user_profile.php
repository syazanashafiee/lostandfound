<?php
session_start();
include 'db_connect.php';

// Check jika user logged in DAN role user
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$message = "";
$user_id = $_SESSION['user_id'];

// Get user data
$sql = "SELECT * FROM accounts WHERE id='$user_id' AND role='user'";
$result = mysqli_query($connect, $sql);
$user = mysqli_fetch_assoc($result);

if(isset($_POST['update'])) {
    $name = $_POST['name'] ?? $user['name'];
    $contactnum = $_POST['contactnum'] ?? $user['contactnum'];
    $password = $_POST['password'] ?? '';
    
    // If password is provided, hash it
    if(!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $hashed_password = $user['password'];
    }
    
    $update_sql = "UPDATE accounts SET 
                   name='$name', 
                   contactnum='$contactnum', 
                   password='$hashed_password' 
                   WHERE id='$user_id' AND role='user'";
    
    if(mysqli_query($connect, $update_sql)) {
        $message = "Profile updated successfully!";
        // Refresh user data
        $result = mysqli_query($connect, $sql);
        $user = mysqli_fetch_assoc($result);
        $_SESSION['name'] = $user['name'];
    } else {
        $message = "Error: " . mysqli_error($connect);
    }
}

// Generate user key (simple version)
$user_key = strtoupper(substr(md5($user['id'] . $user['name']), 0, 8));
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile - Surau Ismail Kharofa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
        }
        
        .profile-section {
            display: flex;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-form {
            flex: 1;
        }
        
        .info-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .info-label {
            font-size: 14px;
            color: #777;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .info-value {
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }
        
        .uneditable {
            background: #f0f0f0;
            color: #666;
        }
        
        .key-value {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .submit-btn {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        
        .submit-btn:hover {
            background: #45a049;
        }
        
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .nav-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .nav-links a {
            color: #4CAF50;
            text-decoration: none;
            margin: 0 15px;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>User Profile</h1>
        <p>Surau Ismail Kharofa - Lost and Found System</p>
    </div>
    
    <?php if($message != ""): ?>
        <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-section">
        <!-- LEFT: Profile Information -->
        <div class="profile-info">
            <h2>Profile Information</h2>
            
            <div class="info-box">
                <div class="info-label">NAME</div>
                <div class="info-value"><?php echo htmlspecialchars($user['name']); ?></div>
            </div>
            
            <div class="info-box">
                <div class="info-label">CONTACT NUMBER</div>
                <div class="info-value"><?php echo htmlspecialchars($user['contactnum'] ?? 'Not set'); ?></div>
            </div>
            
            <div class="info-box uneditable">
                <div class="info-label">KEY</div>
                <div class="info-value key-value"><?php echo $user_key; ?></div>
            </div>
            
            <div class="info-box">
                <div class="info-label">PASSWORD</div>
                <div class="info-value">••••••••</div>
            </div>
            
            <div class="info-box uneditable">
                <div class="info-label">ROLE</div>
                <div class="info-value">USER</div>
            </div>
        </div>
        
        <!-- RIGHT: Edit Form -->
        <div class="profile-form">
            <h2>Edit Profile</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Contact Number:</label>
                    <input type="text" name="contactnum" value="<?php echo htmlspecialchars($user['contactnum'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>New Password (leave blank to keep current):</label>
                    <input type="password" name="password" placeholder="Enter new password">
                </div>
                
                <button type="submit" name="update" class="submit-btn">
                    Update Profile
                </button>
            </form>
            
            <div style="margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                <h3 style="margin-top: 0; color: #333;">Note:</h3>
                <p style="color: #666; margin-bottom: 0;">
                    • Name and Contact Number can be edited<br>
                    • Password can be changed (leave blank to keep current)<br>
                    • Key and Role are permanent
                </p>
            </div>
        </div>
    </div>
    
    <div class="nav-links">
        <a href="user_dashboard.php">← Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>