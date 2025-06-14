<?php
// Add these lines to the very top of your index.php for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// File: index.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '.';
$page_title = "Connect Market | Your Online Marketplace";

// Include database connection
require_once 'db_connect.php'; // Make sure this path is correct

$product_images = [];
// Fetch, for example, the 5 most recent product images
$sql_banner_images = "SELECT image_path FROM products WHERE image_path IS NOT NULL AND image_path != '' ORDER BY created_at DESC LIMIT 5";
$result_banner_images = $conn->query($sql_banner_images);

if ($result_banner_images && $result_banner_images->num_rows > 0) {
    while ($row = $result_banner_images->fetch_assoc()) {
        $product_images[] = $row['image_path'];
    }
}
// $conn->close(); // Optional: close connection if not done by db_connect.php on script end
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
    <style>
        /* Styles for the banner image slider */
        .banner-image-slider {
            position: relative;
            width: 100%;
            height: 400px; /* Adjust as needed, or match original banner image height */
            overflow: hidden;
            border-radius: 0.75rem; /* Keep existing banner rounding */
        }
        .banner-image-slider .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out; /* Fade transition */
        }
        .banner-image-slider .slide.active {
            opacity: 1;
        }
        .banner-image-slider .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Ensure the overall .new-arrivals-banner grid still works with two columns */
        /* Your existing .new-arrivals-banner CSS from style.css is:
           display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center; ...
           This should be fine. The .banner-image-slider just replaces the old .banner-image div
        */

        @media (max-width: 768px) {
            /* Your existing responsive styles for .new-arrivals-banner:
               grid-template-columns: 1fr;
               .banner-image { order: -1; margin-bottom: 1.5rem; }
               We need to ensure .banner-image-slider behaves similarly.
            */
            .new-arrivals-banner { /* This is already in your style.css for responsiveness */
                 /* grid-template-columns: 1fr; */ /* Already there */
            }
            .banner-image-slider { /* Apply similar responsive order if image goes on top */
                 order: -1; 
                 margin-bottom: 1.5rem;
                 height: 300px; /* Adjust height for mobile if needed */
            }
        }
    </style>
</head>
<body> 
    <?php include 'src/components/header/_header.php'; ?>

    <main class="site-main">
        <section class="new-arrivals-section">
            <div class="container">
                
                <div class="new-arrivals-banner"> 
                    
                    <div class="banner-content">
                        <h2 class="banner-title">Discover the Latest Arrivals</h2>
                        <p class="banner-description">Be the first to explore our exciting collection of sealed goods. From trending gadgets to stylish apparel, find something special today!</p>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/products.php" class="btn btn-primary btn-large">Shop New Arrivals</a> 
                    </div>
                    
                    
                    <div class="banner-image-slider" id="bannerImageSlider">
                        <?php if (!empty($product_images)): ?>
                            <?php foreach ($product_images as $index => $image_path): ?>
                                <div class="slide<?php echo ($index === 0) ? ' active' : ''; ?>">
                                    <img src="<?php echo htmlspecialchars($base_url . '/' . ltrim($image_path, '/')); ?>" alt="New Arrival Product Image <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            
                            <div class="slide active">
                                <img src="<?php echo htmlspecialchars($base_url); ?>/src/img/new-arrivals2.avif" alt="New Arrivals Collection">
                            </div>
                        <?php endif; ?>
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
        if (typeof window.initializeDynamicContentScripts === 'function') {
            window.initializeDynamicContentScripts();
        }

        const sliderContainer = document.getElementById('bannerImageSlider');
        if (sliderContainer) {
            const slides = sliderContainer.querySelectorAll('.slide');
            let currentSlideIndex = 0;

            if (slides.length > 1) {
                function showSlide(index) {
                    slides.forEach((slide, i) => {
                        slide.classList.remove('active');
                        if (i === index) {
                            slide.classList.add('active');
                        }
                    });
                }

                setInterval(() => {
                    currentSlideIndex = (currentSlideIndex + 1) % slides.length;
                    showSlide(currentSlideIndex);
                }, 5000); // Change image every 5 seconds
            }
        }
    });

    </script>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>

</body>
</html>