<?php
$host = "localhost";
$user = "root";   
$pass = "1234";        
$db = "gym_db";     

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (fullname, email, password) VALUES ('$fullname', '$email', '$password')";
if ($conn->query($sql) === TRUE) {
    echo "Account created successfully!";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
