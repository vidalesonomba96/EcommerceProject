<?php
// src/pages/my_orders.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';
$base_url = '../..';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['logged_in'])) {
    header("Location: login.php?message=" . urlencode("Please log in to view your orders."));
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch all order items for the user, joined with product and order details.
// This new query gets all necessary data in one go.
$sql = "SELECT 
            o.id as order_id, 
            o.total_amount, 
            o.created_at,
            p.name as product_name,
            p.image_path,
            oi.quantity,
            oi.price as price_per_item
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC, o.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);

// Group items by order_id in PHP for easy display
$orders = [];
foreach ($order_items as $item) {
    $order_id = $item['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'id' => $order_id,
            'total_amount' => $item['total_amount'],
            'created_at' => $item['created_at'],
            'items' => []
        ];
    }
    $orders[$order_id]['items'][] = $item;
}

$page_title = "My Orders";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ConnectMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
    <style>
        .orders-container {
            padding: 2rem 0;
            max-width: 900px;
            margin: 0 auto;
        }
        .order-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .order-card-header {
            background-color: #f9fafb;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }
        .order-card-header h3 { font-size: 1.1rem; color: #374151; }
        .order-card-header span { font-size: 0.9rem; color: #6b7280; }
        
        .order-items-list {
            padding: 1.5rem;
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        .order-item:last-child { margin-bottom: 0; }
        .order-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-right: 1rem;
        }
        .order-item-details { flex-grow: 1; }
        .order-item-details .item-name { font-weight: 600; color: #374151; }
        .order-item-details .item-qty-price { font-size: 0.85rem; color: #6b7280; }
        .order-item-subtotal { font-weight: 600; color: #374151; }
        
        .order-card-footer {
            background-color: #f9fafb;
            padding: 1rem 1.5rem;
            text-align: right;
            border-top: 1px solid #e5e7eb;
        }
        .order-card-footer strong { font-size: 1.2rem; color: #4F46E5; }

        /* Dark Mode Styles */
        body.dark-mode .order-card { background-color: var(--dm-bg-tertiary); box-shadow: 0 4px 12px var(--dm-shadow-color); }
        body.dark-mode .order-card-header, body.dark-mode .order-card-footer { background-color: var(--dm-bg-secondary); border-color: var(--dm-border-color); }
        body.dark-mode .order-card-header h3, body.dark-mode .order-item-details .item-name, body.dark-mode .order-item-subtotal { color: var(--dm-text-primary); }
        body.dark-mode .order-card-header span, body.dark-mode .order-item-details .item-qty-price { color: var(--dm-text-secondary); }
        body.dark-mode .order-card-footer strong { color: var(--dm-brand-primary); }
    </style>
</head>
<body>
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">
        <div class="orders-container">
            <h1 class="section-title4" style="text-align: center; margin-bottom: 2.5rem;">My Order History</h1>

            <?php if (empty($orders)): ?>
                <p style="text-align: center;">You haven't placed any orders yet. <a href="<?php echo $base_url; ?>/src/pages/products.php">Start shopping!</a></p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-card-header">
                            <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                            <span>Placed on <?php echo date("F j, Y", strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="order-items-list">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <img src="<?php echo $base_url . '/' . htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <div class="order-item-details">
                                        <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <div class="item-qty-price">
                                            <?php echo $item['quantity']; ?> x R<?php echo number_format($item['price_per_item'], 2); ?>
                                        </div>
                                    </div>
                                    <div class="order-item-subtotal">
                                        R<?php echo number_format($item['quantity'] * $item['price_per_item'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="order-card-footer">
                            <strong>Total: R<?php echo number_format($order['total_amount'], 2); ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>
