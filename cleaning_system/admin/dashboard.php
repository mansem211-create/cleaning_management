<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') { header("Location: ../index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="sidebar">
    <!-- Insert Logo Here -->
    <div class="logo-area" style="padding: 10px; text-align: center;">
        <img src="../assets/img/image_83f513.png" alt="KPMIM Logo" style="width: 100%; max-width: 150px; margin-bottom: 15px;">
    </div>

    <h3>Admin Panel</h3>
    <a href="dashboard.php" class="nav-item active">🏠 Dashboard</a>
    <a href="manage_users.php" class="nav-item">👥 Manage Users</a>
    <a href="manage_schedules.php" class="nav-item">📅 Manage Schedules</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
</div>
    <div class="main-content">
        <div class="page-header">
            <h1>Welcome, <?php echo $_SESSION['full_name']; ?></h1>
            <p>Admin Overview Dashboard</p>
        </div>
        <div class="fluent-grid">
            <div class="fluent-card" onclick="window.location.href='manage_users.php'" style="cursor: pointer;">
                <h3 style="font-size: 24px; margin-bottom: 10px;">👥 Manage Users</h3>
                <p>Add, edit, or remove system users.</p>
            </div>
            <div class="fluent-card" onclick="window.location.href='manage_schedules.php'" style="cursor: pointer;">
                <h3 style="font-size: 24px; margin-bottom: 10px;">📅 Manage Schedules</h3>
                <p>Configure master cleaning schedules.</p>
            </div>
        </div>
    </div>
</body>
</html>