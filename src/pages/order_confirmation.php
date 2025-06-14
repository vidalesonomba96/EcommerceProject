<?php
// src/pages/order_confirmation.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';
$base_url = '../..';

// Security check: ensure there is a last_order_id in the session
// This prevents users from navigating to this page directly.
if (!isset($_SESSION['last_order_id'])) {
    header("Location: " . $base_url . "/index.php");
    exit();
}

$order_id = $_SESSION['last_order_id'];
unset($_SESSION['last_order_id']); // Unset it so the page can't be refreshed to show the same order

// Fetch the order details to display to the user
$stmt = $conn->prepare("SELECT o.id, o.total_amount, o.created_at, GROUP_CONCAT(CONCAT(p.name, ' x', oi.quantity) SEPARATOR ', ') AS items_summary 
                       FROM orders o
                       JOIN order_items oi ON o.id = oi.order_id
                       JOIN products p ON oi.product_id = p.id
                       WHERE o.id = ?
                       GROUP BY o.id");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    // If for some reason the order can't be found, redirect home
    header("Location: " . $base_url . "/index.php");
    exit();
}

$page_title = "Order Confirmation - ConnectMarket";
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
        <section class="confirmation-section" style="padding: 4rem 0;">
            <div class="container confirmation-container">
                <h1><i class="fas fa-check-circle"></i> Thank You For Your Order!</h1>
                <p>Your order has been placed successfully. A confirmation email will be sent to you shortly.</p>
                
                <div class="order-details">
                    <h3>Order Summary</h3>
                    <p><strong>Order Number:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
                    <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($order['created_at'])); ?></p>
                    <p><strong>Total Amount:</strong> R<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Items:</strong> <?php echo htmlspecialchars($order['items_summary']); ?></p>
                </div>
                
                <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/products.php" class="btn btn-primary" style="margin-top: 2rem;">Continue Shopping</a>
            </div>
        </section>
    </main>
    
    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>
