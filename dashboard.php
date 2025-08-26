<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.html"); // Kick out if not logged in
    exit();
}
?>

<div class="profile">
  <div class="pic"></div>
  <div class="meta">
    <div class="name"><?php echo $_SESSION['user_name']; ?></div>
    <div class="role">Member</div>
  </div>
</div>
