<?php
// File: EcommerceProject/sell.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define the actual page where users upload products
$upload_product_destination = 'src/pages/upload_product.php'; // Path relative to project root

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // User is NOT logged in
    // Store the final destination in the session so login_handler can use it
    $_SESSION['redirect_url_after_login'] = $upload_product_destination;

    $message = "Please log in or register to sell your items.";
    // Redirect to the login page, passing the original intended destination as a GET parameter
    // login.php is located at src/pages/login.php
    header("Location: src/pages/login.php?message=" . urlencode($message) . "&redirect=" . urlencode($upload_product_destination));
    exit();
} else {
    // User IS logged in
    // Redirect them directly to the product upload page
    header("Location: " . $upload_product_destination);
    exit();
}
?>