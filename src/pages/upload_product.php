<?php
// src/pages/upload_product.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define base_url relative to the project root
$base_url = '../..';
$page_title = "Upload Product - Connect Market";

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?message=" . urlencode("Please log in to upload products."));
    exit();
}
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

    <main class="site-main auth-page">
        <section class="auth-section">
            <div class="container">
                <div class="auth-form-container upload-form-container"> 
                    <h1 class="section-title auth-title">Upload a New Product</h1>

                    <?php
                    if (isset($_GET['message'])) {
                        $status_type = (isset($_GET['status']) && $_GET['status'] === 'success') ? 'success' : 'error';
                        echo '<p class="auth-message ' . $status_type . '">' . htmlspecialchars(urldecode($_GET['message'])) . '</p>';
                    }
                    ?>

                    <form action="<?php echo htmlspecialchars($base_url); ?>/product_handler.php" method="POST" class="auth-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_product">

                        <div class="form-group">
                            <label for="product_name">Product Name</label>
                            <input type="text" id="product_name" name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="product_description">Product Description</label>
                            <textarea id="product_description" name="product_description" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_price">Price (ZAR)</label>
                            <input type="number" id="product_price" name="product_price" step="0.01" min="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="discount_price">Discount Price (ZAR) <span style="font-weight:normal; color:#888;">(Optional)</span></label>
                            <input type="number" id="discount_price" name="discount_price" step="0.01" min="0.00" placeholder="e.g., 149.99">
                        </div>

                        <div class="form-group">
                            <label for="product_category">Category</label>
                            <select id="product_category" name="product_category" required>
                                <option value="">Select a Category</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing & Apparel">Clothing & Apparel</option>
                                <option value="Home & Garden">Home & Garden</option>
                                <option value="Books & Media">Books & Media</option>
                                <option value="Sports & Outdoors">Sports & Outdoors</option>
                                <option value="Health & Beauty">Health & Beauty</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="product_image">Product Image</label>
                            <input type="file" id="product_image" name="product_image" accept="image/png, image/jpeg, image/webp" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-submit-auth">Upload Product</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>