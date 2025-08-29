<?php
// Enable error reporting (for development only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$username = "root"; // Change as needed
$password = "1234";     // Change as needed
$dbname = "gym_db"; // Change as needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and fetch form inputs
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$trainer_id = trim($_POST['id']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$agreed_terms = isset($_POST['terms']);

// Server-side validation
if (!$agreed_terms) {
    die("You must agree to the terms and service.");
}

if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

if (strlen($password) < 8) {
    die("Password must be at least 8 characters long.");
}

// Check if email or trainer ID already exists
$stmt = $conn->prepare("SELECT * FROM trainers WHERE email = ? OR trainer_id = ?");
$stmt->bind_param("ss", $email, $trainer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("An account with this email or trainer ID already exists.");
}
$stmt->close();

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into the database
$stmt = $conn->prepare("INSERT INTO trainers (full_name, email, trainer_id, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $full_name, $email, $trainer_id, $hashed_password);

if ($stmt->execute()) {
    echo "Account created successfully. <a href='admin_login.html'>Login here</a>";
    
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
