<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Supervisor') { header("Location: ../index.php"); exit(); }

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_task'])) {
    $stmt = $conn->prepare("INSERT INTO tasks (task_name, task_description, assigned_cleaner, assigned_by, deadline) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $_POST['task_name'], $_POST['description'], $_POST['cleaner_id'], $_SESSION['user_id'], $_POST['deadline']);
    if ($stmt->execute()) $message = "<div style='color: green; margin-bottom: 15px;'>✔ Task assigned successfully!</div>";
    $stmt->close();
}
$cleaners = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'Cleaner'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Tasks</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 <div class="sidebar">
    <!-- Insert Logo Here -->
    <div class="logo-area" style="padding: 10px; text-align: center;">
        <img src="../assets/img/image_83f513.png" alt="KPMIM Logo" style="width: 100%; max-width: 150px; margin-bottom: 15px;">
    </div>
    <h3>Supervisor Tools</h3>
    <a href="dashboard.php" class="nav-item">📊 Real-Time Monitoring</a>
    <a href="assign_tasks.php" class="nav-item active">📝 Assign Tasks</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="page-header"><h1>Task Assignment</h1></div>
        <?php echo $message; ?>
        <div class="fluent-card" style="max-width: 600px;">
            <form class="fluent-form" method="POST">
                <div class="form-group"><label>Task Name</label><input type="text" name="task_name" required></div>
                <div class="form-group"><label>Description</label><input type="text" name="description"></div>
                <div class="form-group">
                    <label>Assign to</label>
                    <select name="cleaner_id" required>
                        <?php while($row = $cleaners->fetch_assoc()): ?><option value="<?php echo $row['user_id']; ?>"><?php echo $row['full_name']; ?></option><?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group"><label>Deadline</label><input type="date" name="deadline" required></div>
                <button type="submit" name="assign_task" class="btn-fluent">Deploy Task</button>
            </form>
        </div>
    </div>
</body>
</html>