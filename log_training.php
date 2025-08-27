<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['seconds'])) {
    $seconds = intval($_POST['seconds']);
    $user_id = $_SESSION['user_id'];

    // Insert this training session
    $stmt = $conn->prepare("INSERT INTO training_sessions (user_id, duration_seconds, date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $seconds);
    $stmt->execute();

    // Get total accumulated time
    $result = $conn->query("SELECT SUM(duration_seconds) as total FROM training_sessions WHERE user_id = $user_id");
    $row = $result->fetch_assoc();
    $total_seconds = $row['total'] ?? 0;

    $hours = floor($total_seconds / 3600);
    $minutes = floor(($total_seconds % 3600) / 60);

    $_SESSION['total_time'] = "{$hours}h {$minutes}m";

    // Convert this session’s seconds → minutes
    $session_minutes = floor($seconds / 60);

    echo json_encode([
        "total_time" => $_SESSION['total_time'],   // total across all sessions
        "active_minutes" => $session_minutes       // only this session
    ]);
}
?>
