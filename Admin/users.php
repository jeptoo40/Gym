<?php
include("connect.php");

// Fetch totals
$totalUsers = $conn->query("SELECT COUNT(*) AS total_users FROM users")->fetch_assoc()['total_users'];
$totalWorkouts = $conn->query("SELECT COUNT(*) AS total_workouts FROM workouts")->fetch_assoc()['total_workouts'];
$totalNutrition = $conn->query("SELECT COUNT(*) AS total_nutrition FROM nutrition")->fetch_assoc()['total_nutrition'];
$totalTrainers = $conn->query("SELECT COUNT(*) AS total_trainers FROM trainers")->fetch_assoc()['total_trainers'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard Overview</title>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}
header {
    background: #222;
    color: #fff;
    padding: 20px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
}
header h1 {
    margin: 0;
    font-size: 24px;
}
.back-btn {
    background: #2e7d32;
    color: #fff;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: 0.3s;
}
.back-btn:hover {
    background: #1b5e20;
}

.dashboard-cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin: 30px;
}

.dashboard-card {
    display: flex;
    align-items: center;
    gap: 20px;
    color: #fff;
    padding: 20px 25px;
    border-radius: 15px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    max-width: 250px;
    flex: 1 1 250px;
    transition: transform 0.3s, box-shadow 0.3s;
    text-decoration: none;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
.dashboard-card .icon i {
    font-size: 40px;
}
.dashboard-card .content {
    display: flex;
    flex-direction: column;
}
.dashboard-card .content .label {
    font-size: 14px;
    opacity: 0.85;
}
.dashboard-card .content .number {
    font-size: 28px;
    font-weight: bold;
    line-height: 1;
}

/* Card colors */
.dashboard-card.users { background: linear-gradient(135deg, #43a047, #66bb6a); }
.dashboard-card.trainers { background: linear-gradient(135deg, #8e24aa, #ab47bc); }
.dashboard-card.workouts { background: linear-gradient(135deg, #1e88e5, #42a5f5); }
.dashboard-card.nutrition { background: linear-gradient(135deg, #f4511e, #ff7043); }
</style>
</head>
<body>

<header>
    <h1><i class='bx bx-tachometer'></i> Admin Dashboard Overview</h1>
    <a href="admin dashboard.php" class="back-btn"><i class='bx bx-arrow-back'></i> Dashboard Home</a>
</header>

<div class="dashboard-cards">
    <a href="#" class="dashboard-card users">
        <div class="icon"><i class='bx bx-user'></i></div>
        <div class="content">
            <span class="label">Total Users</span>
            <span class="number"><?= $totalUsers; ?></span>
        </div>
    </a>

    <a href="#" class="dashboard-card trainers">
        <div class="icon"><i class='bx bx-id-card'></i></div>
        <div class="content">
            <span class="label">Total Trainers</span>
            <span class="number"><?= $totalTrainers; ?></span>
        </div>
    </a>

    <a href="workouts.php" class="dashboard-card workouts">
        <div class="icon"><i class='bx bx-dumbbell'></i></div>
        <div class="content">
            <span class="label">Total Workouts</span>
            <span class="number"><?= $totalWorkouts; ?></span>
        </div>
    </a>

    <a href="nutrition.php" class="dashboard-card nutrition">
        <div class="icon"><i class='bx bx-bowl-hot'></i></div>
        <div class="content">
            <span class="label">Total Nutrition Plans</span>
            <span class="number"><?= $totalNutrition; ?></span>
        </div>
    </a>
</div>

</body>
</html>
