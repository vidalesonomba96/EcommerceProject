<?php
// admin/admin_login_handler.php
session_start();
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = trim($_POST['email_or_username'] ?? '');
    $password_plain = trim($_POST['password'] ?? '');

    if (empty($email_or_username) || empty($password_plain)) {
        header("Location: index.php?message=" . urlencode("All fields are required."));
        exit();
    }

    $sql = "SELECT id, username, password, is_admin FROM users WHERE (email = ? OR username = ?) AND is_admin = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_username, $email_or_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password_plain, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['logged_in'] = true;
            $_SESSION['is_admin'] = true;
            header("Location: dashboard.php");
            exit();
        }
    }

    header("Location: index.php?message=" . urlencode("Invalid credentials or not an admin."));
    exit();
}
?>