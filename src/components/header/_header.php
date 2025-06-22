<?php
// File: EcommerceProject/src/components/header/_header.php
if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
$cart_item_count_for_badge = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item_in_cart) {
        if (isset($item_in_cart['quantity'])) {
            $cart_item_count_for_badge += (int)$item_in_cart['quantity'];
        }
    }
}
?>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
</head>
<header class="site-header">
    <div class="container header-container">
        <a href="<?php echo htmlspecialchars($base_url); ?>/index.php" class="logo-link">
             <img src="<?php echo htmlspecialchars($base_url); ?>/src/img/icon2.png" alt="ConnectMarket Logo" class="logo-image">
        </a>
        
        <div class="search-container-desktop">
            <form action="<?php echo htmlspecialchars($base_url); ?>/search_handler.php" method="GET" class="search-form" id="search-form-desktop"> 
                <input type="search" name="query" placeholder="Search for anything..." class="search-input" id="live-search-input" autocomplete="off">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <div id="live-search-results" class="live-search-results-container"></div>
        </div>
        <nav class="nav-desktop">
            <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/products.php" class="nav-link">
                <i class="fas fa-store"></i>Shop</a> 
            <a href="<?php echo htmlspecialchars($base_url); ?>/categories.php" class="nav-link">Categories</a> 
            <a href="<?php echo htmlspecialchars($base_url); ?>/sell.php" class="nav-link btn btn-sell"> 
                <i class="fas fa-plus-circle"></i> Sell</a>

           <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                <div class="user-dropdown">
                    <button class="dropdown-toggle" id="user-dropdown-toggle">
                        <i class="fas fa-user-circle" style="font-size: 1.2rem;"></i>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="user-dropdown-menu">
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/dashboard.php" class="dropdown-item"><i class="fas fa-tachometer-alt fa-fw"></i> Dashboard</a>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/my_products.php" class="dropdown-item"><i class="fas fa-box-open fa-fw"></i> My Products</a>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/my_orders.php" class="dropdown-item"><i class="fas fa-receipt fa-fw"></i> My Orders</a>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/upload_product.php" class="dropdown-item"><i class="fas fa-plus-circle fa-fw"></i> Sell an Item</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo htmlspecialchars($base_url); ?>/logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt fa-fw"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/login.php" class="nav-link"><i class="fa fa-user-circle"></i> Login</a>
                <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/register.php" class="nav-link btn btn-signup">Sign Up</a>
            <?php endif; ?>

            <a href="<?php echo htmlspecialchars($base_url); ?>/admin/" class="nav-link" title="Admin Login">
                <i class="fas fa-user-shield"></i>
            </a>
            <button id="darkModeToggle" aria-label="Toggle dark mode" class="dark-mode-toggle-btn stylish-switch">
                <span class="stylish-switch-track"></span>
                <span class="stylish-switch-knob">
                    <i class="fas fa-sun"></i> 
                </span>
            </button>
            <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/cart.php" class="nav-link cart-link"> <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge"><?php echo $cart_item_count_for_badge; ?></span>
            </a>
            <?php include __DIR__ . '/../mini_cart.php'; ?>
        </nav>

        <div class="menu-toggle-container">
             <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/cart.php" class="nav-link cart-link mobile-cart-link">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge"><?php echo $cart_item_count_for_badge; ?></span>
            </a>
            <button id="darkModeToggleMobile" aria-label="Toggle dark mode" class="dark-mode-toggle-btn stylish-switch mobile-switch">
                 <span class="stylish-switch-track"></span>
                 <span class="stylish-switch-knob">
                     <i class="fas fa-sun"></i> 
                 </span>
            </button>
            <button id="menu-toggle" aria-label="Open menu" class="menu-toggle-button">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <button id="sidebar-close" aria-label="Close menu" class="sidebar-close-button">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <nav class="sidebar-nav">
        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/products.php" class="sidebar-nav-link"><i class="fas fa-store"></i><span>Shop</span></a>
        <a href="<?php echo htmlspecialchars($base_url); ?>/categories.php" class="sidebar-nav-link"><i class="fas fa-th-large"></i><span>Categories</span></a> 
        <a href="<?php echo htmlspecialchars($base_url); ?>/sell.php" class="sidebar-nav-link btn btn-sell"><i class="fas fa-plus-circle"></i> <span>Sell Item</span></a> 
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/dashboard.php" class="sidebar-nav-link"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a> 
            <a href="<?php echo htmlspecialchars($base_url); ?>/profile.php" class="sidebar-nav-link"><i class="fas fa-user-circle"></i> <span><?php echo htmlspecialchars($_SESSION['username']); ?></span></a> 
            <a href="<?php echo htmlspecialchars($base_url); ?>/logout.php" class="sidebar-nav-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        <?php else: ?>
            <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/login.php" class="sidebar-nav-link"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a>
            <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/register.php" class="sidebar-nav-link btn btn-signup"><span>Sign Up</span></a>
        <?php endif; ?>
        <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/cart.php" class="sidebar-nav-link cart-link-sidebar"> <i class="fas fa-shopping-cart"></i>
            <span>Cart</span>
            <span class="cart-badge"><?php echo $cart_item_count_for_badge; ?></span>
        </a>
        <a href="<?php echo htmlspecialchars($base_url); ?>/admin/" class="sidebar-nav-link">
            <i class="fas fa-user-shield"></i> <span>Admin</span>
        </a>
    </nav>
</div>
<div id="sidebar-overlay" class="sidebar-overlay"></div>