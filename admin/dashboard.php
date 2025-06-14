<?php
// admin/dashboard.php
require_once 'admin_auth.php';
$page_title = "Admin Dashboard";
include 'header.php';
?>

<section class="admin-dashboard">
    <div class="container">
        <div class="dashboard-intro">
            <h1 class="section-title">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>This is your command center. From here, you can manage your website's users, products, and view sales reports.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div>
                    <h3>User Management</h3>
                    <p>View, edit, and manage user accounts and their roles.</p>
                </div>
                <a href="manage_users.php" class="btn">Manage Users</a>
            </div>
            <div class="feature-card">
                <div>
                    <h3>Sales Reports</h3>
                    <p>Generate and view sales reports and website analytics.</p>
                </div>
                <a href="sales_reports.php" class="btn">View Reports</a>
            </div>
             <div class="feature-card">
                <div>
                    <h3>Product Management</h3>
                    <p>Add, edit, and manage all product listings on the site.</p>
                </div>
                <a href="manage_products.php" class="btn">Manage Products</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
