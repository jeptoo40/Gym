<?php
$host = "localhost";
$dbname = "gym_db";
$username = "root";  // change if different
$password = "1234";      // change if you have a password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
