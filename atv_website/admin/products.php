<?php
$currentNav = 'products';
$pageTitle = '商品管理';
require_once __DIR__ . '/header.php';

// 删除处理
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $pdo = getDB();
        // 获取图片路径并删除
        $stmt = $pdo->prepare("SELECT image, gallery FROM " . table('products') . " WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product) {
            if ($product['image']) deleteImage($product['image'], 'products');
            if ($product['gallery']) {
                $gallery = json_decode($product['gallery'], true) ?: [];
                foreach ($gallery as $img) deleteImage($img, 'products');
            }
        }
        $pdo->prepare("DELETE FROM " . table('products') . " WHERE id = ?")->execute([$id]);
        $success = '商品已删除';
    } catch (Exception $e) {
        $error = '删除失败: ' . $e->getMessage();
    }
}

// 搜索
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;

// 构建查询
$where = "WHERE 1=1";
$params = [];
if ($search) {
    $where .= " AND (p.title LIKE ? OR p.model_no LIKE ? OR p.slug LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

try {
    $pdo = getDB();
    
    // 总数
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM " . table('products') . " p {$where}");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    
    // 数据
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT p.*, c.name_en as category_name FROM " . table('products') . " p 
            LEFT JOIN " . table('categories') . " c ON p.category_id = c.id 
            {$where} ORDER BY p.sort_order ASC, p.id DESC LIMIT {$offset}, {$perPage}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    $totalPages = ceil($total / $perPage);
} catch (Exception $e) {
    $error = '数据加载失败: ' . $e->getMessage();
    $products = [];
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
        <h3>商品列表</h3>
        <a href="product_edit.php" class="btn btn-primary">+ 添加商品</a>
    </div>
    <div class="card-body">
        <!-- Toolbar -->
        <div class="toolbar">
            <form method="get" action="" class="search-box">
                <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="搜索商品名称、型号...">
            </form>
            <span style="color:var(--admin-text-light);font-size:14px;">共 <?php echo $total; ?> 个商品</span>
        </div>
        
        <?php if (!empty($products)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>图片</th>
                        <th>商品名称</th>
                        <th>分类</th>
                        <th>型号</th>
                        <th>排序</th>
                        <th>状态</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td>
                            <?php if ($p['image']): ?>
                            <img src="<?php echo e($p['image']); ?>" alt="">
                            <?php else: ?>
                            <div style="width:60px;height:45px;background:var(--admin-light);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:20px;">🏍️</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo e(truncate($p['title'], 40)); ?></strong>
                            <?php if ($p['is_featured']): ?>
                            <span style="background:var(--admin-primary);color:white;font-size:10px;padding:2px 8px;border-radius:50px;margin-left:6px;">精选</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($p['category_name'] ?? '未分类'); ?></td>
                        <td><code><?php echo e($p['model_no'] ?: '-'); ?></code></td>
                        <td><?php echo $p['sort_order']; ?></td>
                        <td>
                            <span class="status <?php echo $p['is_show'] ? 'active' : 'inactive'; ?>">
                                <span class="status-dot"></span>
                                <?php echo $p['is_show'] ? '显示' : '隐藏'; ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($p['updated_at']); ?></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="product_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">编辑</a>
                                <a href="../product.php?slug=<?php echo e($p['slug']); ?>" target="_blank" class="btn btn-sm btn-light">预览</a>
                                <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()">删除</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): 
            $urlPattern = 'products.php?' . ($search ? 'search=' . urlencode($search) . '&' : '') . 'page={page}';
            echo pagination($total, $page, $perPage, $urlPattern);
        endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏍️</div>
            <h3>暂无商品</h3>
            <p><?php echo $search ? '没有匹配搜索结果的商品' : '点击"添加商品"按钮创建您的第一个商品'; ?></p>
            <?php if (!$search): ?>
            <a href="product_edit.php" class="btn btn-primary" style="margin-top:16px;">+ 添加商品</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
