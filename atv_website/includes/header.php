<?php
require_once __DIR__ . '/functions.php';
$siteName = getSetting('site_name', 'ATV POWER');
$siteSlogan = getSetting('site_slogan', 'Professional Off-Road Vehicle Manufacturer');
$currentPage = $currentPage ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? pageTitle($currentPage); ?></title>
    <meta name="keywords" content="<?php echo $metaKeywords ?? metaKeywords($currentPage); ?>">
    <meta name="description" content="<?php echo $metaDescription ?? metaDescription($currentPage); ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo $pageTitle ?? pageTitle($currentPage); ?>">
    <meta property="og:description" content="<?php echo $metaDescription ?? metaDescription($currentPage); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo SITE_VERSION; ?>">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php echo e($siteName); ?>",
        "description": "<?php echo e($siteSlogan); ?>",
        "url": "<?php echo SITE_URL; ?>",
        "logo": "<?php echo SITE_URL; ?>/assets/images/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo e(getSetting('company_phone')); ?>",
            "contactType": "sales",
            "availableLanguage": ["English"]
        }
    }
    </script>
</head>
<body class="page-<?php echo $currentPage; ?>">

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            <span><i class="icon-phone"></i> <?php echo e(getSetting('company_phone', '+86-579-12345678')); ?></span>
            <span><i class="icon-email"></i> <?php echo e(getSetting('company_email', 'sales@atvpower.com')); ?></span>
        </div>
        <div class="top-bar-right">
            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', getSetting('company_whatsapp', '')); ?>" target="_blank" class="whatsapp-link">
                <i class="icon-whatsapp"></i> WhatsApp
            </a>
            <?php if (getSetting('company_wechat')): ?>
            <span class="wechat-link"><i class="icon-wechat"></i> WeChat: <?php echo e(getSetting('company_wechat')); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="main-header" id="mainHeader">
    <div class="container">
        <div class="header-inner">
            <!-- Logo -->
            <a href="index.php" class="logo">
                <div class="logo-mark">A</div>
                <div class="logo-text">
                    <span class="logo-name"><?php echo e($siteName); ?></span>
                    <span class="logo-slogan"><?php echo e($siteSlogan); ?></span>
                </div>
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="main-nav" id="mainNav">
                <ul>
                    <?php echo navMenu($currentPage); ?>
                </ul>
            </nav>
            
            <!-- Header Actions -->
            <div class="header-actions">
                <a href="contact.php" class="btn btn-primary btn-sm">Get Quote</a>
                <!-- Mobile Menu Toggle -->
                <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
        <a href="index.php" class="logo">
            <div class="logo-mark">A</div>
            <span class="logo-name"><?php echo e($siteName); ?></span>
        </a>
        <button class="mobile-close" id="mobileClose" aria-label="Close Menu">&times;</button>
    </div>
    <nav class="mobile-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="news.php">News</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </nav>
    <div class="mobile-contact">
        <p><i class="icon-phone"></i> <?php echo e(getSetting('company_phone')); ?></p>
        <p><i class="icon-email"></i> <?php echo e(getSetting('company_email')); ?></p>
    </div>
</div>
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Main Content Start -->
<main>
