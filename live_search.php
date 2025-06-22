<?php
// File: EcommerceProject/live_search.php
require_once 'db_connect.php'; 

// Dynamically determine the base URL to make image and product links work correctly
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base_url == '/' || $base_url == '\\') {
    $base_url = ''; // Set to empty if the project is in the web root directory
}


if (isset($_GET['query'])) {
    $query = trim($_GET['query']);

    if (!empty($query)) {
        // Prepare a statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, price, image_path FROM products WHERE name LIKE ? LIMIT 5");
        $search_param = "%" . $query . "%";
        $stmt->bind_param("s", $search_param);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Loop through results and create an HTML link for each
            while ($product = $result->fetch_assoc()) {
                // Construct the correct image and product URLs using the dynamic base_url
                $image_url = $base_url . '/' . ltrim($product['image_path'], '/');
                $product_url = $base_url . '/src/pages/product_detail.php?id=' . $product['id'];

                echo '<a href="' . htmlspecialchars($product_url) . '" class="live-search-result-item">';
                echo '<img src="' . htmlspecialchars($image_url) . '" alt="' . htmlspecialchars($product['name']) . '">';
                echo '<div class="live-search-result-details">';
                echo '<h4>' . htmlspecialchars($product['name']) . '</h4>';
                echo '<p>R' . htmlspecialchars(number_format((float)$product['price'], 2)) . '</p>';
                echo '</div>';
                echo '</a>';
            }
        } else {
            // Show a 'no results' message
            echo '<div class="live-search-no-results">No results found.</div>';
        }
        $stmt->close();
    }
}
$conn->close();
?>