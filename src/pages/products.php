<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php'; // Assuming this is already corrected
$base_url = '../..';

// Fetch products from database
$products = [];
$sql_products = "SELECT id, name, description, price, image_path FROM products ORDER BY created_at DESC LIMIT 12"; // Get latest 12 products
$result_products = $conn->query($sql_products);
if ($result_products && $result_products->num_rows > 0) {
    while ($row = $result_products->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect Market | Your Online Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .product-grid-section { padding: 3rem 0; background-color: #f9fafb; } 
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; }
        .product-card { background-color: #fff; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; }
        .product-card-image-container { width: 100%; height: 200px; /* Fixed height for image container */ overflow: hidden; }
        .product-card img { width: 100%; height: 100%; object-fit: cover; /* Scales image nicely */ transition: transform 0.3s ease; }
        .product-card:hover img { transform: scale(1.05); }
        .product-card-content { padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; }
        .product-card-name { font-size: 1.1rem; font-weight: 600; color: #333; margin-bottom: 0.5rem; }
        .product-card-price { font-size: 1rem; color: #4F46E5; font-weight: bold; margin-bottom: 0.75rem; }
        .product-card-description { font-size: 0.85rem; color: #555; margin-bottom: 1rem; flex-grow: 1; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; }
        .product-card .btn { margin-top: auto; background-color: #22c55e; color: white; text-align: center; padding: 0.5rem; border-radius: 0.25rem; text-decoration: none;}
        .product-card .btn:hover { background-color: #16a34a; }

        /* Dark mode specific styles for product cards */
        body.dark-mode .product-grid-section { background-color: var(--dm-bg-secondary, #242424); }
        body.dark-mode .product-card { background-color: var(--dm-bg-tertiary, #2c2c2c); box-shadow: 0 4px 6px var(--dm-shadow-color); }
        body.dark-mode .product-card-name { color: var(--dm-text-primary, #e0e0e0); }
        body.dark-mode .product-card-price { color: var(--dm-brand-primary, #7067f0); }
        body.dark-mode .product-card-description { color: var(--dm-text-secondary, #b0b0b0); }
        body.dark-mode .product-card .btn { background-color: var(--dm-btn-sell-bg, #3aa565); }
        body.dark-mode .product-card .btn:hover { background-color: var(--dm-btn-sell-hover-bg, #2f8b53); }
       
    </style>
</head>
<body>

    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">
        <section class="new-arrivals-section">
            </section>

        <section class="product-grid-section">
            <div class="container">
                <h2 class="section-title4">Featured Products</h2>
                <?php if (!empty($products)): ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-card-image-container">
                                    <img src="<?php echo htmlspecialchars($base_url . '/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                                <div class="product-card-content">
                                    <h3 class="product-card-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-card-price">R<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                                    <p class="product-card-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?></p>
                                    <button type="button" class="btn btn-add-to-cart" data-product-id="<?php echo htmlspecialchars($product['id']); ?>" data-product-name="<?php echo htmlspecialchars($product['name']); ?>" data-product-price="<?php echo htmlspecialchars($product['price']);
                                     ?>"  data-product-image="<?php echo htmlspecialchars($product['image_path']); ?>">Add to Cart</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center;">No products found yet. Check back soon!</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="features-section">
            </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>

</div>
    <script src="../scripts/script.js" defer></script>

</body>
</html>