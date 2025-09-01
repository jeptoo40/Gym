<?php
session_start();
include("connect.php");

// Ensure admin logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch users with their latest booking gym_type
$users = $conn->query("
    SELECT b.user_id, b.name, b.gym_type
    FROM bookings b
    WHERE b.id IN (SELECT MAX(id) FROM bookings GROUP BY user_id)
    ORDER BY b.name ASC
");

// Handle nutrition plan assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $gym_type = $_POST['gym_type'];
    $plan_name = $_POST['plan_name'];
    $description = $_POST['description'];
// Handle image upload
$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $targetDir = "uploads/nutrition/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . "_" . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        // ✅ Save only relative path, not absolute Windows path
        $imagePath = $targetDir . $fileName;
    }
}


    $stmt = $conn->prepare("INSERT INTO nutrition (user_id, gym_type, plan_name, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $gym_type, $plan_name, $description, $imagePath);
    $stmt->execute();

    $success = "✅ Nutrition plan assigned successfully!";
}

// Fetch nutrition plans
$nutritionPlans = $conn->query("
    SELECT n.id, b.name, n.gym_type, n.plan_name, n.description, n.image, n.created_at
    FROM nutrition n
    JOIN bookings b ON n.user_id = b.user_id
    ORDER BY n.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Nutrition Plans - Admin Dashboard</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: #f0f2f5;
      margin: 0; padding: 0;
    }
    header {
      background: #222;
      color: #fff;
      padding: 15px 30px;
      font-size: 20px;
      font-weight: bold;
    }
    .container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 20px;
    }
    h1, h2 {
      color: #333;
    }
    .card {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    .card h1 {
      font-size: 22px;
      margin-bottom: 20px;
    }
    form label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    form select, form input, form textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      outline: none;
    }
    form input:focus, form textarea:focus, form select:focus {
      border-color: #222;
    }
    button {
      background: #222;
      color: #fff;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background: #444;
    }
    .success {
      color: green;
      font-weight: bold;
      margin-bottom: 15px;
    }



    .btn-container {
  display: flex;
  justify-content: flex-end; /* pushes button to right */
  margin-bottom: 15px;
}
.back-btn {
  padding: 10px 15px;
  background: green;
  color: #fff;
  border-radius: 5px;
  text-decoration: none;
  transition: 0.3s;
}
.back-btn:hover {
  background: darkgreen;
}









    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: #222;
      color: #fff;
    }
    tr:hover {
      background: #f9f9f9;
    }
    img {
      border-radius: 8px;
    }

   

  </style>
</head>
<body>
  <header>
    <i class='bx bxs-bowl-hot'></i> Admin Dashboard - Nutrition Plans
  </header>
  <div class="container">

    <div class="card">
      <h1>Assign Nutrition Plan</h1>
      <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

      <form method="POST" enctype="multipart/form-data">
        <label>User (by Gym Type):</label>
        <select name="user_id" required onchange="updateGymType(this)">
          <option value="">-- Select User --</option>
          <?php while($u = $users->fetch_assoc()) { ?>
            <option value="<?= $u['user_id']; ?>" data-gym="<?= $u['gym_type']; ?>">
              <?= htmlspecialchars($u['name']); ?> - <?= $u['gym_type']; ?>
            </option>
          <?php } ?>
        </select>

        <input type="hidden" name="gym_type" id="gymTypeField">

        <label>Plan Name:</label>
        <input type="text" name="plan_name" placeholder="e.g. Weight Gain Plan" required>

        <label>Description:</label>
        <textarea name="description" rows="4" placeholder="Write details of the plan..."></textarea>

        <label>Upload Nutrition Image:</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit"><i class='bx bx-plus'></i> Assign Plan</button>
              <!-- Back to Dashboard Button -->
              <div class="btn-container">
              <a href="admin dashboard.php" class="back-btn">
               <i class='bx bx-arrow-back'></i> Back to Dashboard
                    </a>
                    </div>

           <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>


      </form>
    </div>





    <div class="card">
      <h2>Assigned Nutrition Plans</h2>

      <table>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Gym Type</th>
          <th>Plan Name</th>
          <th>Description</th>
          <th>Image</th>
          <th>Assigned At</th>
        </tr>
        <?php while($n = $nutritionPlans->fetch_assoc()) { ?>
          <tr>
            <td><?= $n['id']; ?></td>
            <td><?= htmlspecialchars($n['name']); ?></td>
            <td><?= htmlspecialchars($n['gym_type']); ?></td>
            <td><?= htmlspecialchars($n['plan_name']); ?></td>
            <td><?= nl2br(htmlspecialchars($n['description'])); ?></td>
            <td>
              <?php if (!empty($n['image'])) { ?>
                <img src="<?= $n['image']; ?>" alt="Nutrition" width="80">
              <?php } else { echo "No image"; } ?>
            </td>
            <td><?= $n['created_at']; ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </div>

  <script>
  function updateGymType(select) {
    let gymType = select.options[select.selectedIndex].getAttribute("data-gym");
    document.getElementById("gymTypeField").value = gymType;
  }
  </script>
</body>
</html>
