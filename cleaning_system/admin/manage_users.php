<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') { header("Location: ../index.php"); exit(); }

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, role, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $_POST['username'], $_POST['password'], $_POST['full_name'], $_POST['role'], $_POST['email']);
    if ($stmt->execute()) $message = "<div style='color: green; margin-bottom: 15px;'>✔ User added successfully!</div>";
    $stmt->close();
}
$users = $conn->query("SELECT user_id, username, full_name, role, email FROM users ORDER BY user_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 <div class="sidebar">
    <!-- Insert Logo Here -->
    <div class="logo-area" style="padding: 10px; text-align: center;">
        <img src="../assets/img/image_83f513.png" alt="KPMIM Logo" style="width: 100%; max-width: 150px; margin-bottom: 15px;">
    </div>
    <h3>Admin Panel</h3>
    <a href="dashboard.php" class="nav-item">🏠 Dashboard</a>
    <a href="manage_users.php" class="nav-item active">👥 Manage Users</a>
    <a href="manage_schedules.php" class="nav-item">📅 Manage Schedules</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="page-header">
            <h1>Manage System Users</h1>
        </div>
        <?php echo $message; ?>
        <div class="fluent-grid" style="grid-template-columns: 1fr 2fr;">
            <div class="fluent-card">
                <form class="fluent-form" method="POST" action="">
                    <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required></div>
                    <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="Cleaner">Cleaner</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Management">Management</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <button type="submit" name="add_user" class="btn-fluent">Create Account</button>
                </form>
            </div>
            <div>
                <table class="fluent-table">
                    <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Email</th></tr></thead>
                    <tbody>
                        <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['user_id']; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><span style="background:#E1DFDD; padding:4px 8px; border-radius:12px; font-size:12px;"><?php echo $row['role']; ?></span></td>
                            <td><?php echo $row['email']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>