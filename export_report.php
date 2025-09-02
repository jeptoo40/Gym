<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$userStmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

// Fetch report data (workouts)
$workouts = $conn->prepare("SELECT workout_name, type, description, created_at FROM workouts WHERE user_id = ?");
$workouts->bind_param("i", $user_id);
$workouts->execute();
$workoutResult = $workouts->get_result();

// Fetch report data (nutrition)
$nutrition = $conn->prepare("SELECT plan_name, gym_type, description, created_at FROM nutrition WHERE user_id = ?");
$nutrition->bind_param("i", $user_id);
$nutrition->execute();
$nutritionResult = $nutrition->get_result();

// Build CSV
$csv = "Type,Name/Plan,Category,Gym Type/Type,Description,Assigned At\n";

// Workouts
while ($w = $workoutResult->fetch_assoc()) {
    $csv .= "Workout," . $w['workout_name'] . "," . $w['type'] . ",," . str_replace(",", ";", $w['description']) . "," . $w['created_at'] . "\n";
}

// Nutrition
while ($n = $nutritionResult->fetch_assoc()) {
    $csv .= "Nutrition," . $n['plan_name'] . "," . $n['gym_type'] . "," . $n['gym_type'] . "," . str_replace(",", ";", $n['description']) . "," . $n['created_at'] . "\n";
}

// Save a copy on server for admin
$reportDir = __DIR__ . "/Admin/uploads/reports/";
if (!is_dir($reportDir)) mkdir($reportDir, 0777, true);

$filename = "report_user_" . $user_id . "_" . time() . ".csv";
$filePath = $reportDir . $filename;

// Save CSV file
file_put_contents($filePath, $csv);

// Inserted record to reports table
$stmt = $conn->prepare("INSERT INTO reports (user_id, file_path) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $filePath);
$stmt->execute();

// Serve CSV to user for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="my_report.csv"');
echo $csv;
exit();
