<?php
// src/pages/dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page, which is in the same directory.
    header("Location: login.php?message=" . urlencode("Please log in to access the dashboard."));
    exit();
}

$base_url = '../..';
$page_title = "My Dashboard - ConnectMarket";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
</head>
<body>
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">
        <section class="user-dashboard-section">
            <div class="container">
                <div class="user-dashboard-intro">
                    <h1 class="section-title4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <p>This is your personal dashboard. Manage your listings, track your orders, and more.</p>
                </div>

                <div class="dashboard-features-grid">
                    <div class="dashboard-feature-card">
                        <i class="fas fa-box-open card-icon"></i>
                        <div>
                            <h3>My Products</h3>
                            <p>View, edit, or delete the products you have listed for sale.</p>
                        </div>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/my_products.php" class="btn btn-primary">Manage My Products</a>
                    </div>
                    <div class="dashboard-feature-card">
                        <i class="fas fa-receipt card-icon"></i>
                        <div>
                            <h3>My Orders</h3>
                            <p>Review your purchase history and view details of your past orders.</p>
                        </div>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/my_orders.php" class="btn btn-primary">View My Orders</a>
                    </div>
                     <div class="dashboard-feature-card">
                        <i class="fas fa-plus-circle card-icon"></i>
                        <div>
                            <h3>Sell an Item</h3>
                            <p>Ready to sell something new? List a new product on the marketplace.</p>
                        </div>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/upload_product.php" class="btn btn-primary">Upload a Product</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>