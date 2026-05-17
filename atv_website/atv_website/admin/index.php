<?php
$currentNav = 'dashboard';
$pageTitle = '仪表盘';
require_once __DIR__ . '/header.php';

// 获取最新数据
$recentProducts = getProducts(0, false, 5);
$recentNews = getNewsList(5);
$recentInquiries = [];
try {
    $pdo = getDB();
    $recentInquiries = $pdo->query("SELECT * FROM " . table('inquiries') . " ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (Exception $e) {}
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">🏍️</div>
        <div class="stat-info">
            <h3><?php echo $stats['products']; ?></h3>
            <p>商品总数</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">📰</div>
        <div class="stat-info">
            <h3><?php echo $stats['news']; ?></h3>
            <p>新闻文章</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">📨</div>
        <div class="stat-info">
            <h3><?php echo $stats['inquiries']; ?></h3>
            <p>询盘总数</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">📨</div>
        <div class="stat-info">
            <h3><?php echo $stats['unread_inquiries']; ?></h3>
            <p>未读询盘</p>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <!-- Recent Products -->
    <div class="card">
        <div class="card-header">
            <h3>最新商品</h3>
            <a href="products.php" class="btn btn-sm btn-light">查看全部</a>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (!empty($recentProducts)): ?>
            <table class="data-table">
                <?php foreach ($recentProducts as $p): ?>
                <tr>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <?php if ($p['image']): ?>
                            <img src="<?php echo e($p['image']); ?>" style="width:48px;height:36px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                            <div style="width:48px;height:36px;background:var(--admin-light);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:18px;">🏍️</div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight:500;"><?php echo e(truncate($p['title'], 35)); ?></div>
                                <div style="font-size:12px;color:var(--admin-text-light);"><?php echo e($p['category_name'] ?? 'ATV'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right;padding:12px 16px;">
                        <span class="status <?php echo $p['is_show'] ? 'active' : 'inactive'; ?>">
                            <span class="status-dot"></span>
                            <?php echo $p['is_show'] ? '显示' : '隐藏'; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
            <div class="empty-state" style="padding:32px;">
                <p>暂无商品，<a href="product_edit.php" style="color:var(--admin-primary);">添加商品</a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Inquiries -->
    <div class="card">
        <div class="card-header">
            <h3>最新询盘</h3>
            <a href="inquiries.php" class="btn btn-sm btn-light">查看全部</a>
        </div>
        <div class="card-body" style="padding:0;">
            <?php if (!empty($recentInquiries)): ?>
            <table class="data-table">
                <?php foreach ($recentInquiries as $inq): ?>
                <tr>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:500;"><?php echo e(truncate($inq['name'], 25)); ?></div>
                        <div style="font-size:12px;color:var(--admin-text-light);"><?php echo e(truncate($inq['message'], 40)); ?></div>
                    </td>
                    <td style="text-align:right;padding:12px 16px;white-space:nowrap;">
                        <span class="status <?php echo $inq['is_read'] ? 'active' : 'inactive'; ?>">
                            <span class="status-dot"></span>
                            <?php echo $inq['is_read'] ? '已读' : '新'; ?>
                        </span>
                        <div style="font-size:11px;color:var(--admin-text-light);margin-top:4px;"><?php echo formatDate($inq['created_at']); ?></div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
            <div class="empty-state" style="padding:32px;">
                <p>暂无询盘</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <h3>快速操作</h3>
    </div>
    <div class="card-body">
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="product_edit.php" class="btn btn-primary">+ 添加商品</a>
            <a href="news_edit.php" class="btn btn-success">+ 添加新闻</a>
            <a href="inquiries.php" class="btn btn-info">查看询盘</a>
            <a href="seo.php" class="btn btn-warning">SEO设置</a>
            <a href="settings.php" class="btn btn-secondary">网站设置</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
