<?php
session_start();
include("connect.php");

// Ensure admin logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch users
$users = $conn->query("SELECT id, fullname, email FROM users ORDER BY fullname ASC");

// Handle workout assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $workout_name = $_POST['workout_name'];
    $description = $_POST['description'];
    $type = $_POST['type'];

    // Handle image upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/workouts/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetDir . $fileName; // relative path
        }
    }

    $stmt = $conn->prepare("INSERT INTO workouts (user_id, workout_name, description, type, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $workout_name, $description, $type, $imagePath);
    $stmt->execute();

    $success = "✅ Workout assigned successfully!";
}

// Fetch all assigned workouts
$workouts = $conn->query("
    SELECT w.id, u.fullname, w.workout_name, w.description, w.type, w.image, w.created_at
    FROM workouts w
    JOIN users u ON w.user_id = u.id
    ORDER BY w.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Workouts</title>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
header { background: #222; color: #fff; padding: 15px 30px; font-size: 20px; font-weight: bold; }
.container { max-width: 1100px; margin: 30px auto; padding: 20px; }
.card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
.card h1 { font-size: 22px; margin-bottom: 20px; }
form label { font-weight: bold; display: block; margin-top: 10px; }
form input, form select, form textarea { width: 100%; padding: 10px; margin-top: 6px; border-radius: 8px; border: 1px solid #ccc; }
button { background: #2e7d32; color: #fff; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; margin-top: 10px; }
button:hover { background: #1b5e20; }
.success { color: green; font-weight: bold; margin-bottom: 15px; }
table { width: 100%; border-collapse: collapse; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
th { background: #222; color: #fff; }
tr:hover { background: #f9f9f9; }
img { border-radius: 8px; max-width: 80px; }
</style>
</head>
<body>
<header><i class='bx bx-dumbbell'></i> Admin Dashboard - Workouts</header>
<div class="container">

  <div class="card">
    <h1>Assign Workout</h1>
    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
      <label>User:</label>
      <select name="user_id" required>
        <option value="">-- Select User --</option>
        <?php while($u = $users->fetch_assoc()) { ?>
            <option value="<?= $u['id']; ?>"><?= htmlspecialchars($u['fullname']); ?> (<?= htmlspecialchars($u['email']); ?>)</option>
        <?php } ?>
      </select>

      <label>Workout Name:</label>
      <input type="text" name="workout_name" placeholder="e.g. Full Body Strength" required>

      <label>Description:</label>
      <textarea name="description" rows="4" placeholder="Workout details..."></textarea>

      <label>Type:</label>
      <input type="text" name="type" placeholder="e.g. Strength, Cardio">

      <label>Upload Image:</label>
      <input type="file" name="image" accept="image/*">

      <button type="submit"><i class='bx bx-plus-circle'></i> Assign Workout</button>
<!-- Back to Dashboard Button on the right -->
      <a href="admin dashboard.php" 
       style="position: rights; top: 20px; right: 20px; padding:10px 15px; background:green; color:#fff; border-radius:5px; text-decoration:none; font-weight:bold; transition:0.3s;">
       <i class='bx bx-arrow-back'></i> Back to Dashboard
      </a>


    </form>
  </div>

  <div class="card">
    <h2>Assigned Workouts</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Workout Name</th>
        <th>Description</th>
        <th>Type</th>
        <th>Image</th>
        <th>Assigned At</th>
      </tr>
      <?php while($w = $workouts->fetch_assoc()) { ?>
        <tr>
          <td><?= $w['id']; ?></td>
          <td><?= htmlspecialchars($w['fullname']); ?></td>
          <td><?= htmlspecialchars($w['workout_name']); ?></td>
          <td><?= nl2br(htmlspecialchars($w['description'])); ?></td>
          <td><?= htmlspecialchars($w['type']); ?></td>
          <td>
            <?php if(!empty($w['image'])) { ?>
              <img src="<?= htmlspecialchars($w['image']); ?>" alt="Workout Image">
            <?php } else { echo "No image"; } ?>
          </td>
          <td><?= $w['created_at']; ?></td>
        </tr>
      <?php } ?>
    </table>
  </div>

</div>
</body>
</html>
