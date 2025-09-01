<?php
session_start();
include("connect.php");


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


// Base URL for images (adjust if project folder name changes)
$base_url = "http://localhost/Gym/Admin/";


// Fetch nutrition plans for this user
$result = $conn->prepare("
    SELECT n.plan_name, n.description, n.gym_type, n.image, n.created_at
    FROM nutrition n
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
");
$result->bind_param("i", $user_id);
$result->execute();
$plans = $result->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Nutrition Plans</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      font-family: "Segoe UI", Tahoma, sans-serif;
      background: linear-gradient(135deg, #e0f7fa, #f1f8e9);
      margin: 0;
      padding: 0;
    }

    header {
      background: darkslategrey;
      color: #fff;
      padding: 20px;
      text-align: center;
      box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    }

    header h1 {
      margin: 0;
      font-size: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .container {
      max-width: 900px;
      margin: 30px auto;
      padding: 0 20px;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 18px;
      background: #2e7d32;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .back-btn:hover {
      background: #1b5e20;
    }

    .plan {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
    }

    .plan:hover {
      transform: translateY(-5px);
    }

    .plan h2 {
      margin: 0 0 10px;
      color: #2e7d32;
    }

    .plan p {
      margin: 6px 0;
      line-height: 1.6;
      color: #444;
    }

    .plan img {
      margin-top: 15px;
      max-width: 100%;
      height: auto;
      border-radius: 10px;
      border: 2px solid #eee;
    }

    .date {
      font-size: 0.85em;
      color: #777;
      margin-top: 10px;
    }

    .no-plan {
      text-align: center;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      font-size: 18px;
      color: #555;
    }
  </style>
</head>
<body>
  <header>
    <h1><i class='bx bx-bowl-hot'></i> My Nutrition Plans</h1>
  </header>

  <div class="container">
    <a href="dashboard.php" class="back-btn"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>

    <?php if ($plans->num_rows > 0): ?>
      <?php while ($p = $plans->fetch_assoc()): ?>
        <div class="plan">
          <h2><?= htmlspecialchars($p['plan_name']); ?></h2>
          <p><strong>Gym Type:</strong> <?= htmlspecialchars($p['gym_type']); ?></p>
          <p><?= nl2br(htmlspecialchars($p['description'])); ?></p>
          <?php if (!empty($p['image'])): ?>
            <img src="<?= htmlspecialchars($base_url . $p['image']); ?>" alt="Nutrition Plan Image">

          <?php endif; ?>
          <p class="date">Assigned: <?= $p['created_at']; ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-plan">
        <i class='bx bx-sad'></i> No nutrition plans assigned yet.
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
