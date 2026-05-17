<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'about';

$pageTitle = pageTitle('about');
$metaKeywords = metaKeywords('about');
$metaDescription = metaDescription('about');

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>About Us</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">/</span>
            <span>About Us</span>
        </div>
    </div>
</section>

<!-- About Page -->
<section class="about-page">
    <div class="container">
        <div class="about-content-full">
            <h2>Company Profile</h2>
            <p>
                <strong><?php echo e(getSetting('site_name', 'ATV POWER')); ?></strong> is a leading manufacturer and exporter of all-terrain vehicles (ATVs), 
                utility task vehicles (UTVs), go karts, and dirt bikes based in Yongkang City, Zhejiang Province, China. 
                Established in 2008, we have over 16 years of experience in the powersports industry.
            </p>
            <p>
                Our state-of-the-art manufacturing facility covers 50,000 square meters and is equipped with advanced 
                production lines, testing equipment, and a dedicated R&D team. We employ over 200 skilled workers and 
                engineers who are committed to producing high-quality off-road vehicles that meet international standards.
            </p>
            
            <div class="about-values">
                <div class="value-card">
                    <div class="value-icon">🎯</div>
                    <h3>Our Mission</h3>
                    <p>To provide the highest quality off-road vehicles that deliver exceptional performance, safety, and value to our customers worldwide.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">👁️</div>
                    <h3>Our Vision</h3>
                    <p>To become the world's most trusted brand in the powersports industry through innovation, quality, and customer satisfaction.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">⭐</div>
                    <h3>Core Values</h3>
                    <p>Quality first, customer-centric, integrity in business, and continuous innovation drive everything we do.</p>
                </div>
            </div>
            
            <h2>Why Choose Us</h2>
            <p>
                <strong>Quality Assurance:</strong> Every vehicle undergoes rigorous testing and quality control procedures 
                before leaving our factory. We are ISO 9001 certified and all our products carry CE and EEC certifications 
                for the European market, as well as EPA compliance for North America.
            </p>
            <p>
                <strong>OEM & ODM Services:</strong> We offer comprehensive customization services including branding, 
                color schemes, specifications tuning, and packaging design. Our engineering team can develop custom 
                solutions tailored to your specific market requirements.
            </p>
            <p>
                <strong>Global Reach:</strong> We export to over 50 countries across Europe, North America, South America, 
                Australia, and Asia. Our efficient logistics network ensures timely delivery to any destination worldwide.
            </p>
            <p>
                <strong>After-Sales Support:</strong> We provide comprehensive technical support, spare parts supply, 
                and warranty service. Our dedicated customer service team is always ready to assist you with any questions 
                or concerns.
            </p>
            
            <h2>Our Factory</h2>
            <p>
                Our modern production facility features advanced manufacturing equipment including CNC machining centers, 
                automated welding robots, powder coating lines, and professional assembly lines. We have the capacity 
                to produce over 50,000 units annually, ensuring we can meet large volume orders with consistent quality.
            </p>
            
            <h2>Certifications</h2>
            <p>
                We maintain strict quality management systems and hold the following certifications:
                ISO 9001:2015 Quality Management System, CE Certification (EU), EEC Approval (EU), 
                EPA Certification (USA), and CCC Certification (China). All products undergo 100% inspection 
                before shipment.
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
