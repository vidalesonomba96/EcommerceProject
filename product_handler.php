<?php
// product_handler.php
session_start();
require_once 'db_connect.php'; // Your database connection

// Authentication check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: src/pages/login.php?message=" . urlencode("Authentication required."));
    exit();
}

$message = '';
$message_type = 'error'; // Default to error

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'upload_product') {
    $user_id = $_SESSION['user_id'];
    $product_name = trim($_POST['product_name'] ?? '');
    $product_description = trim($_POST['product_description'] ?? '');
    $product_price = trim($_POST['product_price'] ?? '');
    $product_category = trim($_POST['product_category'] ?? ''); // Get the selected category

    // Basic Validation
    // Check if category is empty if it's required
    if (empty($product_name) || empty($product_description) || empty($product_price) || empty($product_category) 
        || !isset($_FILES['product_image']) || $_FILES['product_image']['error'] != UPLOAD_ERR_OK) {
        $message = "All fields, including category, and a product image are required.";
    } elseif (!is_numeric($product_price) || $product_price <= 0) {
        $message = "Invalid product price.";
    } else {
        // Image Upload Handling
        $target_dir = "uploads/product_images/"; // Ensure this directory exists and is writable
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true) && !is_dir($target_dir)) { // Check if mkdir failed
                 $message = "Failed to create upload directory.";
                 // No exit here, let it fall through to the error redirect at the end
            }
        }
        
        // Continue only if message is still empty (i.e., directory creation didn't fail or wasn't needed)
        if (empty($message)) {
            $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
            $unique_image_name = uniqid('product_', true) . '.' . $image_file_type;
            $target_file = $target_dir . $unique_image_name;
            $allowed_types = array("jpg", "jpeg", "png", "webp");

            if (!in_array($image_file_type, $allowed_types)) {
                $message = "Sorry, only JPG, JPEG, PNG & WEBP files are allowed for images.";
            } elseif ($_FILES["product_image"]["size"] > 5000000) { // 5MB limit
                $message = "Sorry, your image file is too large (max 5MB).";
            } else {
                if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    // Image uploaded successfully, now insert into database
                    $sql = "INSERT INTO products (user_id, name, description, price, image_path, category) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("issdss", $user_id, $product_name, $product_description, $product_price, $target_file, $product_category);
                        if ($stmt->execute()) {
                            $message = "Product uploaded successfully!";
                            $message_type = 'success';
                            // Redirect to upload_product.php with success message
                            header("Location: src/pages/upload_product.php?status=success&message=" . urlencode($message));
                            exit();
                        } else {
                            $message = "Database error (execute failed): " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $message = "Database error (prepare failed): " . $conn->error;
                    }
                } else {
                    $message = "Sorry, there was an error uploading your image file.";
                }
            }
        }
    }
    // If any error occurred ($message will be set), redirect back
    header("Location: src/pages/upload_product.php?status=error&message=" . urlencode($message));
    exit();

} else {
    // Not a POST request or invalid action
    header("Location: index.php");
    exit();
}

$conn->close();
?>