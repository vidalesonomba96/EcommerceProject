<?php
// EcommerceProject/cart_handler.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php'; // For product validation if needed

header('Content-Type: application/json'); // Respond with JSON

// Initialize cart in session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- Helper function to get cart details ---
function getCartDetails() {
    $cart = $_SESSION['cart'] ?? [];
    $totalItems = 0;      // To count unique product types
    $totalQuantity = 0;   // To count total number of all units
    $grandTotal = 0.0;

    if (!empty($cart)) {
        $totalItems = count($cart); // Number of unique products
        foreach ($cart as $item) {
            if (isset($item['quantity']) && isset($item['price'])) {
                $totalQuantity += (int)$item['quantity'];
                $grandTotal += (float)$item['price'] * (int)$item['quantity'];
            }
        }
    }
    return [
        'cart' => array_values($cart), // Return as indexed array for easier JS iteration
        'totalItems' => $totalItems,
        'totalQuantity' => $totalQuantity,
        'grandTotal' => round($grandTotal, 2)
    ];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$productId = $_POST['product_id'] ?? $_GET['product_id'] ?? null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

$response = ['success' => false, 'message' => 'Invalid action.', 'cartDetails' => getCartDetails()];

// --- Add item to cart ---
if ($action === 'add' && $productId) {
    // **IMPORTANT: Security Enhancement**
    // In a production environment, you should fetch product details (name, price, image)
    // directly from your database using $productId to prevent manipulation.
    // For this example, we'll use client-sent data but with a note.

    $productName = $_POST['product_name'] ?? 'Unknown Product';
    $productPrice = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0.0;
    $productImage = $_POST['product_image'] ?? ''; // Path to image

    /*
    // Example: Fetching product details from DB (Recommended)
    $stmt = $conn->prepare("SELECT name, price, image_path FROM products WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($product_db = $result->fetch_assoc()) {
            $productName = $product_db['name'];
            $productPrice = (float)$product_db['price'];
            $productImage = $product_db['image_path']; // Ensure this is a web-accessible path
        } else {
            $response = ['success' => false, 'message' => 'Product not found in database.'];
            echo json_encode($response);
            $conn->close();
            exit;
        }
        $stmt->close();
    } else {
        $response = ['success' => false, 'message' => 'Database query error.'];
        echo json_encode($response);
        $conn->close();
        exit;
    }
    */
    
    if ($productPrice <= 0) {
         $response = ['success' => false, 'message' => 'Invalid product price. Cannot add to cart.'];
         echo json_encode($response);
         // $conn->close(); // Close connection if you opened it for DB check
         exit;
    }


    if (isset($_SESSION['cart'][$productId])) {
        // Product already in cart, update quantity
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
        $response['message'] = 'Product quantity updated in cart.';
    } else {
        // Product not in cart, add as new item
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $productName,
            'price' => $productPrice,
            'quantity' => $quantity,
            'image' => $productImage
        ];
        $response['message'] = 'Product added to cart.';
    }
    $response['success'] = true;
    $response['cartDetails'] = getCartDetails();

// --- Update item quantity ---
} elseif ($action === 'update' && $productId) {
    if ($quantity > 0) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
            $response = [
                'success' => true,
                'message' => 'Cart updated.',
                'cartDetails' => getCartDetails()
            ];
        } else {
            $response['message'] = 'Product not found in cart for update.';
        }
    } else {
        // If quantity is 0 or less, treat as removal
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $response = [
                'success' => true,
                'message' => 'Product removed from cart due to zero quantity.',
                'cartDetails' => getCartDetails()
            ];
        } else {
             $response['message'] = 'Product not found in cart.';
        }
    }

// --- Remove item from cart ---
} elseif ($action === 'remove' && $productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        $response = [
            'success' => true,
            'message' => 'Product removed from cart.',
            'cartDetails' => getCartDetails()
        ];
    } else {
        $response['message'] = 'Product not found in cart for removal.';
    }

// --- Get cart contents ---
} elseif ($action === 'get') {
    $response = [
        'success' => true,
        'message' => 'Cart details fetched.',
        'cartDetails' => getCartDetails()
    ];
}

// Output the JSON response
echo json_encode($response);

// Close the database connection if it was used explicitly for product lookups
if (isset($conn) && is_object($conn) && method_exists($conn, 'close')) {
   // $conn->close(); // Only close if you are sure it's not managed elsewhere or needed later in the script run (unlikely here with exit).
}
exit;
?>