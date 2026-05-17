<?php
$currentNav = 'inquiries';
$pageTitle = '询盘管理';
require_once __DIR__ . '/header.php';

// 标记已读
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    try {
        $pdo = getDB();
        $pdo->prepare("UPDATE " . table('inquiries') . " SET is_read = 1 WHERE id = ?")->execute([intval($_GET['read'])]);
    } catch (Exception $e) {}
}

// 删除
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $pdo = getDB();
        $pdo->prepare("DELETE FROM " . table('inquiries') . " WHERE id = ?")->execute([intval($_GET['delete'])]);
        $success = '询盘已删除';
    } catch (Exception $e) {
        $error = '删除失败';
    }
}

// 标记全部已读
if (isset($_GET['read_all'])) {
    try {
        $pdo = getDB();
        $pdo->exec("UPDATE " . table('inquiries') . " SET is_read = 1");
        $success = '全部标记为已读';
    } catch (Exception $e) {}
}

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

try {
    $pdo = getDB();
    $total = $pdo->query("SELECT COUNT(*) FROM " . table('inquiries'))->fetchColumn();
    $offset = ($page - 1) * $perPage;
    $inquiries = $pdo->query("SELECT i.*, p.title as product_title FROM " . table('inquiries') . " i 
                              LEFT JOIN " . table('products') . " p ON i.product_id = p.id 
                              ORDER BY i.created_at DESC LIMIT {$offset}, {$perPage}")->fetchAll();
    $totalPages = ceil($total / $perPage);
} catch (Exception $e) {
    $inquiries = [];
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
        <h3>询盘列表</h3>
        <a href="?read_all=1" class="btn btn-light btn-sm">全部标记已读</a>
    </div>
    <div class="card-body">
        <?php if (!empty($inquiries)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>状态</th>
                        <th>联系人</th>
                        <th>邮箱</th>
                        <th>电话</th>
                        <th>公司</th>
                        <th>相关产品</th>
                        <th>留言</th>
                        <th>时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inq): ?>
                    <tr style="<?php echo !$inq['is_read'] ? 'background:rgba(233,69,96,0.03);' : ''; ?>">
                        <td><?php echo $inq['id']; ?></td>
                        <td>
                            <?php if (!$inq['is_read']): ?>
                            <span class="status inactive"><span class="status-dot"></span> 新</span>
                            <?php else: ?>
                            <span class="status active"><span class="status-dot"></span> 已读</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo e($inq['name']); ?></strong></td>
                        <td><a href="mailto:<?php echo e($inq['email']); ?>" style="color:var(--admin-primary);"><?php echo e($inq['email']); ?></a></td>
                        <td><?php echo e($inq['phone'] ?: '-'); ?></td>
                        <td><?php echo e($inq['company'] ?: '-'); ?></td>
                        <td><?php echo e($inq['product_title'] ?: 'General'); ?></td>
                        <td><?php echo e(truncate($inq['message'], 50)); ?></td>
                        <td><?php echo formatDate($inq['created_at'], 'Y-m-d H:i'); ?></td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <?php if (!$inq['is_read']): ?>
                                <a href="?read=<?php echo $inq['id']; ?>" class="btn btn-sm btn-success">标为已读</a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $inq['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()">删除</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): 
            echo pagination($total, $page, $perPage, 'inquiries.php?page={page}');
        endif; ?>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📨</div>
            <h3>暂无询盘</h3>
            <p>客户询盘将显示在这里</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
