<?php
// File: EcommerceProject/src/pages/checkout.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: products.php');
    exit;
}

$base_url = '../..';
$page_title = "Checkout - Connect Market";
require_once '../../db_connect.php'; 
require_once '../components/_breadcrumbs.php';
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
        <?php generate_breadcrumbs(); ?>
        <section class="page-section checkout-section">
            <div class="container">
                <div class="checkout-header">
                    <h1 class="section-title4">Secure Checkout</h1>
                    <div class="checkout-progress-bar">
                        <div class="progress-step active">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Cart</span>
                        </div>
                        <div class="progress-step active">
                            <i class="fas fa-truck"></i>
                            <span>Shipping</span>
                        </div>
                        <div class="progress-step">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment</span>
                        </div>
                    </div>
                </div>

                <div class="checkout-grid-modern">
                    <div class="shipping-details-form">
                        <h2 class="form-section-title"><i class="fas fa-map-marker-alt"></i> Shipping Address</h2>
                        <form action="../../process_order.php" method="POST" id="checkout-form">
                            <div class="form-group">
                                <label for="fullname">Full Name</label>
                                <input type="text" id="fullname" name="fullname" placeholder="Vidal Esono" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="you@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Street Address</label>
                                <input type="text" id="address" name="address" placeholder="123 Market St" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" name="city" placeholder="Pretoria" required>
                                </div>
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" id="postal_code" name="postal_code" placeholder="0001" required>
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="notes">Order Notes (Optional)</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Special instructions for delivery..."></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="order-summary-modern">
                        <h2 class="form-section-title"><i class="fas fa-box-open"></i> Your Order</h2>
                        <div class="order-summary-items">
                            <?php 
                                $grandTotal = 0;
                                foreach ($_SESSION['cart'] as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $grandTotal += $subtotal;
                            ?>
                                <div class="summary-item">
                                    <div class="summary-item-image">
                                        <img src="<?php echo $base_url . '/' . htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <span class="summary-item-quantity"><?php echo $item['quantity']; ?></span>
                                    </div>
                                    <div class="summary-item-details">
                                        <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                    <span class="summary-item-price">R<?php echo number_format($subtotal, 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="order-summary-total">
                            <span>Total</span>
                            <strong>R<?php echo number_format($grandTotal, 2); ?></strong>
                        </div>
                        <button type="submit" form="checkout-form" class="btn btn-primary btn-place-order">
                            <i class="fas fa-lock"></i> Place Secure Order
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>
