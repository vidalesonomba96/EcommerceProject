<?php
// File: EcommerceProject/src/handlers/submit_review_handler.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';

// 1. Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to submit a review.";
    header("Location: ../pages/login.php");
    exit;
}

// 2. Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../index.php");
    exit;
}

// 3. Validate the incoming data
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$comment = trim(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS));
$user_id = (int)$_SESSION['user_id'];

if (!$product_id || !$rating || $rating < 1 || $rating > 5 || empty($comment)) {
    $_SESSION['error_message'] = "Please provide a valid rating and comment.";
    header("Location: ../pages/product_detail.php?id=" . $product_id);
    exit;
}

// 4. Check if the user has already reviewed this product
$stmt_check = $conn->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
$stmt_check->bind_param("ii", $product_id, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows > 0) {
    $_SESSION['error_message'] = "You have already reviewed this product.";
    header("Location: ../pages/product_detail.php?id=" . $product_id);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

// 5. Insert the new review into the database
// We include created_at with NOW() for reliability
$stmt_insert = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
if ($stmt_insert === false) {
    // Handle SQL preparation error
    error_log("SQL Prepare Error: " . $conn->error);
    $_SESSION['error_message'] = "A database error occurred. Please try again later.";
    header("Location: ../pages/product_detail.php?id=" . $product_id);
    exit;
}

$stmt_insert->bind_param("iiis", $product_id, $user_id, $rating, $comment);

if ($stmt_insert->execute()) {
    $_SESSION['success_message'] = "Thank you! Your review has been submitted.";
} else {
    error_log("SQL Execute Error: " . $stmt_insert->error);
    $_SESSION['error_message'] = "Could not submit your review due to a database error.";
}

$stmt_insert->close();
$conn->close();

// 6. Redirect back to the product page
header("Location: ../pages/product_detail.php?id=" . $product_id);
exit;