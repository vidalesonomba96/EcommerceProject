<?php
// File: EcommerceProject/src/components/footer/_footer.php
// This file expects $base_url to be defined by the including PHP page.
if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <h3 class="footer-heading gradient-text">ConnectMarket</h3>
                <p>Your friendly C2C marketplace for buying and selling unique goods. Join our community today!</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-links-column">
                <h4 class="footer-heading">Quick Links</h4>
                <ul>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/about.php">About Us</a></li>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/contact.php">Contact Us</a></li> 
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/faq.php">FAQ</a></li> 
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/how_it_works.php">How It Works</a></li> 
                </ul>
            </div>
            <div class="footer-links-column">
                <h4 class="footer-heading">For Buyers</h4>
                <ul>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/buyer_guide.php">Buyer Guide</a></li>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/safety_tips.php">Safety Tips</a></li>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/report_issue.php">Report an Issue</a></li>
                </ul>
            </div>
            <div class="footer-links-column">
                <h4 class="footer-heading">For Sellers</h4>
                <ul>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/seller_guide.php">Seller Guide</a></li>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/fees.php">Fees &amp; Pricing</a></li>
                    <li><a href="<?php echo htmlspecialchars($base_url); ?>/seller_dashboard.php">Seller Dashboard</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <span id="currentYear"><?php echo date("Y"); ?></span> ConnectMarket. All rights reserved.</p>
            <p>
                <a href="<?php echo htmlspecialchars($base_url); ?>/privacy.php">Privacy Policy</a> |
                <a href="<?php echo htmlspecialchars($base_url); ?>/terms.php">Terms of Service</a>
            </p>
        </div>
        
    </div>
</footer>