<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No account found!";
    }
}
?>
