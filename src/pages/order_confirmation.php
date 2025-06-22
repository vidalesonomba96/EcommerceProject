<?php
// File: EcommerceProject/src/pages/order_confirmation.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if the user accesses this page without placing an order
if (!isset($_SESSION['last_order_id'])) {
    header('Location: ../../index.php');
    exit;
}

$base_url = '../..';
$page_title = "Order Confirmed - Connect Market";
require_once '../../db_connect.php';

$last_order_id = $_SESSION['last_order_id'];

// Fetch the order details to display on the confirmation page
$order = null;
$order_items = [];

$stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt_order->bind_param("i", $last_order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
if ($result_order->num_rows > 0) {
    $order = $result_order->fetch_assoc();

    // Fetch the items associated with this order
    $stmt_items = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt_items->bind_param("i", $last_order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();
    while ($item = $result_items->fetch_assoc()) {
        $order_items[] = $item;
    }
    $stmt_items->close();
}
$stmt_order->close();

// Unset the session variable to prevent users from seeing the confirmation page on refresh
unset($_SESSION['last_order_id']);
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
        <section class="page-section confirmation-section">
            <div class="container">
                <?php if ($order): ?>
                    <div class="confirmation-card">
                        <div class="confirmation-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h1 class="confirmation-title">Thank You For Your Order!</h1>
                        <p class="confirmation-subtitle">Your order has been placed successfully. A confirmation email will be sent to you shortly.</p>
                        <p class="order-id">Order ID: <strong>#<?php echo htmlspecialchars($order['id']); ?></strong></p>
                        
                        <div class="confirmation-details">
                            <h3 class="details-title">Order Summary</h3>
                            <div class="details-items">
                                <?php foreach ($order_items as $item): ?>
                                    <div class="details-item">
                                        <span class="item-name"><?php echo htmlspecialchars($item['name']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</span>
                                        <span class="item-price">R<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="details-total">
                                <span>Total</span>
                                <strong>R<?php echo number_format($order['total_amount'], 2); ?></strong>
                            </div>
                        </div>

                        <div class="confirmation-actions">
                            <a href="<?php echo $base_url; ?>/src/pages/products.php" class="btn btn-secondary">Continue Shopping</a>
                            <a href="<?php echo $base_url; ?>/src/pages/my_orders.php" class="btn btn-primary">View My Orders</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="confirmation-card error">
                        <h1 class="confirmation-title">Something Went Wrong</h1>
                        <p class="confirmation-subtitle">We could not find the details for your recent order. Please check your "My Orders" page or contact support.</p>
                        <div class="confirmation-actions">
                            <a href="<?php echo $base_url; ?>/src/pages/my_orders.php" class="btn btn-primary">Check My Orders</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>