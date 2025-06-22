<?php
session_start();
require_once '../db_connect.php';
require_once 'admin_auth.php';

$product_id = $_GET['id'] ?? 0;
if ($product_id == 0) {
    header("Location: manage_products.php");
    exit();
}

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: manage_products.php?message=Product not found.");
    exit();
}
$page_title = "Edit Product";
include 'header.php';
?>

<div class="container">
    <h1 class="page-title">Edit Product: <?php echo htmlspecialchars($product['name']); ?></h1>

    <div class="form-container">
        <form action="update_product_handler.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="product_description">Description</label>
                <textarea id="product_description" name="product_description" class="form-control" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="product_price">Price (ZAR)</label>
                <input type="number" id="product_price" name="product_price" class="form-control" step="0.01" min="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>

            <div class="form-group">
                <label for="discount_price">Discount Price (ZAR) <small>(Optional)</small></label>
                <input type="number" id="discount_price" name="discount_price" class="form-control" step="0.01" min="0.00" value="<?php echo htmlspecialchars($product['discount_price'] ?? ''); ?>" placeholder="Leave empty or 0 to remove discount">
            </div>

            <div class="form-group">
                <label for="product_category">Category</label>
                <select id="product_category" name="product_category" class="form-control" required>
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
                <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" class="current-image">
            </div>

            <div class="form-group">
                <label for="product_image">Upload New Image (optional)</label>
                <input type="file" id="product_image" name="product_image" class="form-control-file" accept="image/png, image/jpeg, image/webp">
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>