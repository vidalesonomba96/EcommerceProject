<?php
// admin/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Admin"; ?> - ConnectMarket</title>
    <link rel="shortcut icon" href="../src/img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../src/styles/style.css"> <!-- Main Site Styles -->
    <link rel="stylesheet" href="style.css"> <!-- Admin Specific Overrides -->
</head>
<body class="admin-body">
    <header class="admin-header site-header">
        <div class="header-container container">
            <div class="logo-link" style="color: white; font-size: 1.5rem; font-weight: bold;">ConnectMarket Admin</div>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="manage_users.php">Users</a>
                <a href="manage_products.php">Products</a>
                <a href="sales_reports.php">Reports</a>
                <a href="../index.php"><i class="fas fa-home"></i> Back to Site</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>
    <main class="site-main">
