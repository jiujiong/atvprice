<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'news';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: news.php');
    exit;
}

$article = getNews($slug);
if (!$article) {
    header('Location: news.php');
    exit;
}

// 增加浏览量
incrementViews(table('news'), $article['id']);

// 获取相关新闻
$relatedNews = getRelatedNews($article['id'], 5);

// SEO
$pageTitle = $article['meta_title'] ? e($article['meta_title']) : pageTitle('news', $article['title']);
$metaKeywords = $article['meta_keywords'] ?: metaKeywords('news');
$metaDescription = $article['meta_description'] ?: metaDescription('news', truncate($article['summary'], 160));

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>News & Updates</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">/</span>
            <a href="news.php">News</a>
            <span class="sep">/</span>
            <span><?php echo e(truncate($article['title'], 40)); ?></span>
        </div>
    </div>
</section>

<!-- News Detail -->
<section class="news-detail">
    <div class="container">
        <div class="news-detail-grid">
            <!-- Article -->
            <article class="news-article">
                <?php if ($article['image']): ?>
                <div class="news-article-image">
                    <img src="<?php echo e($article['image']); ?>" alt="<?php echo e($article['title']); ?>">
                </div>
                <?php endif; ?>
                
                <div class="news-article-meta">
                    <span>📅 <?php echo formatDate($article['created_at'], 'F d, Y'); ?></span>
                    <?php if ($article['author']): ?>
                    <span>👤 <?php echo e($article['author']); ?></span>
                    <?php endif; ?>
                    <span>👁️ <?php echo $article['views']; ?> views</span>
                </div>
                
                <h1><?php echo e($article['title']); ?></h1>
                
                <div class="news-article-content">
                    <?php if ($article['summary']): ?>
                    <p><strong><?php echo e($article['summary']); ?></strong></p>
                    <?php endif; ?>
                    <?php echo $article['content']; ?>
                </div>
                
                <!-- Share -->
                <div style="margin-top:40px;padding-top:24px;border-top:1px solid var(--gray-light);">
                    <p style="font-size:14px;color:var(--gray);margin-bottom:12px;">Share this article:</p>
                    <div style="display:flex;gap:10px;">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-sm" style="background:#3b5998;color:white;">Facebook</a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>" target="_blank" class="btn btn-sm" style="background:#1da1f2;color:white;">Twitter</a>
                        <a href="https://www.linkedin.com/shareArticle?url=<?php echo urlencode(SITE_URL . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-sm" style="background:#0077b5;color:white;">LinkedIn</a>
                        <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' ' . SITE_URL . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn btn-sm" style="background:#25d366;color:white;">WhatsApp</a>
                    </div>
                </div>
            </article>
            
            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Latest News -->
                <div class="sidebar-widget">
                    <h4>Latest News</h4>
                    <?php foreach ($relatedNews as $rn): ?>
                    <div class="sidebar-news-item">
                        <?php if ($rn['image']): ?>
                        <div class="sidebar-news-thumb">
                            <img src="<?php echo e($rn['image']); ?>" alt="<?php echo e($rn['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="sidebar-news-info">
                            <h5><a href="news_detail.php?slug=<?php echo e($rn['slug']); ?>"><?php echo e(truncate($rn['title'], 45)); ?></a></h5>
                            <div class="date"><?php echo formatDate($rn['created_at'], 'M d, Y'); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- CTA Widget -->
                <div class="sidebar-widget" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white;text-align:center;">
                    <h4 style="color:white;border-color:rgba(255,255,255,0.3);">Need Help?</h4>
                    <p style="font-size:14px;margin-bottom:20px;opacity:0.9;">Contact our sales team for product inquiries and wholesale pricing.</p>
                    <a href="contact.php" class="btn btn-white" style="width:100%;">Contact Us</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
