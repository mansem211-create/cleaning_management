<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') { header("Location: ../index.php"); exit(); }

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_schedule'])) {
    $stmt = $conn->prepare("INSERT INTO schedules (task_id, schedule_date, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_POST['task_id'], $_POST['schedule_date'], $_POST['start_time'], $_POST['end_time']);
    if ($stmt->execute()) $message = "<div style='color: green; margin-bottom: 15px;'>✔ Schedule created!</div>";
    $stmt->close();
}
$tasks = $conn->query("SELECT task_id, task_name FROM tasks WHERE status != 'Completed'");
$schedules = $conn->query("SELECT s.schedule_date, s.start_time, s.end_time, t.task_name, u.full_name as cleaner_name FROM schedules s JOIN tasks t ON s.task_id = t.task_id JOIN users u ON t.assigned_cleaner = u.user_id ORDER BY s.schedule_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Schedules</title>
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
    <a href="manage_users.php" class="nav-item">👥 Manage Users</a>
    <a href="manage_schedules.php" class="nav-item active">📅 Manage Schedules</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="page-header"><h1>Master Schedule</h1></div>
        <?php echo $message; ?>
        <div class="fluent-grid" style="grid-template-columns: 1fr 2fr;">
            <div class="fluent-card">
                <form class="fluent-form" method="POST">
                    <div class="form-group">
                        <label>Select Task</label>
                        <select name="task_id" required>
                            <?php while($t = $tasks->fetch_assoc()): ?><option value="<?php echo $t['task_id']; ?>"><?php echo $t['task_name']; ?></option><?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Date</label><input type="date" name="schedule_date" required></div>
                    <div class="form-group"><label>Start Time</label><input type="time" name="start_time" required></div>
                    <div class="form-group"><label>End Time</label><input type="time" name="end_time" required></div>
                    <button type="submit" name="add_schedule" class="btn-fluent">Save Schedule</button>
                </form>
            </div>
            <div>
                <table class="fluent-table">
                    <thead><tr><th>Date</th><th>Task</th><th>Staff</th><th>Shift Time</th></tr></thead>
                    <tbody>
                        <?php while($row = $schedules->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("d M Y", strtotime($row['schedule_date'])); ?></td>
                            <td><?php echo $row['task_name']; ?></td>
                            <td><?php echo $row['cleaner_name']; ?></td>
                            <td><?php echo date("h:i A", strtotime($row['start_time'])) . ' - ' . date("h:i A", strtotime($row['end_time'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>