<?php
session_start();
require 'config/db.php';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['full_name'] = $row['full_name'];
        if ($row['role'] == 'Admin') header("Location: admin/dashboard.php");
        else if ($row['role'] == 'Supervisor') header("Location: supervisor/dashboard.php");
        else if ($row['role'] == 'Cleaner') header("Location: cleaner/dashboard.php");
        else if ($row['role'] == 'Management') header("Location: management/dashboard.php");
        exit();
    } else { $error = "Invalid username or password!"; }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Cleaning Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo-area">
                <img src="assets/img/image_83f513.png" alt="KPMIM Logo">
            </div>
            <h2>System Login</h2>
            <?php if($error): ?><p style="color: red; margin-bottom: 10px;"><?php echo $error; ?></p><?php endif; ?>
            <form class="fluent-form" method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-fluent" style="width: 100%;">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>