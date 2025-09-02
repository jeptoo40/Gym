<?php
session_start();
include("connect.php");

// Ensure admin is logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all reports
$reports = $conn->query("SELECT * FROM reports ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Reports</title>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: #f4f4f4;
    padding: 20px;
}
.container {
    max-width: 1000px;
    margin: auto;
}
h1 {
    text-align: center;
    color: #2e7d32;
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}
th {
    background: #2e7d32;
    color: #fff;
}
tr:hover {
    background: #f0f0f0;
}
.download-btn {
    background: #2e7d32;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}
.download-btn:hover {
    background: #1b5e20;
}
.back-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 8px 12px;
    background: #333;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
}
.back-btn:hover {
    background: #555;
}
</style>
</head>
<body>
<div class="container">
<h1>User Reports</h1>
<table>
<tr>
    <th>ID</th>
    <th>User</th>
    <th>Report File</th>
    <th>Submitted At</th>
    <th>Action</th>
</tr>
<?php while($report = $reports->fetch_assoc()): ?>
    <?php
    // Fetch user fullname
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
    $stmt->bind_param("i", $report['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $username = $user ? $user['fullname'] : "Unknown";
    ?>
    <tr>
        <td><?= $report['id']; ?></td>
        <td><?= htmlspecialchars($username); ?></td>
        <td><?= basename($report['file_path']); ?></td>
        <td><?= $report['created_at']; ?></td>
        <td>
        <a class="download-btn" href="/Admin/uploads/reports/<?= urlencode(basename($report['file_path'])); ?>" download>
    <i class='bx bx-download'></i> Download
</a>


    
</td>

    </tr>
<?php endwhile; ?>
</table>

<a href="admin dashboard.php" class="back-btn"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>
</div>
</body>
</html>
