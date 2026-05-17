<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'news';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 9;

$newsList = getNewsList(0, $page, $perPage);
$totalNews = getNewsCount();
$totalPages = ceil($totalNews / $perPage);

$pageTitle = pageTitle('news');
$metaKeywords = metaKeywords('news');
$metaDescription = metaDescription('news');

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>News & Updates</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">/</span>
            <span>News</span>
        </div>
    </div>
</section>

<!-- News Page -->
<section class="news-page">
    <div class="container">
        <?php if (!empty($newsList)): ?>
        <div class="news-list">
            <?php foreach ($newsList as $news): ?>
            <div class="news-card">
                <div class="news-image">
                    <?php if ($news['image']): ?>
                    <img src="<?php echo e($news['image']); ?>" alt="<?php echo e($news['title']); ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e0e0e0,#d0d0d0);color:var(--gray);font-size:14px;">
                        📰 News Image
                    </div>
                    <?php endif; ?>
                </div>
                <div class="news-content">
                    <div class="news-date"><?php echo formatDate($news['created_at'], 'F d, Y'); ?></div>
                    <h3 class="news-title">
                        <a href="news_detail.php?slug=<?php echo e($news['slug']); ?>"><?php echo e($news['title']); ?></a>
                    </h3>
                    <p class="news-excerpt"><?php echo e(truncate($news['summary'], 160)); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): 
            echo pagination($totalNews, $page, $perPage, 'news.php?page={page}');
        endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📰</div>
            <h3>No News Yet</h3>
            <p>News articles will appear here once published from the admin panel.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
