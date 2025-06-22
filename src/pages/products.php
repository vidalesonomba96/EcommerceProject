<?php
// File: src/pages/products.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '../..';
$page_title = "All Products";

require_once '../../db_connect.php';
require_once '../components/_breadcrumbs.php';
require_once '../components/_pagination.php';

// --- FILTERING AND PAGINATION LOGIC ---
$products_per_page = 6; // Number of products to display per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

// Check for the "deals" filter
$filter = $_GET['filter'] ?? '';
$where_clause = '';
$page_heading = 'All Products';

if ($filter === 'deals') {
    // Only select products that have a valid discount price
    $where_clause = "WHERE discount_price IS NOT NULL AND discount_price > 0 AND discount_price < price";
    $page_heading = 'Special Deals';
}

// Get the total number of products (with filter applied)
$total_products_result = $conn->query("SELECT COUNT(id) AS total FROM products $where_clause");
$total_products = $total_products_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $products_per_page;

// Fetch a specific "page" of products (with filter applied)
$products = [];
$sql = "SELECT id, name, description, price, discount_price, image_path FROM products $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $products_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_heading); ?> | Connect Market</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
</head>
<body>
    <?php include '../components/header/_header.php'; ?>
    <main class="site-main">
        <?php generate_breadcrumbs(); // Display breadcrumbs ?>
        <section class="product-grid-section page-section">
            <div class="container">
                <h2 class="section-title4"><?php echo $page_heading; ?></h2>
                <?php if (!empty($products)): ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <?php // Product card HTML is unchanged ?>
                            <div class="product-card">
                                <div class="product-card-image-container">
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($base_url . '/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </a>
                                </div>
                                <div class="product-card-content">
                                    <h3 class="product-card-name">
                                        <a href="product_detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                    </h3>
                                    <?php 
                                        $isOnSale = isset($product['discount_price']) && (float)$product['discount_price'] > 0;
                                        $displayPrice = $isOnSale ? $product['discount_price'] : $product['price'];
                                    ?>
                                    <p class="product-card-price">
                                        <?php if ($isOnSale): ?>
                                            <span class="sale-price">R<?php echo htmlspecialchars(number_format((float)$product['discount_price'], 2)); ?></span>
                                            <span class="original-price">R<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?></span>
                                        <?php else: ?>
                                            <span class="regular-price">R<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <button type="button" class="btn btn-add-to-cart" 
                                        data-product-id="<?php echo htmlspecialchars($product['id']); ?>" 
                                        data-product-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                        data-product-price="<?php echo htmlspecialchars($displayPrice);?>" 
                                        data-product-image="<?php echo htmlspecialchars($product['image_path']); ?>">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php 
                        // Display the pagination links and pass the filter parameter
                        generate_pagination($current_page, $total_pages, 'products.php', ['filter' => $filter]); 
                    ?>

                <?php else: ?>
                    <p style="text-align: center;">No products found for this criteria.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>