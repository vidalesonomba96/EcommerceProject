<?php
// EcommerceProject/src/pages/cart.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '../..'; // Relative path from src/pages/ to project root
$page_title = "Your Shopping Cart - Connect Market";

// No need to fetch cart items here in PHP initially if JS handles it,
// but you could for non-JS users or initial render.
// For this implementation, we'll let JavaScript populate it.
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
        /* Basic Cart Page Styles (can be moved to style.css) */
        .cart-page-container { padding: 2rem 0; }
        .cart-item { display: flex; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee; }
        .cart-item-image img { width: 100px; height: 100px; object-fit: cover; border-radius: 0.375rem; margin-right: 1rem; border: 1px solid #ddd;}
        .cart-item-details { flex-grow: 1; }
        .cart-item-details h3 { font-size: 1.1rem; margin-bottom: 0.25rem; color: var(--text-primary-color, #333); } /* Adapts to dark mode if variables are set */
        .cart-item-details p { margin-bottom: 0.25rem; font-size: 0.9rem; color: var(--text-secondary-color, #555); }
        .cart-item-quantity { margin-left: 1rem; margin-right: 1rem; text-align: center;}
        .cart-item-quantity label { font-size: 0.8rem; display: block; margin-bottom: 0.25rem;}
        .cart-item-quantity input { width: 60px; text-align: center; padding: 0.5rem; border: 1px solid #ddd; border-radius: 0.25rem; font-size: 0.9rem; }
        .cart-item-actions button.remove-item-btn { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.9rem; padding: 0.5rem; }
        .cart-item-actions button.remove-item-btn:hover { color: #dc2626; text-decoration: underline; }
        .cart-summary { margin-top: 2rem; padding: 1.5rem; background-color: #f9f9f9; border-radius: 0.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .cart-summary h2 { margin-bottom: 1rem; font-size: 1.5rem; color: var(--text-primary-color, #333); }
        .cart-summary p { display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 1rem; color: var(--text-secondary-color, #555); }
        .cart-summary p strong { color: var(--text-primary-color, #333); }
        .cart-summary .btn-checkout { display: block; width: 100%; text-align: center; margin-top: 1.5rem; padding: 0.875rem; font-size: 1.1rem; }
        .empty-cart-message { text-align:center; padding: 3rem 1rem; font-size: 1.1rem; color: #777;}
        .empty-cart-message a { color: #4F46E5; text-decoration: underline;}

        /* Dark mode cart specific styles */
        body.dark-mode .cart-item { border-bottom-color: var(--dm-border-color, #444); }
        body.dark-mode .cart-item-image img { border-color: var(--dm-border-color, #444); }
        body.dark-mode .cart-item-quantity input { background-color: var(--dm-bg-secondary, #242424); border-color: var(--dm-border-color, #444); color: var(--dm-text-primary, #e0e0e0); }
        body.dark-mode .cart-summary { background-color: var(--dm-bg-tertiary, #2c2c2c); box-shadow: 0 2px 8px var(--dm-shadow-color); }
        body.dark-mode .empty-cart-message { color: var(--dm-text-secondary, #b0b0b0);}
        body.dark-mode .empty-cart-message a { color: var(--dm-brand-primary, #7067f0);}

    </style>
</head>
<body>
    <?php include '../components/header/_header.php'; // ?>

    <main class="site-main">
        <section class="cart-page-section">
            <div class="container cart-page-container">
                <h1 class="section-title4">Your Shopping Cart</h1>
                
                <div id="cart-items-container">
                    <p style="text-align:center; padding: 2rem 0;">Loading your cart...</p>
                </div>

                <div class="cart-summary" style="display: none;"> <h2>Cart Summary</h2>
                    <p><span>Total Unique Items:</span> <span id="cart-total-items">0</span></p>
                    <p><span>Grand Total:</span> <strong id="cart-grand-total">R0.00</strong></p>
                    <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/checkout.php" class="btn btn-primary btn-checkout">Proceed to Checkout</a>
                    <p style="text-align:center; margin-top: 1rem;"><a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/products.php">Continue Shopping</a></p>
                </div>
            </div>
        </section>
    </main>

    <?php include '../components/footer/_footer.php'; // ?>
    
    
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script> 
    <script>
        // Ensure that refreshCartPageDisplay is called once the DOM is ready and script.js is loaded.
        // The main script.js already has a DOMContentLoaded listener that calls refreshCartPageDisplay
        // if on the cart page, so this explicit call might not be needed if that's set up correctly.
        // However, to be certain for initial load:
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof refreshCartPageDisplay === 'function') {
                refreshCartPageDisplay(); // Call it to initially populate the cart
            }
            if (typeof initializeCartPageEventListeners === 'function') {
                // This is likely already called in script.js's DOMContentLoaded too,
                // but ensure it's bound.
                // initializeCartPageEventListeners();
            }
        });
    </script>

</body>
</html>