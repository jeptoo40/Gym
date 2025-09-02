<?php
session_start();
include("connect.php");

// Ensure admin is logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: login.php");
    exit();
}

$trainer_id = $_SESSION['trainer_id'];

// Fetch trainer info
$stmt = $conn->prepare("SELECT id, full_name, email, trainer_id, created_at FROM trainers WHERE trainer_id = ?");
$stmt->bind_param("s", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Trainer not found.";
    exit();
}

$trainer = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Profile</title>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg, #e0f7fa, #f1f8e9);
    margin: 0;
    padding: 0;
}

.profile-container {
    max-width: 500px;
    margin: 60px auto;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    text-align: center;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    background: #2e7d32;
    color: #fff;
    font-size: 48px;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.profile-container h1 {
    margin: 10px 0;
    color: #2e7d32;
    font-size: 26px;
}

.profile-container p {
    font-size: 16px;
    color: #555;
    margin: 6px 0;
}

.back-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 18px;
    background: #2e7d32;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s;
}
.back-btn:hover {
    background: #1b5e20;
}

.info-box {
    background: #f9f9f9;
    padding: 15px 20px;
    margin: 10px 0;
    border-radius: 10px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    text-align: left;
}
.info-box strong {
    color: #2e7d32;
}
</style>
</head>
<body>

<div class="profile-container">
    <div class="profile-avatar"><?= strtoupper(substr($trainer['full_name'],0,1)); ?></div>
    <h1><?= htmlspecialchars($trainer['full_name']); ?></h1>

    <div class="info-box"><strong>Email:</strong> <?= htmlspecialchars($trainer['email']); ?></div>
    <div class="info-box"><strong>Trainer ID:</strong> <?= htmlspecialchars($trainer['trainer_id']); ?></div>
    <div class="info-box"><strong>Joined:</strong> <?= $trainer['created_at']; ?></div>

    <a href="admin dashboard.php" class="back-btn"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>
</div>

</body>
</html>
