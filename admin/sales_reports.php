<?php
// admin/sales_reports.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

$page_title = "Sales Reports";
include 'header.php';

// For simplicity, this report shows total sales.
// You can expand this with more complex queries for different date ranges, etc.
$sql = "SELECT
            (SELECT COUNT(*) FROM orders) as total_transactions,
            (SELECT SUM(total_amount) FROM orders) as total_revenue,
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM products) as total_products";

$result = $conn->query($sql);
$report_data = $result->fetch_assoc();
?>

<section class="features-section admin-features">
    <div class="container">
        <div class="report-header">
            <h1 class="section-title">Sales Reports</h1>
            <a href="generate_report.php" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-alt"></i> Generate Text Report
            </a>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <h3 class="feature-title">Total Transactions</h3>
                <p class="feature-description" style="font-size: 1.75rem; color: white; font-weight: bold;"><?php echo $report_data['total_transactions'] ?? 0; ?></p>
            </div>
            <div class="feature-card">
                <h3 class="feature-title">Total Revenue</h3>
                <p class="feature-description" style="font-size: 1.75rem; color: white; font-weight: bold;">R<?php echo number_format($report_data['total_revenue'] ?? 0, 2); ?></p>
            </div>
            <div class="feature-card">
                <h3 class="feature-title">Total Users</h3>
                <p class="feature-description" style="font-size: 1.75rem; color: white; font-weight: bold;"><?php echo $report_data['total_users'] ?? 0; ?></p>
            </div>
            <div class="feature-card">
                <h3 class="feature-title">Total Products</h3>
                <p class="feature-description" style="font-size: 1.75rem; color: white; font-weight: bold;"><?php echo $report_data['total_products'] ?? 0; ?></p>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
