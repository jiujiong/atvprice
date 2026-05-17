<?php
$categories = getCategories();
$latestNews = getNewsList(3);
$siteName = getSetting('site_name', 'ATV POWER');
?>
</main><!-- End Main Content -->

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Power Your Business?</h2>
            <p>Contact us today for wholesale pricing and customized ATV solutions for your market.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-white">Get a Free Quote</a>
                <a href="products.php" class="btn btn-outline-white">Browse Products</a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="main-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-col footer-about">
                    <div class="footer-logo">
                        <div class="logo-mark">A</div>
                        <span class="logo-name"><?php echo e($siteName); ?></span>
                    </div>
                    <p class="footer-desc"><?php echo e(getSetting('footer_about', 'Leading manufacturer of ATVs, UTVs, Go Karts and Dirt Bikes. We provide high quality off-road vehicles with CE certification.')); ?></p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="icon-facebook"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="icon-linkedin"></i></a>
                        <a href="#" aria-label="YouTube"><i class="icon-youtube"></i></a>
                        <a href="#" aria-label="Instagram"><i class="icon-instagram"></i></a>
                    </div>
                </div>
                
                <!-- Product Categories -->
                <div class="footer-col">
                    <h4>Products</h4>
                    <ul class="footer-links">
                        <?php foreach ($categories as $cat): ?>
                        <li><a href="products.php?category=<?php echo $cat['slug']; ?>"><?php echo e($cat['name_en']); ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="products.php">View All Products</a></li>
                    </ul>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="news.php">News & Updates</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="products.php">Product Catalog</a></li>
                        <li><a href="contact.php">Become a Dealer</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="footer-col footer-contact">
                    <h4>Contact Us</h4>
                    <ul class="contact-list">
                        <li>
                            <i class="icon-location"></i>
                            <span><?php echo e(getSetting('company_address', 'No.168 Industrial Zone, Yongkang City, Zhejiang Province, China')); ?></span>
                        </li>
                        <li>
                            <i class="icon-phone"></i>
                            <span><?php echo e(getSetting('company_phone', '+86-579-12345678')); ?></span>
                        </li>
                        <li>
                            <i class="icon-email"></i>
                            <span><?php echo e(getSetting('company_email', 'sales@atvpower.com')); ?></span>
                        </li>
                        <li>
                            <i class="icon-whatsapp"></i>
                            <span>WhatsApp: <?php echo e(getSetting('company_whatsapp', '+8613800138000')); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <p><?php echo e(getSetting('footer_copyright', '© 2026 ATV Power. All Rights Reserved.')); ?></p>
            <div class="footer-cert">
                <span>CE Certified</span>
                <span>ISO 9001</span>
                <span>EEC Approved</span>
                <span>EPA Compliant</span>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" aria-label="Back to Top">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>

<!-- Scripts -->
<script src="assets/js/main.js?v=<?php echo SITE_VERSION; ?>"></script>

<!-- Analytics -->
<?php echo getSetting('analytics_code', ''); ?>

</body>
</html>
