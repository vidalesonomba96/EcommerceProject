<?php
// src/pages/my_products.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../db_connect.php';
$base_url = '../..';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?message=" . urlencode("Please log in to manage your products."));
    exit();
}
$user_id = $_SESSION['user_id'];

// Handle product deletion
if (isset($_GET['delete_product'])) {
    $product_id_to_delete = intval($_GET['delete_product']);
    
    // Security check: Make sure the product belongs to the current user before deleting
    $stmt_check = $conn->prepare("SELECT image_path FROM products WHERE id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $product_id_to_delete, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($row_check = $result_check->fetch_assoc()) {
        // Delete image file
        $image_path_to_delete = $base_url . '/' . $row_check['image_path'];
        if (file_exists($image_path_to_delete)) {
            unlink($image_path_to_delete);
        }
        
        // Delete product from DB
        $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt_delete->bind_param("i", $product_id_to_delete);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        header("Location: my_products.php?message=" . urlencode("Product deleted successfully."));
        exit();
    }
    $stmt_check->close();
}


// Fetch user's products
$stmt = $conn->prepare("SELECT id, name, price, category, image_path, created_at FROM products WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

$page_title = "My Products";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - ConnectMarket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
    <!-- Reusing admin table styles for a consistent management look -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/admin/style.css">
</head>
<body>
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">
        <div class="table-container">
            <h1 class="admin-title" style="text-align: center; color: #374151; margin-bottom: 2rem;">My Listed Products</h1>

            <?php if(isset($_GET['message'])): ?>
                <p class="auth-message success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 1rem;"><?php echo htmlspecialchars(urldecode($_GET['message'])); ?></p>
            <?php endif; ?>

            <?php if (empty($products)): ?>
                <p style="text-align: center;">You haven't listed any products yet. <a href="<?php echo $base_url; ?>/src/pages/upload_product.php">Sell your first item!</a></p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><img src="<?php echo $base_url . '/' . htmlspecialchars($product['image_path']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>R<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><?php echo date("Y-m-d", strtotime($product['created_at'])); ?></td>
                            <td class="actions">
                                <a href="edit_my_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Edit</a>
                                <a href="?delete_product=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="btn-danger">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>
