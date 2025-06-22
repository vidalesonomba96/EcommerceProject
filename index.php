<?php
// File: index.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '.';
$page_title = "Connect Market | Your Online Marketplace";

require_once 'db_connect.php';

// Define the static banner images for the hero slider from the 'src/img/hero/' directory
$hero_images = [
    'src/img/hero/slide1.jpg',
    'src/img/hero/slide2.jpg',
    'src/img/hero/slide3.png',
    'src/img/hero/slide4.jpg',
    'src/img/hero/slide5.jpg'
];

// Fetch 4 featured products, now including discount_price
$featured_products = [];
$sql_products = "SELECT id, name, description, price, discount_price, image_path FROM products ORDER BY created_at DESC LIMIT 3";
$result_products = $conn->query($sql_products);
if ($result_products && $result_products->num_rows > 0) {
    while ($row = $result_products->fetch_assoc()) {
        $featured_products[] = $row;
    }
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body> 
    <?php include 'src/components/header/_header.php'; ?>

    <main class="site-main">
        <section class="hero-section">
            <div class="hero-slider" id="heroSlider">
                <?php foreach ($hero_images as $index => $image_path): ?>
                    <div class="slide<?php echo ($index === 0) ? ' active' : ''; ?>" style="background-image: url('<?php echo htmlspecialchars($base_url . '/' . ltrim($image_path, '/')); ?>');"></div>
                <?php endforeach; ?>
            </div>
            <div class="hero-content">
                <h1 class="hero-title">Discover & Connect</h1>
                <p class="hero-description">Your trusted marketplace for unique finds and quality goods. Buy, sell, and connect with a vibrant community.</p>
                <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/products.php" class="btn btn-primary">Shop All Products</a>
            </div>
        </section>

        <section class="page-section bg-light">
            <div class="container">
                <h2 class="section-title4">Featured Products</h2>
                <?php if (!empty($featured_products)): ?>
                    <div class="product-grid">
                        <?php foreach ($featured_products as $product): ?>
                            <?php
                                // Logic to determine if the product is on sale
                                $isOnSale = isset($product['discount_price']) && (float)$product['discount_price'] > 0 && (float)$product['discount_price'] < (float)$product['price'];
                                $displayPrice = $isOnSale ? $product['discount_price'] : $product['price'];
                            ?>
                            <div class="product-card">
                                <div class="product-card-image-container">
    <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/product_detail.php?id=<?php echo $product['id']; ?>">
        <img src="<?php echo htmlspecialchars($base_url . '/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </a>
</div>
<div class="product-card-content">
    <h3 class="product-card-name">
        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/product_detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
    </h3>
                                    <p class="product-card-price">
                                        <?php if ($isOnSale): ?>
                                            <span class="sale-price">R<?php echo htmlspecialchars(number_format((float)$product['discount_price'], 2)); ?></span>
                                            <span class="original-price">R<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?></span>
                                        <?php else: ?>
                                            <span class="regular-price">R<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="product-card-description"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
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
                <?php else: ?>
                    <p style="text-align: center;">No featured products yet. Check back soon!</p>
                <?php endif; ?>
            </div>
        </section>
        
        <section class="page-section">
            <div class="container">
                <div class="special-offer-section">
                    <video autoplay loop muted playsinline class="special-offer-video-bg">
                        <source src="src/media/apple_huawei_deals.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <div class="special-offer-overlay"></div>
                    <div class="special-offer-content">
                        <h2>Deal of the Week!</h2>
                        <p>Get up to 30% off on selected electronics. Don't miss out on these amazing prices. Limited time only!</p>
                        <a href="src/pages/products.php?filter=deals" class="btn">Shop Deals</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Why Trade with ConnectMarket?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt feature-icon feature-icon-secure"></i>
                        <h3 class="feature-title">Secure Transactions</h3>
                        <p class="feature-description">Shop and sell with confidence thanks to our secure payment gateway and fraud protection.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-users feature-icon feature-icon-community"></i>
                        <h3 class="feature-title">Vibrant Community</h3>
                        <p class="feature-description">Connect with fellow buyers and sellers, discover unique items, and share your passion.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-rocket feature-icon feature-icon-easy"></i>
                        <h3 class="feature-title">Easy Listing &amp; Selling</h3>
                        <p class="feature-description">List your items in minutes and reach thousands of potential buyers effortlessly.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'src/components/footer/_footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hero Slider
        const sliderContainer = document.getElementById('heroSlider');
        if (sliderContainer) {
            const slides = sliderContainer.querySelectorAll('.slide');
            let currentSlideIndex = 0;
            if (slides.length > 1) {
                setInterval(() => {
                    slides[currentSlideIndex].classList.remove('active');
                    currentSlideIndex = (currentSlideIndex + 1) % slides.length;
                    slides[currentSlideIndex].classList.add('active');
                }, 5000); // Change image every 5 seconds
            }
        }
    });
    </script>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>

</body>
</html>