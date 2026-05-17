<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'products';

// 获取参数
$categorySlug = $_GET['category'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = ITEMS_PER_PAGE;

// 获取分类信息
$categoryId = 0;
$categoryName = '';
if ($categorySlug) {
    $categories = getCategories();
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug) {
            $categoryId = $cat['id'];
            $categoryName = $cat['name_en'];
            break;
        }
    }
}

// 获取商品
$products = getProducts($categoryId, false, 0, $page, $perPage);
$totalProducts = getProductCount($categoryId);
$totalPages = ceil($totalProducts / $perPage);

// SEO
$pageTitle = pageTitle('products', $categoryName ? $categoryName . ' Products' : '');
$metaKeywords = metaKeywords('products');
$metaDescription = metaDescription('products');

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo $categoryName ? e($categoryName) : 'Our Products'; ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">/</span>
            <a href="products.php">Products</a>
            <?php if ($categoryName): ?>
            <span class="sep">/</span>
            <span><?php echo e($categoryName); ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Products Page -->
<section class="products-page">
    <div class="container">
        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-categories">
                <a href="products.php" class="<?php echo !$categorySlug ? 'active' : ''; ?>">All</a>
                <?php foreach (getCategories() as $cat): ?>
                <a href="products.php?category=<?php echo e($cat['slug']); ?>" class="<?php echo $categorySlug === $cat['slug'] ? 'active' : ''; ?>">
                    <?php echo e($cat['name_en']); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="filter-count"><?php echo $totalProducts; ?> products found</div>
        </div>
        
        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <?php if ($product['is_featured']): ?>
                <span class="product-badge">Featured</span>
                <?php endif; ?>
                <div class="product-image">
                    <?php if ($product['image']): ?>
                    <img src="<?php echo e($product['image']); ?>" alt="<?php echo e($product['title']); ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--light);color:var(--gray);font-size:14px;">
                        🏍️
                    </div>
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
                        <a href="product.php?slug=<?php echo e($product['slug']); ?>" class="product-link">Details →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): 
            $urlPattern = 'products.php?' . ($categorySlug ? 'category=' . $categorySlug . '&' : '') . 'page={page}';
            echo pagination($totalProducts, $page, $perPage, $urlPattern);
        endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏍️</div>
            <h3>No Products Found</h3>
            <p>No products available in this category yet.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
