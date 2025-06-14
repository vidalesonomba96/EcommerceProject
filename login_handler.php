<?php
// File: EcommerceProject/login_handler.php

session_start();
require_once 'db_connect.php'; //

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = trim($_POST['email_or_username'] ?? '');
    $password_plain = trim($_POST['password'] ?? '');

    if (empty($email_or_username) || empty($password_plain)) {
        $message = "Email/Username and password are required.";
    } else {
        $sql = "SELECT id, username, email, password, fullname FROM users WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ss", $email_or_username, $email_or_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password_plain, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['logged_in'] = true;

                    // Check for a redirect URL
                    if (isset($_SESSION['redirect_url_after_login'])) {
                        $redirect_url = $_SESSION['redirect_url_after_login'];
                        unset($_SESSION['redirect_url_after_login']); // Important: clear it after use
                        // Ensure $redirect_url is a path relative to the project root (e.g., 'src/pages/upload_product.php')
                        header("Location: " . $redirect_url);
                        exit();
                    } else {
                        // Default redirect to homepage if no specific redirect was set
                        header("Location: index.php"); // index.php is in the root
                        exit();
                    }
                } else {
                    $message = "Invalid login credentials (password mismatch).";
                }
            } else {
                $message = "User not found or invalid credentials.";
            }
            $stmt->close();
        } else {
            $message = "Database error. Please try again. ";
        }
    }
    // If login failed, redirect back to login page with error
    // And re-append the redirect URL if it was originally present, so it's not lost
    $redirect_query_param = '';
    if (isset($_POST['redirect_url_hidden_field'])) { // You might need to add this hidden field in login form
         // Or, more simply, check if it's still in session or was passed via GET to login.php
    } else if (isset($_SESSION['redirect_url_after_login'])) { // If login fails, keep the redirect target
         $redirect_query_param = "&redirect=" . urlencode($_SESSION['redirect_url_after_login']);
    }

    header("Location: src/pages/login.php?status=loginerror&message=" . urlencode($message) . $redirect_query_param);
    exit();

} else {
    // Not a POST request
    header("Location: src/pages/login.php");
    exit();
}

$conn->close();
?>