<?php
session_start();
require(__DIR__ . "/Admin/connect.php"); // this works from D:\Gym\
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch workouts assigned to this user
$sql = "SELECT workout_name, description, type, image, created_at 
        FROM workouts 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Workouts</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
    .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
    h1 { text-align: center; }
    .workout { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 6px; background: #fafafa; }
    .workout img { max-width: 200px; display: block; margin-top: 10px; border-radius: 5px; }
    .date { font-size: 12px; color: gray; margin-top: 5px; }
    .back-btn { float: right; margin-bottom: 20px; background: #333; color: #fff; padding: 8px 12px; text-decoration: none; border-radius: 5px; }
    .back-btn:hover { background: #555; }
  </style>
</head>
<body>
  <div class="container">
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    <h1>My Workouts</h1>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="workout">
          <h2><?= htmlspecialchars($row['workout_name']); ?></h2>
          <p><strong>Type:</strong> <?= htmlspecialchars($row['type']); ?></p>
          <p><?= nl2br(htmlspecialchars($row['description'])); ?></p>
          <?php if (!empty($row['image'])): ?>
            <img src="../Admin/<?= htmlspecialchars($row['image']); ?>" alt="Workout Image">
          <?php endif; ?>
          <p class="date">Assigned: <?= $row['created_at']; ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No workouts assigned yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>
