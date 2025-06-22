<?php
// EcommerceProject/src/pages/cart.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '../..';
$page_title = "Your Shopping Cart - Connect Market";
require_once '../../db_connect.php'; // Needed for breadcrumbs if it queries DB
require_once '../components/_breadcrumbs.php'; // Include breadcrumbs
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include '../components/header/_header.php'; ?>
    <main class="site-main">
        <?php generate_breadcrumbs(); // Display breadcrumbs ?>
        <section class="cart-page-section">
            <div class="container cart-page-container">
                <h1 class="section-title4">Your Shopping Cart</h1>
                <div id="cart-items-container">
                    <p style="text-align:center; padding: 2rem 0;">Loading your cart...</p>
                </div>
                <div class="cart-summary" style="display: none;">
                    <h2>Cart Summary</h2>
                    <p><span>Total Unique Items:</span> <span id="cart-total-items">0</span></p>
                    <p><span>Grand Total:</span> <strong id="cart-grand-total">R0.00</strong></p>
                    <a href="checkout.php" class="btn btn-primary btn-checkout">Proceed to Checkout</a>
                    <p style="text-align:center; margin-top: 1rem;"><a href="products.php">Continue Shopping</a></p>
                </div>
            </div>
        </section>
    </main>
    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>