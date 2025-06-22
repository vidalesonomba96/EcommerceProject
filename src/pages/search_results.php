<?php
// File: EcommerceProject/src/pages/search_results.php
session_start();
$base_url = '/EcommerceProject'; // Adjust as needed

// Retrieve search results and query from session
$results = isset($_SESSION['search_results']) ? $_SESSION['search_results'] : [];
$query = isset($_SESSION['search_query']) ? $_SESSION['search_query'] : '';

// Clear the session variables to prevent old results from showing
unset($_SESSION['search_results']);
unset($_SESSION['search_query']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results for "<?php echo htmlspecialchars($query); ?>"</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/styles/style.css">
</head>
<body>
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">
        <section class="product-grid-section page-section">
            <div class="container">
                <h2 class="section-title">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>

                <?php if (!empty($results)): ?>
                    <div class="product-grid">
                        <?php foreach ($results as $product): ?>
                            <div class="product-card">
                                <div class="product-card-image-container">
                                    <a href="<?php echo $base_url; ?>/src/pages/product_detail.php?product_id=<?php echo $product['id']; ?>">
                                        <img src="<?php echo $base_url . '/src/img/product_images/' . htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </a>
                                </div>
                                <div class="product-card-content">
                                    <h3 class="product-card-name">
                                        <a href="<?php echo $base_url; ?>/src/pages/product_detail.php?product_id=<?php echo $product['id']; ?>">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                    </h3>
                                    <div class="product-card-price">
                                        $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?>
                                    </div>
                                    <p class="product-card-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                                    <a href="<?php echo $base_url; ?>/src/pages/product_detail.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="empty-cart-message">No products found matching your search query.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo $base_url; ?>/src/scripts/script.js"></script>
</body>
</html>