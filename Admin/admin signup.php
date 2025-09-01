<?php
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name   = $_POST['full_name'];
    $email       = $_POST['email'];
    $trainer_id  = $_POST['trainer_id'];
    $password    = $_POST['password'];
    $confirm     = $_POST['confirm_password'];

    // ✅ Password match check
    if ($password !== $confirm) {
        die("❌ Passwords do not match");
    }

    // ✅ Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert admin
    $stmt = $conn->prepare("INSERT INTO trainers (full_name, email, trainer_id, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $trainer_id, $hashedPassword);

    if ($stmt->execute()) {
        echo "✅ Signup successful. <a href='admin_login.html'>Login here</a>";
    } else {
        echo "❌ Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
