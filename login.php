<?php
ob_start(); // ensure no output before headers
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // ✅ Verify hashed password
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['fullname'];
                $_SESSION['user_email'] = $row['email'];

                header("Location: dashboard.php");
                exit();
            } else {
                echo "❌ Invalid password.";
            }
        } else {
            echo "❌ No account found with that email.";
        }
    } else {
        echo "❌ Please enter both email and password.";
    }
}
?>
