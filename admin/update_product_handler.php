<?php
// admin/update_product_handler.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['product_description'] ?? '');
    $price = trim($_POST['product_price'] ?? '');
    $category = trim($_POST['product_category'] ?? '');

    if ($product_id === 0 || empty($name) || empty($description) || empty($price) || empty($category)) {
        header("Location: edit_product.php?id=" . $product_id . "&error=" . urlencode("All fields are required."));
        exit();
    }

    $image_sql_path = '';
    // Check if a new image was uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        
        // Delete old image first
        $stmt_img = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt_img->bind_param("i", $product_id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();
        if ($row_img = $result_img->fetch_assoc()) {
            $old_image_path = '../' . $row_img['image_path'];
            if (file_exists($old_image_path) && is_file($old_image_path)) {
                unlink($old_image_path);
            }
        }
        $stmt_img->close();

        // Process new image
        $target_dir = "../uploads/product_images/";
        $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        $unique_image_name = uniqid('product_', true) . '.' . $image_file_type;
        $target_file = $target_dir . $unique_image_name;
        
        // The SQL path does NOT include the leading '../'
        $image_sql_path = "uploads/product_images/" . $unique_image_name;

        if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            header("Location: edit_product.php?id=" . $product_id . "&error=" . urlencode("Failed to upload new image."));
            exit();
        }
    }

    // Prepare SQL update
    if (!empty($image_sql_path)) {
        // If new image was uploaded, update the image path as well
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_path = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        $stmt_update->bind_param("ssdssi", $name, $description, $price, $category, $image_sql_path, $product_id);
    } else {
        // Otherwise, update everything except the image path
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql);
        $stmt_update->bind_param("ssdsi", $name, $description, $price, $category, $product_id);
    }

    if ($stmt_update->execute()) {
        header("Location: manage_products.php?message=" . urlencode("Product updated successfully."));
    } else {
        header("Location: edit_product.php?id=" . $product_id . "&error=" . urlencode("Database update failed."));
    }
    $stmt_update->close();
    $conn->close();
    exit();

} else {
    // Redirect if not a POST request
    header("Location: manage_products.php");
    exit();
}
?>
