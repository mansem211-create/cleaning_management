<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Cleaner') { header("Location: ../index.php"); exit(); }

$cleaner_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$current_time = date('H:i:s');

$stmt = $conn->prepare("SELECT * FROM attendance WHERE cleaner_id = ? AND attendance_date = ?");
$stmt->bind_param("is", $cleaner_id, $today);
$stmt->execute();
$attendance = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['clock_in']) && !$attendance) {
        $stmt = $conn->prepare("INSERT INTO attendance (cleaner_id, attendance_date, clock_in, status) VALUES (?, ?, ?, 'Present')");
        $stmt->bind_param("iss", $cleaner_id, $today, $current_time);
        $stmt->execute();
        header("Refresh:0"); 
    } elseif (isset($_POST['clock_out']) && $attendance && empty($attendance['clock_out'])) {
        $stmt = $conn->prepare("UPDATE attendance SET clock_out = ? WHERE attendance_id = ?");
        $stmt->bind_param("si", $current_time, $attendance['attendance_id']);
        $stmt->execute();
        header("Refresh:0");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 <div class="sidebar">
    <!-- Insert Logo Here -->
    <div class="logo-area" style="padding: 10px; text-align: center;">
        <img src="../assets/img/image_83f513.png" alt="KPMIM Logo" style="width: 100%; max-width: 150px; margin-bottom: 15px;">
    </div>
    <h3>Staff Workspace</h3>
    <a href="dashboard.php" class="nav-item">📋 My Tasks</a>
    <a href="attendance.php" class="nav-item active">🕒 Attendance</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="page-header"><h1>Daily Attendance</h1></div>
        <div class="fluent-card" style="text-align: center; padding: 50px;">
            <div id="clock" style="font-size: 48px; color: var(--fluent-primary); margin-bottom: 20px;"></div>
            <p style="margin-bottom: 30px; font-size: 18px;">Date: <?php echo date("l, d F Y"); ?></p>
            <form method="POST">
                <?php if (!$attendance): ?>
                    <button type="submit" name="clock_in" class="btn-fluent" style="padding: 15px 40px; font-size: 18px;">Clock In</button>
                <?php elseif (empty($attendance['clock_out'])): ?>
                    <p style="color: green; margin-bottom: 15px;">Clocked in at: <strong><?php echo $attendance['clock_in']; ?></strong></p>
                    <button type="submit" name="clock_out" class="btn-fluent" style="background: #D13438; padding: 15px 40px;">Clock Out</button>
                <?php else: ?>
                    <p style="color: #107C10; font-size: 18px; font-weight: 600;">Shift Completed</p>
                    <p>In: <?php echo $attendance['clock_in']; ?> | Out: <?php echo $attendance['clock_out']; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <script>
        function updateClock() {
            var d = new Date();
            document.getElementById('clock').textContent = d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0') + ':' + d.getSeconds().toString().padStart(2,'0');
        }
        setInterval(updateClock, 1000); updateClock();
    </script>
</body>
</html>