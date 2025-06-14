<?php
// src/pages/upload_product.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define base_url relative to the project root
// Since this file is in src/pages/, ../.. points to the project root
$base_url = '../..';
$page_title = "Upload Product - Connect Market";

// Redirect if not logged in
// login.php is in the same src/pages/ directory
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
    <style>
        .upload-form-container { 
            max-width: 600px; 
            margin: 2rem auto; 
            padding: 2rem; 
            background: #fff; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        .auth-form textarea { /* Ensure textarea matches other inputs */
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.975rem;
            line-height: 1.5;
            font-family: inherit; /* Ensures consistent font */
        }
        .auth-form select { /* Styling for the category dropdown */
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.975rem;
            background-color: #fff;
            color: #1f2937; /* Default text color for inputs */
            line-height: 1.5;
            font-family: inherit; /* Ensures consistent font */
        }
        /* Dark mode styles for textarea and select if not covered by global styles */
        body.dark-mode .upload-form-container {
            background-color: var(--dm-bg-tertiary, #2c2c2c);
            box-shadow: 0 10px 15px -3px var(--dm-shadow-color), 0 4px 6px -2px var(--dm-shadow-color);
        }
        body.dark-mode .auth-form textarea {
            background-color: var(--dm-bg-secondary, #242424);
            border-color: var(--dm-border-color, #444444);
            color: var(--dm-text-primary, #e0e0e0);
        }
        body.dark-mode .auth-form select {
            background-color: var(--dm-bg-secondary, #242424);
            border-color: var(--dm-border-color, #444444);
            color: var(--dm-text-primary, #e0e0e0);
        }
        body.dark-mode .auth-form label { /* Ensure labels are visible in dark mode */
             color: var(--dm-text-secondary, #b0b0b0);
        }
        body.dark-mode .auth-title { /* Ensure title is visible in dark mode */
            color: var(--dm-text-primary, #e0e0e0);
        }

    </style>
</head>
<body>
    <?php
    // _header.php is in src/components/, so from src/pages/ the path is ../components/_header.php
    // It relies on $base_url being defined by this parent script.
    include '../components/header/_header.php';
    ?>

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

    <?php
    // _footer.php is in src/components/, so from src/pages/ the path is ../components/_footer.php
    // It relies on $base_url being defined by this parent script.
    include '../components/footer/_footer.php';
    ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>