<?php
// process_order.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

// Security checks: must be a POST request, user logged in, and cart not empty
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION['logged_in']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Basic validation for form fields
if (empty($_POST['fullname']) || empty($_POST['address']) || empty($_POST['city']) || empty($_POST['postal_code'])) {
    header("Location: src/pages/checkout.php?error=" . urlencode("All shipping fields are required."));
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Calculate total one more time on the server-side to ensure accuracy
$total_amount = 0.0;
foreach ($cart as $item) {
    $total_amount += (float)$item['price'] * (int)$item['quantity'];
}

// Use a database transaction to ensure all queries succeed or none do.
$conn->begin_transaction();

try {
    // 1. Insert the main order into the `orders` table
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt_order->bind_param("id", $user_id, $total_amount);
    $stmt_order->execute();
    
    // Get the ID of the order we just inserted
    $order_id = $conn->insert_id;

    // 2. Insert each item from the cart into the `order_items` table
    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $product_id_key => $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $stmt_items->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $stmt_items->execute();
    }
    
    // If all queries were successful, commit the transaction
    $conn->commit();
    
    // 3. Clear the cart and store the order ID for the confirmation page
    unset($_SESSION['cart']);
    $_SESSION['last_order_id'] = $order_id;
    
    // 4. Redirect to the confirmation page
    header("Location: src/pages/order_confirmation.php");
    exit();

} catch (Exception $e) {
    // If any query failed, roll back the transaction
    $conn->rollback();
    
    // Redirect back to checkout with an error message
    error_log("Order processing failed: " . $e->getMessage()); // Log the actual error for debugging
    header("Location: src/pages/checkout.php?error=" . urlencode("An error occurred while processing your order. Please try again."));
    exit();
}
?>
