<?php
// File: EcommerceProject/src/pages/login.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect them from login page
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // If there's a redirect URL, go there, otherwise to index.
    // This handles cases where a logged-in user somehow lands on login.php with a redirect param.
    if (isset($_GET['redirect'])) {
        header("Location: ../../" . urldecode($_GET['redirect'])); // Adjust path relative to root
        exit();
    }
    header("Location: ../../index.php"); // Adjust path to your home page
    exit();
}

// Capture the redirect URL from GET parameter if the user was sent here before logging in
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_url_after_login'] = urldecode($_GET['redirect']);
}

$base_url = '../..'; // Relative path from src/pages/ to project root
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Connect Market</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../styles/style.css"> 
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon"> 
</head>
<body>

    <?php include '../components/header/_header.php'; ?> 

    <main class="site-main auth-page">
        <section class="auth-section">
            <div class="container">
                <div class="auth-form-container">
                    <h1 class="section-title auth-title">Login to ConnectMarket</h1>

                    <?php
                    if (isset($_GET['message'])) {
                        $status_type = (isset($_GET['status']) && $_GET['status'] === 'regsuccess') ? 'success' : 'error';
                        echo '<p class="auth-message ' . $status_type . '">' . htmlspecialchars(urldecode($_GET['message'])) . '</p>';
                    }
                    ?>

                    <form action="../../login_handler.php" method="POST" class="auth-form"> 
                        <div class="form-group">
                            <label for="email_or_username">Email or Username</label>
                            <input type="text" id="email_or_username" name="email_or_username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group form-group-inline">
                            <input type="checkbox" id="remember_me" name="remember_me">
                            <label for="remember_me">Remember Me</label> 
                        </div>
                        <button type="submit" class="btn btn-primary btn-submit-auth">Login</button>
                        <div class="auth-links">
                            <a href="#">Forgot Password?</a> 
                            <p>Don't have an account? <a href="register.php">Sign Up</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <div id="footer-placeholder"></div>

     <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initializeDynamicContentScripts === 'function') {
            window.initializeDynamicContentScripts();
        }
        function loadHTML(filePath, placeholderId, callback) {
            fetch(filePath)
                .then(response => response.ok ? response.text() : Promise.reject(`Failed: ${filePath}`))
                .then(data => {
                    const el = document.getElementById(placeholderId);
                    if (el) el.innerHTML = data;
                    if (callback) callback();
                })
                .catch(error => console.error(`Error loading HTML:`, error));
        }
        
    });
    </script>
    <script src="../scripts/script.js" defer></script> 
    <?php include '../components/footer/_footer.php'; ?>
?>
</body>
</html>