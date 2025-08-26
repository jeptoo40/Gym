<?php
session_start();
include("connect.php"); // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        // Insert user
        $sql = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $fullname, $email, $password);

        if ($sql->execute()) {
            // Get new user ID
            $user_id = $sql->insert_id;

            // Start session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $fullname;
            $_SESSION['user_email'] = $email;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>
