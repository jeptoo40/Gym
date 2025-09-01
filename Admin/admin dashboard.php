<?php
session_start();
include("connect.php");

if (!isset($_SESSION['trainer_id'])) {
    die("⚠️ No session. Please login again.");
}

$trainerID = $_SESSION['trainer_id'];

// ✅ Fetch admin details using trainer_id
$stmt = $conn->prepare("SELECT full_name, email FROM trainers WHERE trainer_id = ?");
$stmt->bind_param("s", $trainerID);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$adminName  = $admin['full_name'] ?? "Admin";
$adminEmail = $admin['email'] ?? "";

// ✅ Fetch bookings data
$bookings = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      display: flex;
    }



    .logout-btn {
  background-color: #e74c3c;   /* red */
  color: #fff;                /* white text */
  border: none;
  padding: 10px 20px;
  font-size: 16px;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.logout-btn:hover {
  background-color: #c0392b;  /* darker red */
  transform: translateY(-2px);
  box-shadow: 0 6px 10px rgba(0,0,0,0.15);
}

.logout-btn:active {
  transform: translateY(0);
  box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}














    /* Sidebar */
    .sidebar {
      width: 250px;
      background: #222;
      color: #fff;
      min-height: 100vh;
      padding: 20px 0;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: #ddd;
      text-decoration: none;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: #444;
      color: #fff;
    }

    /* Main content */
    .main {
      flex: 1;
      padding: 20px;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .header h1 {
      margin: 0;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 30px;
    }
    .card {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }
    .card i {
      font-size: 30px;
      margin-bottom: 10px;
      color: #444;
    }
    .card h3 {
      margin: 0;
      font-size: 22px;
    }
    .card p {
      color: #777;
      margin: 5px 0 0;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #222;
      color: #fff;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#"><i class='bx bx-home'></i> Dashboard</a>
    <a href="#"><i class='bx bx-user'></i>Total Users</a>
    <a href="#"><i class='bx bx-calendar'></i> Bookings</a>
    <a href="workouts.php"><i class='bx bx-dumbbell'></i> Workouts</a>

    <a href="#"><i class='bx bx-bar-chart'></i> Reports</a>
    <a href="#"><i class='bx bx-user'></i>User Profile</a>
    <a href="nutrition.php"><i class='bx bx-dish'></i> Nutrition Plans</a>

    <a href="#"><i class='bx bx-cog'></i> Settings</a>
  </div>

  <!-- Main content -->
  <div class="main">
    <div class="header">
      <!-- ✅ Now shows correct full name -->
      <h1>Welcome, <?php echo htmlspecialchars($adminName); ?> 👋</h1>
      <form action="admin_login.html" method="post">
      <button type="submit" class="logout-btn">Logout</button>
      </form>
    </div>

    <!-- Cards Section (you can add stats later) -->
    <div class="cards">
      <div class="card">
        <i class='bx bx-user'></i>
        <h3>150</h3>
        <p>Total Users</p>
      </div>
      <div class="card">
        <i class='bx bx-calendar'></i>
        <h3>85</h3>
        <p>Total Bookings</p>
      </div>
      <div class="card">
        <i class='bx bx-dumbbell'></i>
        <h3>40</h3>
        <p>Workouts</p>
      </div>
      <div class="card">
        <i class='bx bx-bar-chart'></i>
        <h3>12</h3>
        <p>Reports</p>
      </div>
    </div>

    <!-- ✅ Bookings Table -->
    <div class="card">
      <h2>User Bookings</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>User ID</th>
          <th>Name</th>
          <th>Age</th>
          <th>Session Date</th>
          <th>Session Time</th>
          <th>Gym Type</th>
          <th>Booked At</th>
        </tr>
        <?php while($row = $bookings->fetch_assoc()) { ?>
          <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['user_id']; ?></td>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= $row['age']; ?></td>
            <td><?= $row['session_date']; ?></td>
            <td><?= $row['session_time']; ?></td>
            <td><?= htmlspecialchars($row['gym_type']); ?></td>
            <td><?= $row['created_at']; ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</body>
</html>
