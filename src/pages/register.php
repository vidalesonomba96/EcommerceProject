<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// If user is already logged in, redirect them from registration page
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: ../../index.php"); // Adjust path to your home page
    exit();
}
$base_url = '../..'; // Relative path from src/pages/ to project root
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Connect Market</title>
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
                    <h1 class="section-title auth-title">Create Your Account</h1>

                    <?php
                    if (isset($_GET['message'])) {
                        $status_type = (isset($_GET['status']) && $_GET['status'] === 'regsuccess') ? 'success' : 'error';
                        echo '<p class="auth-message ' . $status_type . '">' . htmlspecialchars(urldecode($_GET['message'])) . '</p>';
                    }
                    ?>

                    <form action="../../register_handler.php" method="POST" class="auth-form"> 
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password (min 8 characters)</label>
                            <input type="password" id="password" name="password" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                         <div class="form-group form-group-inline">
                            <input type="checkbox" id="agree_terms" name="agree_terms" required>
                            <label for="agree_terms">I agree to the <a href="<?php echo htmlspecialchars($base_url); ?>/terms.php" target="_blank">Terms & Conditions</a></label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-submit-auth">Create Account</button>
                        <div class="auth-links">
                            <p>Already have an account? <a href="login.php">Login</a></p>
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

</body>
</html>