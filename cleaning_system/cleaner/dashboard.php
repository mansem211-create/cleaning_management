<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Cleaner') { header("Location: ../index.php"); exit(); }

$cleaner_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task'])) {
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE task_id = ? AND assigned_cleaner = ?");
    $stmt->bind_param("sii", $_POST['status'], $_POST['task_id'], $cleaner_id);
    if ($stmt->execute()) $message = "<div style='color: green; margin-bottom: 15px;'>✔ Status updated!</div>";
    $stmt->close();
}
$tasks = $conn->query("SELECT t.task_id, t.task_name, t.task_description, t.deadline, t.status, u.full_name as supervisor_name FROM tasks t JOIN users u ON t.assigned_by = u.user_id WHERE t.assigned_cleaner = $cleaner_id ORDER BY t.deadline ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cleaner Workspace</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 <div class="sidebar">
    <!-- Insert Logo Here -->
    <div class="logo-area" style="padding: 10px; text-align: center;">
        <img src="../assets/img/image_83f513.png" alt="KPMIM Logo" style="width: 100%; max-width: 150px; margin-bottom: 15px;">
    </div>
    <h3>Staff Workspace</h3>
    <a href="dashboard.php" class="nav-item active">📋 My Tasks</a>
    <a href="attendance.php" class="nav-item">🕒 Attendance</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="page-header"><h1>Welcome, <?php echo $_SESSION['full_name']; ?></h1></div>
        <?php echo $message; ?>
        <div class="fluent-card">
            <table class="fluent-table">
                <thead><tr><th>Task Name</th><th>Description</th><th>Deadline</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($row = $tasks->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo $row['task_name']; ?></td>
                        <td><?php echo $row['task_description']; ?></td>
                        <td><?php echo date("d M Y", strtotime($row['deadline'])); ?></td>
                        <td>
                            <?php if($row['status'] == 'Pending'): ?><span style="background:#FFF4CE; color:#9D5D00; padding:4px 10px; border-radius:12px; font-size:12px;">Pending</span>
                            <?php elseif($row['status'] == 'In Progress'): ?><span style="background:#CCE8FF; color:#004E8C; padding:4px 10px; border-radius:12px; font-size:12px;">In Progress</span>
                            <?php else: ?><span style="background:#DFF6DD; color:#107C10; padding:4px 10px; border-radius:12px; font-size:12px;">Completed</span><?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] != 'Completed'): ?>
                            <form method="POST" style="display:flex; gap:8px;">
                                <input type="hidden" name="task_id" value="<?php echo $row['task_id']; ?>">
                                <select name="status" style="padding:6px; border:1px solid #CCC; border-radius:4px;">
                                    <option value="In Progress" <?php if($row['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                                <button type="submit" name="update_task" class="btn-fluent" style="padding:6px 12px;">Update</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>