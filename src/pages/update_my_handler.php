<?php
// src/pages/update_my_product_handler.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';

// Security: User must be logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['product_name'] ?? '');
    // ... (rest of the fields)

    // Security Check: Verify the product belongs to the current user before updating
    $stmt_check = $conn->prepare("SELECT image_path FROM products WHERE id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $product_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows !== 1) {
        // Product does not exist or user does not own it.
        header("Location: my_products.php?message=" . urlencode("Update failed: Access denied."));
        exit();
    }
    $product_data = $result_check->fetch_assoc();
    $stmt_check->close();

    // The rest of the update logic is the same as the admin handler
    $price = trim($_POST['product_price'] ?? '');
    $description = trim($_POST['product_description'] ?? '');
    $category = trim($_POST['product_category'] ?? '');

    $image_sql_path = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        // Delete old image
        $old_image_path = '../../' . $product_data['image_path'];
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }
        // Process new image
        $target_dir = "../../uploads/product_images/";
        $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        $unique_image_name = uniqid('product_', true) . '.' . $image_file_type;
        $target_file = $target_dir . $unique_image_name;
        $image_sql_path = "uploads/product_images/" . $unique_image_name;
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
    }

    if (!empty($image_sql_path)) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_path = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        $stmt_update->bind_param("ssdssi", $name, $description, $price, $category, $image_sql_path, $product_id);
    } else {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        $stmt_update->bind_param("ssdsi", $name, $description, $price, $category, $product_id);
    }

    if ($stmt_update->execute()) {
        header("Location: my_products.php?message=" . urlencode("Product updated successfully."));
    } else {
        header("Location: edit_my_product.php?id=" . $product_id . "&error=" . urlencode("Database update failed."));
    }
    exit();
}
?>
