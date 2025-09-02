<?php
session_start();
include("connect.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT fullname, email, created_at, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = $error = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    
    // Handle profile image upload
    $profile_image = $user['profile_image'];
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadDir = "uploads/profiles/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
        $filePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filePath)) {
            $profile_image = $filePath;
        }
    }

    if ($fullname && $email) {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $fullname, $email, $profile_image, $user_id);
        if ($stmt->execute()) {
            $success = "✅ Profile updated successfully!";
            $user['fullname'] = $fullname;
            $user['email'] = $email;
            $user['profile_image'] = $profile_image;
        } else {
            $error = "❌ Failed to update profile.";
        }
    } else {
        $error = "❌ Full Name and Email cannot be empty.";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password, $user_id);
    if ($stmt->execute()) {
        $success = "✅ Password updated successfully!";
    } else {
        $error = "❌ Failed to update password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Settings</title>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg, #e0f7fa, #f1f8e9);
    margin: 0; padding: 0;
}
header {
    background: #2e7d32;
    color: #fff;
    padding: 20px;
    text-align: center;
    font-size: 26px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}
.container {
    max-width: 700px;
    margin: 30px auto;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
h2 { color: #2e7d32; margin-bottom: 15px; }
form label { display: block; margin: 10px 0 5px; font-weight: bold; }
form input[type="text"], form input[type="email"], form input[type="password"] {
    width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 15px;
}
form input[type="file"] {
    margin-bottom: 15px;
}
button {
    background: #2e7d32; color: #fff; padding: 12px 20px;
    border: none; border-radius: 8px; cursor: pointer; font-weight: bold;
    transition: 0.3s;
}
button:hover { background: #1b5e20; }
.back-btn {
    display: inline-block; margin-bottom: 20px; padding: 8px 12px;
    background: #333; color: #fff; border-radius: 5px;
    text-decoration: none;
}
.back-btn:hover { background: #555; }
.success { color: green; font-weight: bold; margin-bottom: 15px; }
.error { color: red; font-weight: bold; margin-bottom: 15px; }

.profile-section {
    text-align: center;
    margin-bottom: 25px;
}
.profile-section img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px solid #2e7d32;
    object-fit: cover;
    margin-bottom: 15px;
}
.info p { margin: 6px 0; }
</style>
</head>
<body>

<header>
    <i class='bx bx-cog'></i> User Settings
</header>

<div class="container">
    <a href="dashboard.php" class="back-btn"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>

    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <div class="profile-section">
        <img src="<?= $user['profile_image'] ? htmlspecialchars($user['profile_image']) : 'uploads/profiles/default.png'; ?>" alt="Profile Image">
        <div class="info">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($user['fullname']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
            <p><strong>Member Since:</strong> <?= $user['created_at']; ?></p>
        </div>
    </div>

    <h2>Edit Profile</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_profile" value="1">
        <label>Full Name:</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']); ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        <label>Profile Image:</label>
        <input type="file" name="profile_image" accept="image/*">
        <button type="submit"><i class='bx bx-user'></i> Update Profile</button>
    </form>

    <h2>Change Password</h2>
    <form method="POST">
        <label>New Password:</label>
        <input type="password" name="new_password" placeholder="Enter new password" required>
        <button type="submit"><i class='bx bx-lock-alt'></i> Update Password</button>
    </form>
</div>

</body>
</html>
