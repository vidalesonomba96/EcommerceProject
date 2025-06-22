<?php
// EcommerceProject/register_handler.php

session_start();
require_once 'db_connect.php';

$message = '';
$message_type = 'error'; // 'error' or 'success'

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_plain = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $agree_terms = isset($_POST['agree_terms']);

    if (empty($fullname) || empty($username) || empty($email) || empty($password_plain) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif (strlen($password_plain) < 8) {
        $message = "Password must be at least 8 characters long.";
    } elseif ($password_plain !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (!$agree_terms) {
        $message = "You must agree to the terms and conditions.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        $sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt_check = $conn->prepare($sql_check);
        if ($stmt_check) {
            $stmt_check->bind_param("ss", $username, $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $message = "Username or email already taken.";
            } else {
                $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                if ($stmt_insert) {
                    $stmt_insert->bind_param("ssss", $fullname, $username, $email, $password_hashed);
                    if ($stmt_insert->execute()) {
                        $message = "Registration successful! You can now login.";
                        $message_type = 'success';
                        // Redirect to login page with success message
                        header("Location: src/pages/login.php?status=regsuccess&message=" . urlencode($message));
                        exit();
                    } else {
                        $message = "Error during registration. Please try again.";
                    }
                    $stmt_insert->close();
                } else {
                    $message = "Database error (prepare insert failed). Please try again. ";
                }
            }
            $stmt_check->close();
        } else {
             $message = "Database error (prepare check failed). Please try again. ";
        }
    }
    // If registration failed, redirect back to register page with error message
    if ($message_type === 'error') {
        header("Location: src/pages/register.php?status=regerror&message=" . urlencode($message));
        exit();
    }
} else {
    // Not a POST request, redirect to registration page or home
    header("Location: src/pages/register.php");
    exit();
}

$conn->close();
?>