<?php
// admin/index.php
session_start();
if (isset($_SESSION['logged_in']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: dashboard.php");
    exit();
}
$base_url = '..'; // To access root files from admin/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ConnectMarket</title>
    <link rel="stylesheet" href="../src/styles/style.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body class="auth-page">
    <main class="site-main">
        <div class="auth-form-container">
            <h1 class="auth-title">Admin Login</h1>
            <?php
            if (isset($_GET['message'])) {
                echo '<p class="auth-message error">' . htmlspecialchars(urldecode($_GET['message'])) . '</p>';
            }
            ?>
            <form action="admin_login_handler.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email_or_username">Email or Username</label>
                    <input type="text" id="email_or_username" name="email_or_username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-submit-auth">Login</button>
            </form>
        </div>
    </main>
</body>
</html>