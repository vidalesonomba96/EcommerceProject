<?php
// admin/admin_auth.php
session_start();
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php?message=" . urlencode("You must be an admin to view this page."));
    exit();
}
?>