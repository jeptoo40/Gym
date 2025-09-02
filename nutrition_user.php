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
      background: linear-gradient(135deg, #f1f8e9, #e0f7fa);
      margin: 0;
      padding: 0;
    }

    header {
      background: #2e7d32;
      color: #fff;
      padding: 25px;
      text-align: center;
      box-shadow: 0 3px 6px rgba(0,0,0,0.2);
      border-bottom-left-radius: 20px;
      border-bottom-right-radius: 20px;
    }

    header h1 {
      margin: 0;
      font-size: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }

    .container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 0 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 25px;
    }

    .back-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 10px;
  background: black;
  color: palevioletred;
  border-radius: 50%;
  text-decoration: none;
  font-size: 18px;
  position: fixed;  /* floating in top-left corner */
  top: 20px;
  left: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  transition: background 0.3s ease, transform 0.2s ease;
}

.back-btn:hover {
  background: burlywood;
  transform: scale(1.1);
}


    .plan {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .plan:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .plan img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .plan-content {
      padding: 20px;
      flex-grow: 1;
    }

    .plan h2 {
      margin: 0 0 10px;
      font-size: 22px;
      color: #2e7d32;
    }

    .plan p {
      margin: 6px 0;
      line-height: 1.6;
      color: #555;
      font-size: 15px;
    }

    .date {
      font-size: 0.85em;
      color: #777;
      margin-top: 12px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .no-plan {
      grid-column: 1 / -1;
      text-align: center;
      background: #fff;
      padding: 50px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      font-size: 18px;
      color: #666;
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
            <img src="/Admin/<?= htmlspecialchars($p['image']); ?>" alt="Nutrition Image">



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
