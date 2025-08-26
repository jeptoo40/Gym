<?php
session_start();
include("connect.php"); // your DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // ✅ verify hashed password
        if (password_verify($password, $row['password'])) {
            // set session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['fullname']; // adjust column name if different
            $_SESSION['user_email'] = $row['email'];

            // redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
