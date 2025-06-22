<?php
// src/pages/edit_my_product.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';
$base_url = '../..';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?message=" . urlencode("Please log in."));
    exit();
}
$user_id = $_SESSION['user_id'];

$product_id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id_to_edit === 0) {
    header("Location: my_products.php");
    exit();
}

// Updated query to fetch discount_price
$stmt = $conn->prepare("SELECT id, name, description, price, discount_price, category, image_path FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id_to_edit, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: my_products.php?message=" . urlencode("Product not found or access denied."));
    exit();
}

$page_title = "Edit Product: " . htmlspecialchars($product['name']);
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
</head>
<body class="auth-page">
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main auth-page">
        <section class="auth-section">
            <div class="container">
                <div class="auth-form-container upload-form-container">
                    <h1 class="section-title auth-title">Edit Your Product</h1>
                    
                    <form action="update_my_handler.php" method="POST" enctype="multipart/form-data" class="auth-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product_description">Product Description</label>
                            <textarea id="product_description" name="product_description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="product_price">Price (ZAR)</label>
                            <input type="number" id="product_price" name="product_price" step="0.01" min="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="discount_price">Discount Price (ZAR) <span style="font-weight:normal; color:#888;">(Optional)</span></label>
                            <input type="number" id="discount_price" name="discount_price" step="0.01" min="0.00" value="<?php echo htmlspecialchars($product['discount_price'] ?? ''); ?>" placeholder="Leave empty to remove discount">
                        </div>

                        <div class="form-group">
                            <label for="product_category">Category</label>
                            <select id="product_category" name="product_category" required>
                                <option value="Electronics" <?php echo ($product['category'] == 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                                <option value="Clothing & Apparel" <?php echo ($product['category'] == 'Clothing & Apparel') ? 'selected' : ''; ?>>Clothing & Apparel</option>
                                <option value="Home & Garden" <?php echo ($product['category'] == 'Home & Garden') ? 'selected' : ''; ?>>Home & Garden</option>
                                <option value="Books & Media" <?php echo ($product['category'] == 'Books & Media') ? 'selected' : ''; ?>>Books & Media</option>
                                <option value="Sports & Outdoors" <?php echo ($product['category'] == 'Sports & Outdoors') ? 'selected' : ''; ?>>Sports & Outdoors</option>
                                <option value="Health & Beauty" <?php echo ($product['category'] == 'Health & Beauty') ? 'selected' : ''; ?>>Health & Beauty</option>
                                <option value="Other" <?php echo ($product['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Current Image</label>
                            <img src="<?php echo $base_url . '/' . htmlspecialchars($product['image_path']); ?>" alt="Current Product Image" style="max-width: 150px; display: block; margin-bottom: 1rem; border-radius: 5px;">
                            <label for="product_image">Upload New Image (optional)</label>
                            <input type="file" id="product_image" name="product_image" accept="image/png, image/jpeg, image/webp">
                        </div>

                        <button type="submit" class="btn btn-primary btn-submit-auth">Update Product</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>