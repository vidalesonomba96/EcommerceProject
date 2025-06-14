<?php
// admin/generate_report.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

// Fetch the report data
$sql = "SELECT
            (SELECT COUNT(*) FROM orders) as total_transactions,
            (SELECT SUM(total_amount) FROM orders) as total_revenue,
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM products) as total_products";
$result = $conn->query($sql);
$report_data = $result->fetch_assoc();

// Prepare the report content
$report_content = "ConnectMarket Sales & Activity Report\n";
$report_content .= "========================================\n";
$report_content .= "Generated on: " . date("Y-m-d H:i:s") . "\n\n";
$report_content .= "Total Transactions: " . ($report_data['total_transactions'] ?? 0) . "\n";
$report_content .= "Total Revenue: R " . number_format($report_data['total_revenue'] ?? 0, 2) . "\n";
$report_content .= "Total Registered Users: " . ($report_data['total_users'] ?? 0) . "\n";
$report_content .= "Total Products Listed: " . ($report_data['total_products'] ?? 0) . "\n";

// Set headers to force download
$filename = "ConnectMarket_Report_" . date("Y-m-d") . ".txt";
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output the content
echo $report_content;
exit();
?>
