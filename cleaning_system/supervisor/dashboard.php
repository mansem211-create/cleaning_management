<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Supervisor') { header("Location: ../index.php"); exit(); }
$supervisor_id = $_SESSION['user_id'];

$tasks = $conn->query("SELECT t.task_id, t.task_name, t.status, t.deadline, c.full_name as cleaner_name FROM tasks t JOIN users c ON t.assigned_cleaner = c.user_id WHERE t.assigned_by = $supervisor_id ORDER BY t.deadline ASC");
$stats = $conn->query("SELECT SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress, SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed FROM tasks WHERE assigned_by = $supervisor_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Real-Time Monitoring</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <meta http-equiv="refresh" content="30">
</head>
<body>
 <div class="sidebar">
    <!-- Insert Logo Here -->
    <div class="logo-area" style="padding: 10px; text-align: center;">
        <img src="../assets/img/image_83f513.png" alt="KPMIM Logo" style="width: 100%; max-width: 150px; margin-bottom: 15px;">
    </div>
    <h3>Supervisor Tools</h3>
    <a href="dashboard.php" class="nav-item active">📊 Real-Time Monitoring</a>
    <a href="assign_tasks.php" class="nav-item">📝 Assign Tasks</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="page-header"><h1>Live Operations Dashboard</h1></div>
        <div class="fluent-grid" style="margin-bottom: 30px;">
            <div class="fluent-card" style="border-left: 4px solid #9D5D00;"><h3>Pending</h3><p style="font-size:32px; font-weight:600;"><?php echo $stats['pending'] ?? 0; ?></p></div>
            <div class="fluent-card" style="border-left: 4px solid #004E8C;"><h3>In Progress</h3><p style="font-size:32px; font-weight:600;"><?php echo $stats['in_progress'] ?? 0; ?></p></div>
            <div class="fluent-card" style="border-left: 4px solid #107C10;"><h3>Completed</h3><p style="font-size:32px; font-weight:600;"><?php echo $stats['completed'] ?? 0; ?></p></div>
        </div>
        <div class="fluent-card">
            <table class="fluent-table">
                <thead><tr><th>Task</th><th>Staff</th><th>Deadline</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($row = $tasks->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['task_name']; ?></td>
                        <td><?php echo $row['cleaner_name']; ?></td>
                        <td><?php echo date("d M Y", strtotime($row['deadline'])); ?></td>
                        <td>
                            <?php if($row['status'] == 'Pending'): ?><span style="background:#FFF4CE; color:#9D5D00; padding:4px 10px; border-radius:12px; font-size:12px;">Pending</span>
                            <?php elseif($row['status'] == 'In Progress'): ?><span style="background:#CCE8FF; color:#004E8C; padding:4px 10px; border-radius:12px; font-size:12px;">In Progress</span>
                            <?php else: ?><span style="background:#DFF6DD; color:#107C10; padding:4px 10px; border-radius:12px; font-size:12px;">Completed</span><?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>