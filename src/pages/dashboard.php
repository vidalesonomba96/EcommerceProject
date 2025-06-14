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
    <style>
        /* Re-using styles from admin dashboard for consistency */
        .dashboard-section {
            padding: 4rem 0;
            background-color: #f9fafb;
        }
        body.dark-mode .dashboard-section { background-color: var(--dm-bg-secondary); }

        .dashboard-intro { text-align: center; margin-bottom: 3rem; }
        .dashboard-intro .section-title4 { margin-bottom: 0.5rem; }
        body.dark-mode .dashboard-intro .section-title4 { color: var(--dm-text-primary); }
        body.dark-mode .dashboard-intro p { color: var(--dm-text-secondary); }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        .feature-card {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 0.75rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .feature-card h3 { font-size: 1.5rem; margin-bottom: 0.75rem; color: #374151; }
        .feature-card p { color: #6b7280; font-size: 0.95rem; margin-bottom: 1.5rem; flex-grow: 1; }
        .feature-card .btn {
            background-color: #4f46e5;
            color: #fff;
            font-weight: bold;
            align-self: center;
            width: fit-content;
        }
        body.dark-mode .feature-card {
            background-color: var(--dm-bg-tertiary);
            box-shadow: 0 4px 12px var(--dm-shadow-color);
        }
        body.dark-mode .feature-card h3 { color: var(--dm-text-primary); }
        body.dark-mode .feature-card p { color: var(--dm-text-secondary); }

    </style>
</head>
<body>
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">
        <section class="dashboard-section">
            <div class="container">
                <div class="dashboard-intro">
                    <h1 class="section-title4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                    <p>This is your personal dashboard. Manage your listings, track your orders, and more.</p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div>
                            <h3><i class="fas fa-box-open"></i> My Products</h3>
                            <p>View, edit, or delete the products you have listed for sale.</p>
                        </div>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/my_products.php" class="btn">Manage My Products</a>
                    </div>
                    <div class="feature-card">
                        <div>
                            <h3><i class="fas fa-receipt"></i> My Orders</h3>
                            <p>Review your purchase history and view details of your past orders.</p>
                        </div>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/my_orders.php" class="btn">View My Orders</a>
                    </div>
                     <div class="feature-card">
                        <div>
                            <h3><i class="fas fa-plus-circle"></i> Sell an Item</h3>
                            <p>Ready to sell something new? List a new product on the marketplace.</p>
                        </div>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/upload_product.php" class="btn">Upload a Product</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>
