<?php
// src/components/mini_cart.php
?>
<div id="mini-cart" class="mini-cart-container">
    <div class="mini-cart-content">
        <div class="mini-cart-header">
            <h3>Your Cart</h3>
            <button id="mini-cart-close" class="mini-cart-close-btn" aria-label="Close cart">&times;</button>
        </div>
        <div id="mini-cart-items" class="mini-cart-items">
            <p>Your cart is empty.</p>
        </div>
        <div class="mini-cart-footer">
            <a href="<?php echo htmlspecialchars($base_url ?? ''); ?>/src/pages/cart.php" class="btn btn-secondary">View Cart</a>
        </div>
    </div>
</div>