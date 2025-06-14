<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '../..'; // Relative path from src/pages/ to project root
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Connect Market</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../styles/style.css"> 
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
</head>
<body>

    <?php include '../components/header/_header.php'; ?> 

    <main class="site-main">
        <section class="about-us-section" style="padding-top: 3rem; padding-bottom: 3rem; background-color: #f9fafb;">
            <div class="container">
                <h1 class="section-title" style="margin-bottom: 2.5rem; color: #374151;">Connect Market, Where Buyers and Sellers truly-Connect!</h1>
                <div class="about-content" style="background-color: #ffffff; padding: 2rem; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); font-size: 1rem; line-height: 1.6;">
                    <p style="margin-bottom: 1.5rem;">
                        Welcome to ConnectMarket, your premier peer-to-peer marketplace designed to connect buyers and sellers in a seamless, secure, and vibrant community.
                        We believe in the power of connection and the joy of discovering unique items while empowering individuals to turn their pre-loved goods or creative crafts into opportunities.
                    </p>
                    <h2 style="font-size: 1.75rem; font-weight: 600; margin-top: 2.5rem; margin-bottom: 1rem; color: #4F46E5;" class="gradient-text">Our Mission</h2>
                    <p style="margin-bottom: 1.5rem;">
                        Our mission is to provide an accessible and trustworthy platform where anyone can easily buy and sell a wide variety of goods. We aim to foster a supportive environment that champions local economies and sustainable consumption by giving items a second life. Our goal is to simplify the C2C e-commerce experience, making it enjoyable and beneficial for all users.
                    </p>
                    <h2 style="font-size: 1.75rem; font-weight: 600; margin-top: 2.5rem; margin-bottom: 1rem; color: #4F46E5;" class="gradient-text">Our Vision</h2>
                    <p style="margin-bottom: 1.5rem;">
                        We envision ConnectMarket as the go-to online destination for C2C commerce, celebrated for its diverse offerings, user-friendly experience, and steadfast commitment to security. We strive to continuously innovate and adapt to the evolving needs of our community, making online trading not just a transaction, but a positive and rewarding interaction for everyone involved.
                    </p>
                    <h2 style="font-size: 1.75rem; font-weight: 600; margin-top: 2.5rem; margin-bottom: 1rem; color: #4F46E5;" class="gradient-text">Why Choose ConnectMarket?</h2>
                    <p style="margin-bottom: 1rem;">At ConnectMarket, we're dedicated to providing the best possible experience for our users. Here's what sets us apart:</p>
                    <ul style="list-style: none; padding-left: 0; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 1rem; display: flex; align-items: flex-start;">
                            <i class="fas fa-shield-alt" style="color: #4ade80; font-size: 1.5rem; margin-right: 0.75rem; margin-top: 0.25rem;"></i>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem; font-size: 1.125rem;">Secure Transactions</h4>
                                <p>Shop and sell with confidence thanks to our secure payment gateway and fraud protection measures.</p>
                            </div>
                        </li>
                        <li style="margin-bottom: 1rem; display: flex; align-items: flex-start;">
                            <i class="fas fa-users" style="color: #facc15; font-size: 1.5rem; margin-right: 0.75rem; margin-top: 0.25rem;"></i>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem; font-size: 1.125rem;">Vibrant Community</h4>
                                <p>Connect with fellow buyers and sellers, discover unique items, and share your passion within a supportive network.</p>
                            </div>
                        </li>
                        <li style="margin-bottom: 1rem; display: flex; align-items: flex-start;">
                            <i class="fas fa-rocket" style="color: #f472b6; font-size: 1.5rem; margin-right: 0.75rem; margin-top: 0.25rem;"></i>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem; font-size: 1.125rem;">Easy Listing & Selling</h4>
                                <p>List your items in minutes with our intuitive platform and reach thousands of potential buyers effortlessly.</p>
                            </div>
                        </li>
                         <li style="margin-bottom: 0rem; display: flex; align-items: flex-start;">
                            <i class="fas fa-headset" style="color: #60a5fa; font-size: 1.5rem; margin-right: 0.75rem; margin-top: 0.25rem;"></i>
                            <div>
                                <h4 style="font-weight: 600; margin-bottom: 0.25rem; font-size: 1.125rem;">Dedicated Support</h4>
                                <p>Our friendly support team is always ready to assist you with any queries or issues, ensuring a smooth experience on ConnectMarket.</p>
                            </div>
                        </li>
                    </ul>
                    <p style="text-align: center; font-size: 1.125rem; margin-top: 2.5rem; font-weight: 500;">
                        Join the ConnectMarket family today and become a part of our dynamic and trustworthy C2C ecosystem!
                    </p>
                </div>
            </div>
        </section>
    </main>

    <div id="footer-placeholder"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initializeDynamicContentScripts === 'function') {
            window.initializeDynamicContentScripts();
        }
        function loadHTML(filePath, placeholderId, callback) {
            fetch(filePath)
                .then(response => response.ok ? response.text() : Promise.reject(`Failed: ${filePath}`))
                .then(data => {
                    const el = document.getElementById(placeholderId);
                    if (el) el.innerHTML = data;
                    if (callback) callback();
                })
                .catch(error => console.error(`Error loading HTML:`, error));
        }
        // Path from src/pages/ to src/components/ for _footer.html
        loadHTML('../components/footer/_footer.html', 'footer-placeholder', function() {
            const currentYearSpan = document.getElementById('currentYear');
            if (currentYearSpan) currentYearSpan.textContent = new Date().getFullYear();
        });
    });
    </script>
    <script src="../scripts/script.js" defer></script> 
    <?php
// ... (rest of your page content)
// $base_url is already defined at the top of src/pages/about.php as '../..'
include '../components/footer/_footer.php'; // Corrected path for include
?>

</body>
</html>