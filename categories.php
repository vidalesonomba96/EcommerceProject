<?php
// categories.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';
$base_url = '.'; // This file is in the root

// Define the list of categories and their corresponding Font Awesome icons
$categories = [
    'Electronics' => 'fa-laptop',
    'Clothing & Apparel' => 'fa-tshirt',
    'Home & Garden' => 'fa-home',
    'Books & Media' => 'fa-book-open',
    'Sports & Outdoors' => 'fa-futbol',
    'Health & Beauty' => 'fa-heartbeat',
    'Other' => 'fa-box'
];

$selected_category = isset($_GET['category']) ? trim($_GET['category']) : null;
$products = [];

// If a category is selected, fetch products for that category
if ($selected_category && array_key_exists($selected_category, $categories)) {
    $stmt = $conn->prepare("SELECT id, name, description, price, image_path FROM products WHERE category = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $selected_category);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
$page_title = $selected_category ? "Category: " . htmlspecialchars($selected_category) : "Browse Categories";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ConnectMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="src/styles/style.css">
    <link rel="shortcut icon" href="src/img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .category-grid-section {
            padding: 3rem 0;
            background-color: #f9fafb;
        }
        body.dark-mode .category-grid-section { background-color: var(--dm-bg-secondary); }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .category-card {
            background-color: #fff;
            border-radius: 0.75rem;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: #374151;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            color: #4F46E5;
        }
        .category-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
            color: #4F46E5;
        }
        .category-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
        }
        body.dark-mode .category-card {
             background-color: var(--dm-bg-tertiary);
             box-shadow: 0 4px 6px var(--dm-shadow-color);
             color: var(--dm-text-primary);
        }
        body.dark-mode .category-card:hover { color: var(--dm-brand-primary); }
        body.dark-mode .category-card i { color: var(--dm-brand-primary); }

        /* Styles for the product grid (re-uses styles from product.php) */
        .product-grid-section { padding-top: 2rem; }
    </style>
</head>
<body>

    <?php include 'src/components/header/_header.php'; ?>

    <main class="site-main">
        <section class="category-grid-section">
            <div class="container">
                <h1 class="section-title4"><?php echo $page_title; ?></h1>

                <?php if ($selected_category): ?>
                    <!-- Display products if a category is selected -->
                    <p style="text-align: center; margin-bottom: 2rem;"><a href="categories.php">&larr; Back to all categories</a></p>
                    
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
                        <p style="text-align: center;">No products found in this category yet. Check back soon!</p>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Display the category grid if no category is selected -->
                    <div class="category-grid">
                        <?php foreach ($categories as $name => $icon): ?>
                            <a href="?category=<?php echo urlencode($name); ?>" class="category-card">
                                <i class="fas <?php echo $icon; ?>"></i>
                                <h3><?php echo htmlspecialchars($name); ?></h3>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'src/components/footer/_footer.php'; ?>
    <script src="src/scripts/script.js" defer></script>

</body>
</html>
