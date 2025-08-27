<?php
session_start();
include("connect.php");
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['user_name'] ?? ''); // STRING
    $age  = intval($_POST['user_age'] ?? 0); // INTEGER

    $date = $_POST['session_date'] ?? '';
    $time = $_POST['session_time'] ?? '';
    $gym = $_POST['gym_type'] ?? '';
    $agree = isset($_POST['agree_conditions']);

    if (!$name || !$age || !$date || !$time || !$gym || !$agree) {
        echo json_encode(['success'=>false,'message'=>'Please fill all fields and agree to conditions.']);
        exit();
    }

    $user_id = $_SESSION['user_id'] ?? 0;
    if (!$user_id) {
        echo json_encode(['success'=>false,'message'=>'You must be logged in.']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, age, session_date, session_time, gym_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isisss", $user_id, $name, $age, $date, $time, $gym);

    if ($stmt->execute()) {
        echo json_encode(['success'=>true,'message'=>'Your session has been booked!']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Failed to book session. Try again.']);
    }
}
