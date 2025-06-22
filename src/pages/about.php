<?php
// src/pages/about.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = '../..';
$page_title = "About Us - ConnectMarket";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/src/styles/style.css">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($base_url); ?>/src/img/icon.png" type="image/x-icon">
</head>
<body>
    <?php include '../components/header/_header.php'; ?>

    <main class="site-main">

        <section class="about-hero">
            <div class="container">
                <h1>Connecting People, Powering Commerce</h1>
                <p>We are ConnectMarket, a community-driven marketplace dedicated to making buying and selling simple, secure, and enjoyable for everyone.</p>
            </div>
        </section>

        <section class="page-section bg-light">
            <div class="container">
                <h2 class="section-title4">Our Story</h2>
                <div class="about-content-section">
                    <p>
                        Founded on the simple idea that everyone should have access to a safe and vibrant online marketplace, ConnectMarket was born. We saw a need for a platform that wasn't just a directory of products, but a real community where individuals could connect over shared interests and unique items.
                    </p>
                    <p style="margin-top: 1rem;">
                        From humble beginnings, we've grown into a bustling hub for buyers and sellers across the nation. Our journey is fueled by a passion for technology and a deep-seated belief in the power of connection. We are committed to continuously improving our platform to better serve you.
                    </p>
                </div>
            </div>
        </section>

        <section class="page-section">
            <div class="container">
                <h2 class="section-title4">What We Stand For</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-users icon"></i>
                        <h3>Community First</h3>
                        <p>We believe in building a supportive and respectful environment where every member feels valued.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-shield-alt icon"></i>
                        <h3>Unwavering Trust</h3>
                        <p>Your security is our top priority. We implement robust measures to ensure every transaction is safe.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-lightbulb icon"></i>
                        <h3>Constant Innovation</h3>
                        <p>We are always exploring new ways to make your experience on ConnectMarket faster, easier, and better.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="page-section bg-light">
            <div class="container">
                <h2 class="section-title4">Meet the Team</h2>
                <div class="team-grid">
                    <div class="team-member-card">
                        <img src="../img/vidal.jpg" alt="Team Member  Vidal Esono">
                        <div class="team-member-info">
                            <h3>Vidal Esono</h3>
                            <span class="title">Founder & CEO</span>
                            <div class="team-member-socials">
                                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-member-card">
                        <img src="" alt="Team Member  Ana Maria">
                        <div class="team-member-info">
                            <h3>Ana Maria</h3>
                            <span class="title">Head of Operations</span>
                            <div class="team-member-socials">
                                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="team-member-card">
                        <img src="../img/ceci.jpg" alt="Team Member  Cecilia Mercedes">
                        <div class="team-member-info">
                            <h3>Cecilia Mercedes</h3>
                            <span class="title">Lead Developer</span>
                            <div class="team-member-socials">
                                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="page-section">
            <div class="container">
                <div class="cta-section">
                    <h2>Join Our Community Today!</h2>
                    <p>Whether you're looking to find a hidden gem or start your own online business, you've come to the right place.</p>
                    <a href="<?php echo htmlspecialchars($base_url); ?>/src/pages/register.php" class="btn btn-primary">Get Started</a>
                </div>
            </div>
        </section>

    </main>

    <?php include '../components/footer/_footer.php'; ?>
    <script src="<?php echo htmlspecialchars($base_url); ?>/src/scripts/script.js" defer></script>
</body>
</html>