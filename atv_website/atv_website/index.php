<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'home';

// 获取数据
$categories = getCategories();
$featuredProducts = getProducts(0, true, 8);
$latestNews = getNewsList(3);
$totalProducts = getProductCount();

$pageTitle = pageTitle('home');
$metaKeywords = metaKeywords('home');
$metaDescription = metaDescription('home');

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section - Full Screen -->
<section class="hero-section">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <div class="hero-badge">
                    <span style="font-size:16px;">&#127981;</span> Professional ATV Manufacturer Since 2008
                </div>
                <h1 class="hero-title">
                    Premium Off-Road
                    <span class="highlight">Vehicles</span>
                </h1>
                <p class="hero-desc">
                    We specialize in manufacturing high-quality ATVs, UTVs, Go Karts, and Dirt Bikes.
                    CE certified, OEM/ODM available. Serving global markets with reliable off-road solutions.
                </p>
                <div class="hero-buttons">
                    <a href="products.php" class="btn btn-primary btn-lg">Explore Products</a>
                    <a href="contact.php" class="btn btn-outline-white btn-lg">Contact Us</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <h4 data-counter="<?php echo $totalProducts; ?>">0</h4>
                        <p>Products</p>
                    </div>
                    <div class="stat-item">
                        <h4 data-counter="50">0</h4>
                        <p>Countries</p>
                    </div>
                    <div class="stat-item">
                        <h4 data-counter="16">0</h4>
                        <p>Years Exp.</p>
                    </div>
                </div>
            </div>
            <div class="hero-image-wrap">
                <div class="hero-image-main">
                    <div class="icon">&#127949;</div>
                    <p>ATV Product Hero Image</p>
                    <p class="hint">Upload your product image in admin panel</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose Us</h2>
            <p>Your trusted partner for premium off-road vehicles and exceptional service</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">&#127981;</div>
                <h3>Factory Direct</h3>
                <p>Own manufacturing facility with strict quality control and competitive factory pricing</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#9989;</div>
                <h3>CE Certified</h3>
                <p>All products meet European CE standards with complete certification documentation</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128666;</div>
                <h3>Global Shipping</h3>
                <p>Efficient logistics network delivering to over 50 countries worldwide</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">&#128295;</div>
                <h3>OEM / ODM</h3>
                <p>Custom design and branding services to meet your specific market requirements</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="products-section">
    <div class="container">
        <div class="section-title">
            <h2>Featured Products</h2>
            <p>Discover our best-selling ATVs, UTVs, and off-road vehicles</p>
        </div>

        <?php if (!empty($featuredProducts)): ?>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card">
                <?php if ($product['is_featured']): ?>
                <span class="product-badge">Featured</span>
                <?php endif; ?>
                <div class="product-image">
                    <?php if ($product['image']): ?>
                    <img src="<?php echo e($product['image']); ?>" alt="<?php echo e($product['title']); ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--light);color:var(--gray);font-size:48px;">&#127949;</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-category"><?php echo e($product['category_name'] ?? 'ATV'); ?></div>
                    <h3 class="product-title">
                        <a href="product.php?slug=<?php echo e($product['slug']); ?>"><?php echo e($product['title']); ?></a>
                    </h3>
                    <p class="product-summary"><?php echo e(truncate($product['summary'], 100)); ?></p>
                    <div class="product-meta">
                        <span class="product-model"><?php echo e($product['model_no'] ?: 'ATV-' . $product['id']); ?></span>
                        <a href="product.php?slug=<?php echo e($product['slug']); ?>" class="product-link">Details &rarr;</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:48px;">
            <a href="products.php" class="btn btn-secondary btn-lg">View All Products</a>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">&#127949;</div>
            <h3>No Products Yet</h3>
            <p>Products will appear here once added from the admin panel.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- About Preview Section -->
<section class="about-preview">
    <div class="container">
        <div class="about-grid">
            <div class="about-images">
                <div class="about-img">Factory Photo 1</div>
                <div class="about-img">Factory Photo 2</div>
                <div class="about-img">Factory Photo 3</div>
            </div>
            <div class="about-content">
                <h2>Leading <span class="highlight">ATV Manufacturer</span></h2>
                <p>
                    With over 16 years of experience, we are one of China's leading manufacturers of all-terrain vehicles.
                    Our state-of-the-art facility spans 50,000 square meters with advanced production lines.
                </p>
                <p>
                    We specialize in ATVs, UTVs, Go Karts, and Dirt Bikes, serving clients across Europe, North America,
                    South America, and Asia. Our commitment to quality and innovation has made us a trusted partner
                    for distributors worldwide.
                </p>
                <ul class="about-features">
                    <li>50,000 SQM Factory</li>
                    <li>ISO 9001 Certified</li>
                    <li>200+ Employees</li>
                    <li>CE/EEC Approved</li>
                    <li>OEM/ODM Service</li>
                    <li>Global Export</li>
                </ul>
                <a href="about.php" class="btn btn-primary">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="news-section">
    <div class="container">
        <div class="section-title">
            <h2>Latest News</h2>
            <p>Stay updated with our latest products, industry trends, and company updates</p>
        </div>

        <?php if (!empty($latestNews)): ?>
        <div class="news-grid">
            <?php foreach ($latestNews as $news): ?>
            <div class="news-card">
                <div class="news-image">
                    <?php if ($news['image']): ?>
                    <img src="<?php echo e($news['image']); ?>" alt="<?php echo e($news['title']); ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e0e0e0,#d0d0d0);color:var(--gray);font-size:14px;">&#128240; News Image</div>
                    <?php endif; ?>
                </div>
                <div class="news-content">
                    <div class="news-date"><?php echo formatDate($news['created_at']); ?></div>
                    <h3 class="news-title">
                        <a href="news_detail.php?slug=<?php echo e($news['slug']); ?>"><?php echo e($news['title']); ?></a>
                    </h3>
                    <p class="news-excerpt"><?php echo e(truncate($news['summary'], 140)); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:48px;">
            <a href="news.php" class="btn btn-outline">View All News</a>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">&#128240;</div>
            <h3>No News Yet</h3>
            <p>News articles will appear here once published from the admin panel.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
