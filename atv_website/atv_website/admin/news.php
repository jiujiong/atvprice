<?php
$currentNav = 'news';
$pageTitle = '新闻管理';
require_once __DIR__ . '/header.php';

// 删除处理
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT image FROM " . table('news') . " WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        if ($article && $article['image']) {
            deleteImage($article['image'], 'news');
        }
        $pdo->prepare("DELETE FROM " . table('news') . " WHERE id = ?")->execute([$id]);
        $success = '新闻已删除';
    } catch (Exception $e) {
        $error = '删除失败: ' . $e->getMessage();
    }
}

// 搜索
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;

try {
    $pdo = getDB();
    $where = "WHERE 1=1";
    $params = [];
    if ($search) {
        $where .= " AND (title LIKE ? OR slug LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM " . table('news') . " {$where}");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT * FROM " . table('news') . " {$where} ORDER BY created_at DESC LIMIT {$offset}, {$perPage}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $newsList = $stmt->fetchAll();
    
    $totalPages = ceil($total / $perPage);
} catch (Exception $e) {
    $error = '数据加载失败';
    $newsList = [];
    $total = 0;
}
?>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
<div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>新闻列表</h3>
        <a href="news_edit.php" class="btn btn-primary">+ 添加新闻</a>
    </div>
    <div class="card-body">
        <div class="toolbar">
            <form method="get" action="" class="search-box">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="搜索新闻标题...">
            </form>
            <span style="color:var(--admin-text-light);font-size:14px;">共 <?php echo $total; ?> 篇文章</span>
        </div>
        
        <?php if (!empty($newsList)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>图片</th>
                        <th>标题</th>
                        <th>作者</th>
                        <th>浏览</th>
                        <th>状态</th>
                        <th>发布时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($newsList as $n): ?>
                    <tr>
                        <td><?php echo $n['id']; ?></td>
                        <td>
                            <?php if ($n['image']): ?>
                            <img src="<?php echo e($n['image']); ?>" alt="" style="width:60px;height:45px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                            <div style="width:60px;height:45px;background:var(--admin-light);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;">📰</div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo e(truncate($n['title'], 45)); ?></strong></td>
                        <td><?php echo e($n['author'] ?: '-'); ?></td>
                        <td><?php echo $n['views']; ?></td>
                        <td>
                            <span class="status <?php echo $n['is_show'] ? 'active' : 'inactive'; ?>">
                                <span class="status-dot"></span>
                                <?php echo $n['is_show'] ? '显示' : '隐藏'; ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($n['created_at']); ?></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="news_edit.php?id=<?php echo $n['id']; ?>" class="btn btn-sm btn-primary">编辑</a>
                                <a href="../news_detail.php?slug=<?php echo e($n['slug']); ?>" target="_blank" class="btn btn-sm btn-light">预览</a>
                                <a href="?delete=<?php echo $n['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()">删除</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): 
            $urlPattern = 'news.php?' . ($search ? 'search=' . urlencode($search) . '&' : '') . 'page={page}';
            echo pagination($total, $page, $perPage, $urlPattern);
        endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📰</div>
            <h3>暂无新闻</h3>
            <p><?php echo $search ? '没有匹配搜索结果的新闻' : '点击"添加新闻"按钮创建您的第一篇文章'; ?></p>
            <?php if (!$search): ?>
            <a href="news_edit.php" class="btn btn-primary" style="margin-top:16px;">+ 添加新闻</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
