<?php
// File: EcommerceProject/search_handler.php
session_start();
require_once 'db_connect.php';

$base_url = '/EcommerceProject';
$query = '';
$results = [];

if (isset($_GET['query'])) {
    $query = trim($_GET['query']);

    if (!empty($query)) {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $search_param = "%" . $query . "%";
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        $result_set = $stmt->get_result();

        if ($result_set->num_rows > 0) {
            while ($row = $result_set->fetch_assoc()) {
                $results[] = $row;
            }
        }
        $stmt->close();
    }
}

// Store results in session to pass to the results page
$_SESSION['search_results'] = $results;
$_SESSION['search_query'] = $query;

// Redirect to the search results page
header("Location: " . $base_url . "/src/pages/search_results.php");
exit();
?>