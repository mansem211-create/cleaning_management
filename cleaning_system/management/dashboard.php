<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Management') { header("Location: ../index.php"); exit(); }

$performance = $conn->query("SELECT u.full_name, COUNT(t.task_id) as total, SUM(CASE WHEN t.status = 'Completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN t.status = 'Pending' THEN 1 ELSE 0 END) as pending FROM users u LEFT JOIN tasks t ON u.user_id = t.assigned_cleaner WHERE u.role = 'Cleaner' GROUP BY u.user_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Management Report</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
 <div class="sidebar">
    <!-- Insert Logo Here -->
    <div class="logo-area" style="padding: 10px; text-align: center;">
        <img src="../assets/img/image_83f513.png" alt="KPMIM Logo" style="width: 100%; max-width: 150px; margin-bottom: 15px;">
    </div>
    <h3>Management</h3>
    <a href="dashboard.php" class="nav-item active">📈 Performance Reports</a>
    <a href="../logout.php" class="nav-item" style="color: #D13438; margin-top: auto;">🚪 Logout</a>
    </div>
    <div class="main-content">
        <div class="page-header" style="display:flex; justify-content:space-between;">
            <h1>Executive Summary Report</h1>
            <button onclick="window.print()" class="btn-fluent" style="background: #107C10;">🖨️ Print</button>
        </div>
        <div class="fluent-card">
            <table class="fluent-table">
                <thead><tr><th>Staff Name</th><th>Total Tasks</th><th>Completed</th><th>Pending</th><th>Completion Rate</th></tr></thead>
                <tbody>
                    <?php while($row = $performance->fetch_assoc()): 
                        $rate = ($row['total'] > 0) ? round(($row['completed'] / $row['total']) * 100) : 0;
                    ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['total']; ?></td>
                        <td style="color: #107C10; font-weight: bold;"><?php echo $row['completed']; ?></td>
                        <td style="color: #9D5D00; font-weight: bold;"><?php echo $row['pending']; ?></td>
                        <td>
                            <div style="width:100%; background:#E6E6E6; border-radius:4px; height:16px; position:relative;">
                                <div style="width:<?php echo $rate; ?>%; background:<?php echo ($rate == 100) ? '#107C10' : 'var(--fluent-primary)'; ?>; height:100%;"></div>
                                <span style="position:absolute; width:100%; text-align:center; font-size:10px; line-height:16px; top:0; left:0; color:<?php echo ($rate > 50)?'white':'black'; ?>;"><?php echo $rate; ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>