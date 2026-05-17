<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'products';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: products.php');
    exit;
}

$product = getProduct($slug);
if (!$product) {
    header('Location: products.php');
    exit;
}

// 增加浏览量
incrementViews(table('products'), $product['id']);

// 获取相关商品
$relatedProducts = getRelatedProducts($product['category_id'], $product['id'], 4);

// 处理图库
$gallery = [];
if ($product['gallery']) {
    $gallery = json_decode($product['gallery'], true) ?: [];
}
if ($product['image']) {
    array_unshift($gallery, $product['image']);
    $gallery = array_unique($gallery);
}

// SEO - 使用商品自定义SEO或默认
$pageTitle = $product['meta_title'] ? e($product['meta_title']) : pageTitle('products', $product['title']);
$metaKeywords = $product['meta_keywords'] ?: metaKeywords('products');
$metaDescription = $product['meta_description'] ?: metaDescription('products', truncate($product['summary'], 160));

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo e($product['title']); ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">/</span>
            <a href="products.php">Products</a>
            <span class="sep">/</span>
            <a href="products.php?category=<?php echo e($product['category_name'] ? slugify($product['category_name']) : ''); ?>"><?php echo e($product['category_name'] ?? 'ATV'); ?></a>
            <span class="sep">/</span>
            <span><?php echo e($product['title']); ?></span>
        </div>
    </div>
</section>

<!-- Product Detail -->
<section class="product-detail">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <div class="product-gallery-main">
                    <?php if (!empty($gallery)): ?>
                    <img src="<?php echo e($gallery[0]); ?>" alt="<?php echo e($product['title']); ?>" id="mainImage">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--light);color:var(--gray);font-size:16px;">
                        🏍️ No Image
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (count($gallery) > 1): ?>
                <div class="product-gallery-thumbs">
                    <?php foreach ($gallery as $index => $img): ?>
                    <div class="gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>" data-src="<?php echo e($img); ?>">
                        <img src="<?php echo e($img); ?>" alt="<?php echo e($product['title']); ?> - <?php echo $index + 1; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="product-info-detail">
                <div class="product-meta-detail">
                    <span><?php echo e($product['category_name'] ?? 'ATV'); ?></span>
                    <span>Model: <?php echo e($product['model_no'] ?: 'N/A'); ?></span>
                    <span>Views: <?php echo $product['views']; ?></span>
                </div>
                
                <h1><?php echo e($product['title']); ?></h1>
                
                <?php if ($product['subtitle']): ?>
                <p style="color:var(--gray);margin-bottom:16px;"><?php echo e($product['subtitle']); ?></p>
                <?php endif; ?>
                
                <?php if ($product['price_display']): ?>
                <div class="product-price"><?php echo e($product['price_display']); ?></div>
                <?php elseif ($product['price'] > 0): ?>
                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                <?php endif; ?>
                
                <div class="product-description">
                    <?php echo nl2br(e($product['summary'])); ?>
                </div>
                
                <div class="product-actions">
                    <a href="contact.php?product=<?php echo e($product['slug']); ?>" class="btn btn-primary btn-lg">Send Inquiry</a>
                    <a href="contact.php" class="btn btn-outline btn-lg">Contact Us</a>
                </div>
                
                <!-- Specifications -->
                <?php if ($product['engine_type'] || $product['displacement'] || $product['power'] || $product['transmission']): ?>
                <div class="product-specs">
                    <h3>Quick Specifications</h3>
                    <table class="specs-table">
                        <?php if ($product['engine_type']): ?>
                        <tr><th>Engine Type</th><td><?php echo e($product['engine_type']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['displacement']): ?>
                        <tr><th>Displacement</th><td><?php echo e($product['displacement']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['power']): ?>
                        <tr><th>Power</th><td><?php echo e($product['power']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['transmission']): ?>
                        <tr><th>Transmission</th><td><?php echo e($product['transmission']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['drive_type']): ?>
                        <tr><th>Drive Type</th><td><?php echo e($product['drive_type']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['brakes']): ?>
                        <tr><th>Brakes</th><td><?php echo e($product['brakes']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['tires']): ?>
                        <tr><th>Tires</th><td><?php echo e($product['tires']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['weight']): ?>
                        <tr><th>Weight</th><td><?php echo e($product['weight']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['dimensions']): ?>
                        <tr><th>Dimensions</th><td><?php echo e($product['dimensions']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['colors']): ?>
                        <tr><th>Colors</th><td><?php echo e($product['colors']); ?></td></tr>
                        <?php endif; ?>
                        <?php if ($product['certifications']): ?>
                        <tr><th>Certifications</th><td><?php echo e($product['certifications']); ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Full Description -->
        <?php if ($product['description']): ?>
        <div style="margin-top:48px;">
            <h2 style="margin-bottom:20px;">Product Description</h2>
            <div class="news-article-content" style="max-width:900px;">
                <?php echo $product['description']; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Features -->
        <?php if ($product['features']): ?>
        <div style="margin-top:48px;">
            <h2 style="margin-bottom:20px;">Key Features</h2>
            <div class="news-article-content" style="max-width:900px;">
                <?php echo $product['features']; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Specifications Full -->
        <?php if ($product['specifications']): ?>
        <div style="margin-top:48px;">
            <h2 style="margin-bottom:20px;">Detailed Specifications</h2>
            <div class="news-article-content" style="max-width:900px;">
                <?php echo $product['specifications']; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Inquiry Section -->
        <div class="inquiry-section">
            <h3>Interested in this product?</h3>
            <p style="color:var(--gray);margin-bottom:20px;">Send us an inquiry and our team will get back to you within 24 hours.</p>
            <a href="contact.php?product=<?php echo e($product['slug']); ?>" class="btn btn-primary btn-lg">Send Inquiry Now</a>
        </div>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="related-section">
    <div class="container">
        <div class="section-title">
            <h2>Related Products</h2>
            <p>You may also be interested in these products</p>
        </div>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $rp): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($rp['image']): ?>
                    <img src="<?php echo e($rp['image']); ?>" alt="<?php echo e($rp['title']); ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--light);color:var(--gray);font-size:14px;">
                        🏍️
                    </div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-category"><?php echo e($rp['category_name'] ?? 'ATV'); ?></div>
                    <h3 class="product-title">
                        <a href="product.php?slug=<?php echo e($rp['slug']); ?>"><?php echo e($rp['title']); ?></a>
                    </h3>
                    <div class="product-meta">
                        <span class="product-model"><?php echo e($rp['model_no'] ?: 'ATV-' . $rp['id']); ?></span>
                        <a href="product.php?slug=<?php echo e($rp['slug']); ?>" class="product-link">Details →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
