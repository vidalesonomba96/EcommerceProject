<?php
// src/pages/checkout.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';
$base_url = '../..';

// If cart is empty, redirect to the cart page
if (empty($_SESSION['cart'])) {
    header("Location: " . $base_url . "/src/pages/cart.php");
    exit();
}

// If user is not logged in, redirect them to login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Store the checkout page URL to redirect back to after login
    $_SESSION['redirect_url_after_login'] = 'src/pages/checkout.php';
    $message = "Please log in or register to complete your purchase.";
    header("Location: " . $base_url . "/src/pages/login.php?message=" . urlencode($message));
    exit();
}

// Calculate totals from the cart
$cart = $_SESSION['cart'] ?? [];
$grandTotal = 0.0;
foreach ($cart as $item) {
    $grandTotal += (float)$item['price'] * (int)$item['quantity'];
}

$page_title = "Checkout - ConnectMarket";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
</head>
<body>
    <?php include '../components/header/_header.php'; ?>
    <main class="site-main">
        <section class="checkout-page-section">
            <div class="container checkout-page-container">
                <h1 class="section-title4">Checkout</h1>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="checkout-message">
                        <p><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($base_url); ?>/process_order.php" method="POST" class="checkout-grid">
                    <!-- Customer Details Column -->
                    <div class="customer-details-form">
                        <h2>Shipping Information</h2>
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($_SESSION['fullname'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Street Address</label>
                            <input type="text" id="address" name="address" placeholder="123 Market St" required>
                        </div>
                         <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" placeholder="Johannesburg" required>
                        </div>
                         <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" placeholder="2000" required>
                        </div>
                    </div>

                    <!-- Order Summary Column -->
                    <div class="order-summary-checkout">
                        <h3>Your Order Summary</h3>
                        <?php foreach ($cart as $item): ?>
                            <div class="order-summary-item">
                                <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                <span class="item-qty">x <?php echo htmlspecialchars($item['quantity']); ?></span>
                                <span class="item-price">R<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="order-summary-total">
                            <span>Grand Total</span>
                            <span>R<?php echo number_format($grandTotal, 2); ?></span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-place-order">Place Order</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>
