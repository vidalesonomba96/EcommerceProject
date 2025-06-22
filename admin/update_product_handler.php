<?php
session_start();
require_once '../db_connect.php';
require_once 'admin_auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);
    $name = trim($_POST['product_name']);
    $description = trim($_POST['product_description']);
    $price = trim($_POST['product_price']);
    $discount_price = trim($_POST['discount_price'] ?? ''); // Get discount price
    $category = trim($_POST['product_category']);

    // Handle optional discount price: if empty or 0, set to NULL for the database
    $discount_price = !empty($discount_price) && (float)$discount_price > 0 ? $discount_price : NULL;

    // --- Validation ---
    if ($discount_price !== NULL && (float)$discount_price >= (float)$price) {
        header("Location: edit_product.php?id=" . $product_id . "&error=" . urlencode("Discount price must be less than the regular price."));
        exit();
    }
    // --- End Validation ---

    // Fetch current image path to delete if a new one is uploaded
    $stmt_check = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt_check->bind_param("i", $product_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $product_data = $result_check->fetch_assoc();
    $stmt_check->close();

    $image_sql_path = '';
    // Check if a new image file is uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        // Delete old image if it exists
        if (!empty($product_data['image_path']) && file_exists('../' . $product_data['image_path'])) {
            unlink('../' . $product_data['image_path']);
        }

        // Process and save the new image
        $target_dir = "../uploads/product_images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        $unique_image_name = uniqid('product_', true) . '.' . $image_file_type;
        $target_file = $target_dir . $unique_image_name;
        $image_sql_path = "uploads/product_images/" . $unique_image_name;
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
    }

    // Update the database
    if (!empty($image_sql_path)) {
        // If a new image was uploaded, update the image_path field
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, discount_price = ?, category = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddssi", $name, $description, $price, $discount_price, $category, $image_sql_path, $product_id);
    } else {
        // If no new image, don't update the image_path field
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, discount_price = ?, category = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddsi", $name, $description, $price, $discount_price, $category, $product_id);
    }

    if ($stmt->execute()) {
        header("Location: manage_products.php?message=Product updated successfully.");
    } else {
        header("Location: edit_product.php?id=" . $product_id . "&error=Database update failed.");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>