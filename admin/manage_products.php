<?php
// admin/manage_products.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

// Handle product deletion
if (isset($_GET['delete_product'])) {
    $product_id_to_delete = intval($_GET['delete_product']);

    // First, get the image path to delete the file
    $stmt_img = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt_img->bind_param("i", $product_id_to_delete);
    $stmt_img->execute();
    $result_img = $stmt_img->get_result();
    if ($row_img = $result_img->fetch_assoc()) {
        $image_path_to_delete = '../' . $row_img['image_path'];
        if (file_exists($image_path_to_delete)) {
            unlink($image_path_to_delete); // Delete the image file
        }
    }
    $stmt_img->close();

    // Now, delete the product from the database
    $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt_delete->bind_param("i", $product_id_to_delete);
    $stmt_delete->execute();
    $stmt_delete->close();
    
    header("Location: manage_products.php?message=" . urlencode("Product deleted successfully."));
    exit();
}

$page_title = "Manage Products";
include 'header.php';

// Fetch all products with seller username
$sql = "SELECT p.id, p.name, p.price, p.category, p.image_path, p.created_at, u.username 
        FROM products p 
        JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="table-container">
    <h1 class="admin-title" style="text-align: center; color: #374151; margin-bottom: 2rem;">Manage Products</h1>

    <?php if(isset($_GET['message'])): ?>
        <p class="auth-message success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 1rem;"><?php echo htmlspecialchars(urldecode($_GET['message'])); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Seller</th>
                <th>Price</th>
                <th>Category</th>
                <th>Date Added</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['username']); ?></td>
                <td>R<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td><?php echo date("Y-m-d", strtotime($product['created_at'])); ?></td>
                <td class="actions">
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Edit</a>
                    <a href="?delete_product=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to permanently delete this product?');" class="btn-danger">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
