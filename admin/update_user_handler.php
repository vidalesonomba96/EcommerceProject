<?php
// admin/update_user_handler.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id'] ?? 0);
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $is_admin = intval($_POST['is_admin'] ?? 0);

    if ($user_id === 0 || empty($fullname) || empty($username) || empty($email)) {
        header("Location: edit_user.php?id=" . $user_id . "&error=" . urlencode("All fields are required."));
        exit();
    }
    
    // Prevent making the current admin a non-admin, locking them out
    if ($user_id === $_SESSION['user_id'] && $is_admin === 0) {
        header("Location: edit_user.php?id=" . $user_id . "&error=" . urlencode("You cannot remove your own admin status."));
        exit();
    }

    // Check if new username or email already exists for another user
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt_check->bind_param("ssi", $username, $email, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header("Location: edit_user.php?id=" . $user_id . "&error=" . urlencode("Username or email already taken by another user."));
        exit();
    }
    $stmt_check->close();

    // Update user information
    $stmt_update = $conn->prepare("UPDATE users SET fullname = ?, username = ?, email = ?, is_admin = ? WHERE id = ?");
    $stmt_update->bind_param("sssii", $fullname, $username, $email, $is_admin, $user_id);

    if ($stmt_update->execute()) {
        header("Location: manage_users.php?message=" . urlencode("User information updated successfully."));
    } else {
        header("Location: edit_user.php?id=" . $user_id . "&error=" . urlencode("Failed to update user. Please try again."));
    }
    $stmt_update->close();
    $conn->close();
    exit();

} else {
    header("Location: manage_users.php");
    exit();
}
?>
