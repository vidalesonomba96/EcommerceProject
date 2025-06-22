<?php
// File: EcommerceProject/src/components/_breadcrumbs.php

function generate_breadcrumbs() {
    // Use the global $conn object from db_connect.php if needed for product/category names
    global $conn; 
    
    $base_url = '/EcommerceProject'; // Adjust if your project path is different
    $breadcrumbs = ['<a href="' . $base_url . '/index.php">Home</a>'];
    $current_page = basename($_SERVER['SCRIPT_NAME']);
    
    switch ($current_page) {
        case 'products.php':
            $breadcrumbs[] = '<span>Products</span>';
            break;

        case 'product_detail.php':
            $breadcrumbs[] = '<a href="' . $base_url . '/src/pages/products.php">Products</a>';
            if (isset($_GET['id'])) {
                $product_id = (int)$_GET['id'];
                // Fetch product name for the breadcrumb
                $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($product = $result->fetch_assoc()) {
                    $breadcrumbs[] = '<span>' . htmlspecialchars($product['name']) . '</span>';
                }
                $stmt->close();
            }
            break;

        case 'cart.php':
            $breadcrumbs[] = '<span>Shopping Cart</span>';
            break;
            
        case 'checkout.php':
            $breadcrumbs[] = '<a href="' . $base_url . '/src/pages/cart.php">Shopping Cart</a>';
            $breadcrumbs[] = '<span>Checkout</span>';
            break;
            
        case 'login.php':
            $breadcrumbs[] = '<span>Login</span>';
            break;
            
        case 'register.php':
            $breadcrumbs[] = '<span>Register</span>';
            break;

        case 'about.php':
            $breadcrumbs[] = '<span>About Us</span>';
            break;
        
        default:
            // For other pages, you can add more cases
            break;
    }
    
    if (count($breadcrumbs) > 1) {
        echo '<nav aria-label="breadcrumb" class="breadcrumb-container">';
        echo implode(' <span class="breadcrumb-separator">&raquo;</span> ', $breadcrumbs);
        echo '</nav>';
    }
}
?>