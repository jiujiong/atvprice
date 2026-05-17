<?php
$currentNav = 'seo';
$pageTitle = 'SEO设置';
require_once __DIR__ . '/header.php';

// 保存处理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = getDB();
        foreach ($_POST['seo'] as $pageName => $data) {
            $stmt = $pdo->prepare("INSERT INTO " . table('seo') . " 
                (page_name, page_title, meta_title, meta_keywords, meta_description) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                page_title = VALUES(page_title),
                meta_title = VALUES(meta_title),
                meta_keywords = VALUES(meta_keywords),
                meta_description = VALUES(meta_description)");
            $stmt->execute([
                $pageName,
                trim($data['page_title'] ?? ''),
                trim($data['meta_title'] ?? ''),
                trim($data['meta_keywords'] ?? ''),
                trim($data['meta_description'] ?? '')
            ]);
        }
        $success = 'SEO设置已保存';
    } catch (Exception $e) {
        $error = '保存失败: ' . $e->getMessage();
    }
}

// 获取SEO数据
$seoData = [];
try {
    $pdo = getDB();
    $results = $pdo->query("SELECT * FROM " . table('seo') . " ORDER BY id ASC")->fetchAll();
    foreach ($results as $row) {
        $seoData[$row['page_name']] = $row;
    }
} catch (Exception $e) {}

// 页面定义
$pages = [
    ['name' => 'home', 'label' => '首页', 'desc' => '网站首页'],
    ['name' => 'products', 'label' => '商品列表', 'desc' => '商品列表页'],
    ['name' => 'news', 'label' => '新闻列表', 'desc' => '新闻列表页'],
    ['name' => 'about', 'label' => '关于我们', 'desc' => '关于我们页面'],
    ['name' => 'contact', 'label' => '联系我们', 'desc' => '联系我们页面'],
];
?>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?php echo e($success); ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
<div class="alert alert-error"><?php echo e($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>页面SEO设置</h3>
    </div>
    <div class="card-body">
        <form method="post" action="">
            
            <div class="tabs">
                <?php foreach ($pages as $i => $p): ?>
                <button type="button" class="tab <?php echo $i === 0 ? 'active' : ''; ?>" data-target="tab-seo-<?php echo $p['name']; ?>">
                    <?php echo e($p['label']); ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <div class="tab-content">
                <?php foreach ($pages as $i => $p): 
                    $seo = $seoData[$p['name']] ?? [];
                ?>
                <div class="tab-panel <?php echo $i === 0 ? 'active' : ''; ?>" id="tab-seo-<?php echo $p['name']; ?>">
                    <div style="background:var(--admin-light);padding:16px;border-radius:8px;margin-bottom:20px;">
                        <strong><?php echo e($p['label']); ?></strong> - <?php echo e($p['desc']); ?>
                    </div>
                    
                    <input type="hidden" name="seo[<?php echo $p['name']; ?>][page_title]" value="<?php echo e($p['label']); ?>">
                    
                    <div class="form-group">
                        <label>页面标题 (Meta Title)</label>
                        <input type="text" name="seo[<?php echo $p['name']; ?>][meta_title]" class="form-control" 
                               value="<?php echo e($seo['meta_title'] ?? ''); ?>" placeholder="建议60字符以内">
                    </div>
                    <div class="form-group">
                        <label>关键词 (Meta Keywords)</label>
                        <input type="text" name="seo[<?php echo $p['name']; ?>][meta_keywords]" class="form-control" 
                               value="<?php echo e($seo['meta_keywords'] ?? ''); ?>" placeholder="ATV, UTV, quad bike, wholesale ATV...">
                    </div>
                    <div class="form-group">
                        <label>描述 (Meta Description)</label>
                        <textarea name="seo[<?php echo $p['name']; ?>][meta_description]" class="form-control" rows="3" 
                                  placeholder="建议160字符以内"><?php echo e($seo['meta_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Preview -->
                    <div class="seo-preview">
                        <div class="seo-preview-title"><?php echo e($seo['meta_title'] ?: $p['label'] . ' - ' . getSetting('site_name', 'ATV POWER')); ?></div>
                        <div class="seo-preview-url"><?php echo SITE_URL; ?>/<?php echo $p['name'] == 'home' ? '' : $p['name'] . '.php'; ?></div>
                        <div class="seo-preview-desc"><?php echo e($seo['meta_description'] ?: ''); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="margin-top:28px;padding-top:20px;border-top:2px solid var(--admin-border);">
                <button type="submit" class="btn btn-primary btn-lg">保存SEO设置</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
