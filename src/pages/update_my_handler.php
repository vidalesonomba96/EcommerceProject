<?php
// --- DEBUGGING START ---
// These lines will force PHP to show any fatal errors on the screen.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- DEBUGGING END ---

// src/pages/update_my_product_handler.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['product_name'] ?? '');
    $price = trim($_POST['product_price'] ?? '');
    $discount_price = trim($_POST['discount_price'] ?? ''); // Get discount price
    $description = trim($_POST['product_description'] ?? '');
    $category = trim($_POST['product_category'] ?? '');

    // Handle optional discount price: if it's empty or 0, set it to NULL for the database
    $discount_price = !empty($discount_price) && (float)$discount_price > 0 ? $discount_price : NULL;

    // --- Validation ---
    if ($discount_price !== NULL && (!is_numeric($discount_price) || $discount_price < 0)) {
        header("Location: edit_my_product.php?id=" . $product_id . "&error=" . urlencode("Invalid discount price."));
        exit();
    }
     if ($discount_price !== NULL && (float)$discount_price >= (float)$price) {
        header("Location: edit_my_product.php?id=" . $product_id . "&error=" . urlencode("Discount price must be less than the regular price."));
        exit();
    }
    // --- End Validation ---

    // Security Check: Verify the product belongs to the current user before updating
    $stmt_check = $conn->prepare("SELECT image_path FROM products WHERE id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $product_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows !== 1) {
        header("Location: my_products.php?message=" . urlencode("Update failed: Access denied."));
        exit();
    }
    $product_data = $result_check->fetch_assoc();
    $stmt_check->close();

    $image_sql_path = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        if (!empty($product_data['image_path']) && file_exists('../../' . $product_data['image_path'])) {
            unlink('../../' . $product_data['image_path']);
        }
        $target_dir = "../../uploads/product_images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        $unique_image_name = uniqid('product_', true) . '.' . $image_file_type;
        $target_file = $target_dir . $unique_image_name;
        $image_sql_path = "uploads/product_images/" . $unique_image_name;
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
    }

    // Prepare the SQL statement, now including discount_price
    if (!empty($image_sql_path)) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, discount_price = ?, category = ?, image_path = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        // Bind parameters, including discount_price (ssddssi)
        $stmt_update->bind_param("ssddssi", $name, $description, $price, $discount_price, $category, $image_sql_path, $product_id);
    } else {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, discount_price = ?, category = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        // Bind parameters, including discount_price (ssddsi)
        $stmt_update->bind_param("ssddsi", $name, $description, $price, $discount_price, $category, $product_id);
    }

    if ($stmt_update->execute()) {
        header("Location: my_products.php?message=" . urlencode("Product updated successfully."));
    } else {
        header("Location: edit_my_product.php?id=" . $product_id . "&error=" . urlencode("Database update failed: " . $stmt_update->error));
    }
    
    $stmt_update->close();
    $conn->close();
    exit();
}
?>