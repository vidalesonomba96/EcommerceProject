<?php
// File: src/pages/product_detail.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '../..';
require_once '../../db_connect.php';
require_once '../components/_breadcrumbs.php';

// Check if product ID is provided and is valid
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id === 0) {
    header("Location: products.php");
    exit();
}

// Fetch product details
$stmt_product = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$product_result = $stmt_product->get_result();
$product = $product_result->fetch_assoc();
$stmt_product->close();

// If product doesn't exist, redirect
if (!$product) {
    header("Location: products.php");
    exit();
}

$page_title = htmlspecialchars($product['name']);

// --- Fetch reviews and calculate average rating ---
$reviews = [];
$average_rating = 0;
$total_reviews = 0;
$stmt_reviews = $conn->prepare("SELECT r.rating, r.comment, r.created_at, u.username 
                                FROM reviews r 
                                JOIN users u ON r.user_id = u.id 
                                WHERE r.product_id = ? 
                                ORDER BY r.created_at DESC");
$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$result_reviews = $stmt_reviews->get_result();

if ($result_reviews->num_rows > 0) {
    $total_rating = 0;
    while ($row = $result_reviews->fetch_assoc()) {
        $reviews[] = $row;
        $total_rating += $row['rating'];
    }
    $total_reviews = count($reviews);
    $average_rating = round($total_rating / $total_reviews, 1);
}
$stmt_reviews->close();
// We don't close the main $conn here because breadcrumbs might need it
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Connect Market</title>
    <!-- Main Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
    
    <!-- Image Zoom/Lightbox Library CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/basiclightbox@5.0.4/dist/basicLightbox.min.css">
</head>
<body>
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">
        <?php generate_breadcrumbs(); ?>
        
        <section class="page-section product-detail-section">
            <div class="container">
                <?php
                // Display success or error messages from the review submission
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="auth-message success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                    unset($_SESSION['success_message']);
                }
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="auth-message error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>
                
                <div class="product-detail-layout">
                    <div class="product-detail-image">
                        <!-- The link for the image zoom feature -->
                        <a href="<?php echo htmlspecialchars($base_url . '/' . $product['image_path']); ?>" id="product-image-link">
                            <img src="<?php echo htmlspecialchars($base_url . '/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                    </div>
                    <div class="product-detail-info">
                        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <div class="product-ratings-summary">
                            <div class="stars" style="--rating: <?php echo $average_rating; ?>;" aria-label="Rating: <?php echo $average_rating; ?> out of 5 stars"></div>
                            <a href="#reviews" class="reviews-count-link">(<?php echo $total_reviews; ?> reviews)</a>
                        </div>
                        <p class="product-description-full"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        
                        <div class="product-price-detail">
                            <?php
                                $isOnSale = isset($product['discount_price']) && (float)$product['discount_price'] > 0;
                                $displayPrice = $isOnSale ? $product['discount_price'] : $product['price'];
                            ?>
                            <?php if ($isOnSale): ?>
                                <span class="sale-price-detail">R<?php echo htmlspecialchars(number_format((float)$product['discount_price'], 2)); ?></span>
                                <span class="original-price-detail">R<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?></span>
                            <?php else: ?>
                                <span class="regular-price-detail">R<?php echo htmlspecialchars(number_format((float)$product['price'], 2)); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-add-to-cart" 
                            data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                            data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                            data-product-price="<?php echo htmlspecialchars($displayPrice); ?>"
                            data-product-image="<?php echo htmlspecialchars($product['image_path']); ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- The restored reviews section -->
        <section id="reviews" class="page-section reviews-section bg-light">
           <div class="container">
                <h2 class="section-title4">Customer Reviews</h2>
                <div class="reviews-layout">
                    <div class="review-form-container">
                        <h3>Write a Review</h3>
                        <?php if (isset($_SESSION['logged_in'])): ?>
                            <form action="../handlers/submit_review_handler.php" method="POST" class="review-form">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <div class="form-group">
                                    <label>Your Rating</label>
                                    <div class="star-rating-input">
                                        <input type="radio" id="rate-5" name="rating" value="5" required><label for="rate-5" title="5 stars">★</label>
                                        <input type="radio" id="rate-4" name="rating" value="4"><label for="rate-4" title="4 stars">★</label>
                                        <input type="radio" id="rate-3" name="rating" value="3"><label for="rate-3" title="3 stars">★</label>
                                        <input type="radio" id="rate-2" name="rating" value="2"><label for="rate-2" title="2 stars">★</label>
                                        <input type="radio" id="rate-1" name="rating" value="1"><label for="rate-1" title="1 star">★</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="comment">Your Review</label>
                                    <textarea id="comment" name="comment" rows="4" placeholder="Share your thoughts about the product..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        <?php else: ?>
                             <p>You must be <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">logged in</a> to write a review.</p>
                        <?php endif; ?>
                    </div>
                    <div class="reviews-list">
                        <?php if (empty($reviews)): ?>
                            <p>No reviews yet. Be the first to review this product!</p>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <span class="review-author"><?php echo htmlspecialchars($review['username']); ?></span>
                                        <div class="review-card-stars" style="--rating: <?php echo $review['rating']; ?>;"></div>
                                    </div>
                                    <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                                    <span class="review-date"><?php echo date("F j, Y", strtotime($review['created_at'])); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Image Zoom/Lightbox Library JS -->
    <script src="https://cdn.jsdelivr.net/npm/basiclightbox@5.0.4/dist/basicLightbox.min.js"></script>
    
    <!-- Main Site Script -->
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>
