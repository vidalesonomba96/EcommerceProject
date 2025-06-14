<?php
// admin/edit_product.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

$product_id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id_to_edit === 0) {
    header("Location: manage_products.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, name, description, price, category, image_path FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id_to_edit);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: manage_products.php?message=" . urlencode("Product not found."));
    exit();
}

$page_title = "Edit Product: " . htmlspecialchars($product['name']);
include 'header.php';
?>

<div class="form-container">
    <h1 class="admin-title">Edit Product</h1>

    <?php if(isset($_GET['error'])): ?>
        <p class="auth-message error" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 1rem;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
    <?php endif; ?>
    
    <form action="update_product_handler.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        
        <div class="form-group">
            <label for="product_name">Product Name</label>
            <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="product_description">Product Description</label>
            <textarea id="product_description" name="product_description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="product_price">Price (ZAR)</label>
            <input type="number" id="product_price" name="product_price" step="0.01" min="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>

        <div class="form-group">
            <label for="product_category">Category</label>
            <select id="product_category" name="product_category" required>
                <option value="Electronics" <?php echo ($product['category'] == 'Electronics') ? 'selected' : ''; ?>>Electronics</option>
                <option value="Clothing & Apparel" <?php echo ($product['category'] == 'Clothing & Apparel') ? 'selected' : ''; ?>>Clothing & Apparel</option>
                <option value="Home & Garden" <?php echo ($product['category'] == 'Home & Garden') ? 'selected' : ''; ?>>Home & Garden</option>
                <option value="Books & Media" <?php echo ($product['category'] == 'Books & Media') ? 'selected' : ''; ?>>Books & Media</option>
                <option value="Sports & Outdoors" <?php echo ($product['category'] == 'Sports & Outdoors') ? 'selected' : ''; ?>>Sports & Outdoors</option>
                <option value="Health & Beauty" <?php echo ($product['category'] == 'Health & Beauty') ? 'selected' : ''; ?>>Health & Beauty</option>
                <option value="Other" <?php echo ($product['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Current Image</label>
            <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="Current Product Image" style="max-width: 150px; display: block; margin-bottom: 1rem; border-radius: 5px;">
            <label for="product_image">Upload New Image (optional, will replace the current one)</label>
            <input type="file" id="product_image" name="product_image" accept="image/png, image/jpeg, image/webp">
        </div>

        <button type="submit" class="btn btn-primary" style="background-color: #4f46e5; color: white;">Update Product</button>
        <a href="manage_products.php" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
