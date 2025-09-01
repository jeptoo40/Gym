<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trainer_id = $_POST['trainer_id'];
    $password   = $_POST['password'];

    // ✅ Get admin by trainer_id
    $stmt = $conn->prepare("SELECT * FROM trainers WHERE trainer_id = ?");
    $stmt->bind_param("s", $trainer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();

        // ✅ Verify password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['trainer_id'] = $admin['trainer_id']; // store trainer_id
            header("Location: admin dashboard.php");
            exit();
        } else {
            echo "❌ Wrong password.";
        }
    } else {
        echo "❌ Trainer ID not found.";
    }
}
?>
