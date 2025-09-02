<?php
session_start();
include("connect.php");

// Here the admin logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: login.php");
    exit();
}

$trainer_id = $_SESSION['trainer_id'];

// Fetch admin data
$stmt = $conn->prepare("SELECT full_name, email, trainer_id, created_at, profile_image FROM trainers WHERE trainer_id = ?");
$stmt->bind_param("s", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$success = $error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update basic info
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    // Handle optional profile image upload
    $profileImagePath = $admin['profile_image']; // keep existing if none uploaded
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = "uploads/profile/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            $profileImagePath = $targetFile;
        } else {
            $error = "❌ Failed to upload image.";
        }
    }

    // Update info in database
    $stmt = $conn->prepare("UPDATE trainers SET full_name = ?, email = ?, profile_image = ? WHERE trainer_id = ?");
    $stmt->bind_param("ssss", $full_name, $email, $profileImagePath, $trainer_id);
    if ($stmt->execute()) {
        $success = "✅ Profile updated successfully!";
        $admin['full_name'] = $full_name;
        $admin['email'] = $email;
        $admin['profile_image'] = $profileImagePath;
    } else {
        $error = "❌ Failed to update profile.";
    }

    // Handle password change separately
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE trainers SET password = ? WHERE trainer_id = ?");
        $stmt->bind_param("ss", $new_password, $trainer_id);
        if ($stmt->execute()) {
            $success .= " ✅ Password updated successfully!";
        } else {
            $error .= " ❌ Failed to update password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Settings</title>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
body {
    font-family: "Segoe UI", Tahoma, sans-serif;
    background: linear-gradient(135deg, #e0f7fa, #f1f8e9);
    margin: 0;
    padding: 0;
}
header {
    background: darkslategrey;
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
h2 {
    color: #2e7d32;
    margin-bottom: 15px;
}
.profile-info {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}
.profile-info img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #2e7d32;
}
.profile-details p {
    margin: 5px 0;
}
form label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
}
form input {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-bottom: 15px;
}
button {
    background: #2e7d32;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}
button:hover {
    background: #1b5e20;
}
.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 8px 12px;
    background: #333;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
}
.back-btn:hover {
    background: #555;
}
.success { color: green; font-weight: bold; margin-bottom: 15px; }
.error { color: red; font-weight: bold; margin-bottom: 15px; }
</style>
</head>
<body>

<header>
    <i class='bx bx-cog'></i> Admin Settings
</header>

<div class="container">
    <a href="admin dashboard.php" class="back-btn"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>

    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <h2>Profile Information</h2>
    <div class="profile-info">
        <?php if (!empty($admin['profile_image'])): ?>
            <img src="<?= htmlspecialchars($admin['profile_image']); ?>" alt="Profile Image">
        <?php else: ?>
            <img src="https://via.placeholder.com/100" alt="Profile Image">
        <?php endif; ?>
        <div class="profile-details">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($admin['full_name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']); ?></p>
            <p><strong>Trainer ID:</strong> <?= htmlspecialchars($admin['trainer_id']); ?></p>
            <p><strong>Member Since:</strong> <?= $admin['created_at']; ?></p>
        </div>
    </div>

    <h2>Edit Profile</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" required>

        <label>Profile Image (optional):</label>
        <input type="file" name="profile_image" accept="image/*">

        <label>New Password (optional):</label>
        <input type="password" name="new_password" placeholder="Enter new password">

        <button type="submit"><i class='bx bx-save'></i> Save Changes</button>
    </form>
</div>

</body>
</html>
