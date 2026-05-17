<?php
$currentNav = 'news';
require_once __DIR__ . '/header.php';

$id = intval($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? '编辑新闻' : '添加新闻';

// 初始化
$article = [
    'id' => 0, 'title' => '', 'slug' => '', 'summary' => '', 'content' => '',
    'image' => '', 'author' => '', 'meta_title' => '', 'meta_keywords' => '',
    'meta_description' => '', 'sort_order' => 0, 'is_show' => 1
];

if ($isEdit) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM " . table('news') . " WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();
        if ($existing) {
            $article = array_merge($article, $existing);
        } else {
            $error = '新闻不存在';
            $isEdit = false;
        }
    } catch (Exception $e) {
        $error = '数据加载失败';
    }
}

// 处理提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'summary' => trim($_POST['summary'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'author' => trim($_POST['author'] ?? ''),
        'meta_title' => trim($_POST['meta_title'] ?? ''),
        'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
        'is_show' => isset($_POST['is_show']) ? 1 : 0,
    ];
    
    if (empty($data['title'])) {
        $error = '请输入标题';
    } elseif (empty($data['slug'])) {
        $error = '请输入URL别名';
    } else {
        $data['slug'] = uniqueSlug(table('news'), slugify($data['slug']), $id);
        
        // 图片处理
        $imageUrl = $article['image'];
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = uploadImage($_FILES['image'], 'news');
            if ($upload['success']) {
                if ($imageUrl) deleteImage($imageUrl, 'news');
                $imageUrl = $upload['path'];
            } else {
                $error = '图片上传失败: ' . $upload['message'];
            }
        }
        $data['image'] = $imageUrl;
        
        if (!isset($error)) {
            try {
                $pdo = getDB();
                if ($isEdit) {
                    $fields = [];
                    $values = [];
                    foreach ($data as $k => $v) {
                        $fields[] = "{$k} = ?";
                        $values[] = $v;
                    }
                    $values[] = $id;
                    $pdo->prepare("UPDATE " . table('news') . " SET " . implode(', ', $fields) . " WHERE id = ?")->execute($values);
                    $success = '新闻已更新';
                } else {
                    $fields = array_keys($data);
                    $placeholders = array_fill(0, count($fields), '?');
                    $pdo->prepare("INSERT INTO " . table('news') . " (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")")->execute(array_values($data));
                    $id = $pdo->lastInsertId();
                    $isEdit = true;
                    $success = '新闻已创建';
                }
                
                $stmt = $pdo->prepare("SELECT * FROM " . table('news') . " WHERE id = ?");
                $stmt->execute([$id]);
                $article = array_merge($article, $stmt->fetch());
            } catch (Exception $e) {
                $error = '保存失败: ' . $e->getMessage();
            }
        }
    }
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
        <h3><?php echo $isEdit ? '编辑新闻' : '添加新闻'; ?></h3>
        <a href="news.php" class="btn btn-light btn-sm">返回列表</a>
    </div>
    <div class="card-body">
        <form method="post" action="" enctype="multipart/form-data">
            
            <div class="tabs">
                <button type="button" class="tab active" data-target="tab-basic">基本信息</button>
                <button type="button" class="tab" data-target="tab-content">正文内容</button>
                <button type="button" class="tab" data-target="tab-seo">SEO设置</button>
            </div>
            
            <div class="tab-content">
                <!-- Basic -->
                <div class="tab-panel active" id="tab-basic">
                    <div class="form-row">
                        <div class="form-group">
                            <label>标题 <span class="required">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?php echo e($article['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>URL别名 (Slug) <span class="required">*</span></label>
                            <input type="text" name="slug" class="form-control" value="<?php echo e($article['slug']); ?>" required data-slug-source="title">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>摘要</label>
                        <textarea name="summary" class="form-control" rows="3"><?php echo e($article['summary']); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>作者</label>
                            <input type="text" name="author" class="form-control" value="<?php echo e($article['author']); ?>">
                        </div>
                        <div class="form-group">
                            <label>排序</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $article['sort_order']; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                            <input type="checkbox" name="is_show" value="1" <?php echo $article['is_show'] ? 'checked' : ''; ?>> 显示发布
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label>封面图</label>
                        <div style="display:flex;gap:16px;align-items:flex-start;">
                            <div>
                                <input type="file" name="image" class="form-control" accept="image/*" data-preview="newsImagePreview">
                                <div style="font-size:12px;color:var(--admin-text-light);margin-top:6px;">建议尺寸: 1200x630px</div>
                            </div>
                            <?php if ($article['image']): ?>
                            <img id="newsImagePreview" src="<?php echo e($article['image']); ?>" style="max-width:200px;max-height:130px;border-radius:8px;">
                            <?php else: ?>
                            <img id="newsImagePreview" style="max-width:200px;max-height:130px;border-radius:8px;display:none;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="tab-panel" id="tab-content">
                    <div class="form-group">
                        <label>正文内容 (支持HTML)</label>
                        <div class="editor">
                            <div class="editor-toolbar">
                                <button type="button" onclick="editorCommand('bold')" title="加粗"><b>B</b></button>
                                <button type="button" onclick="editorCommand('italic')" title="斜体"><i>I</i></button>
                                <button type="button" onclick="editorCommand('underline')" title="下划线"><u>U</u></button>
                                <button type="button" onclick="editorCommand('insertUnorderedList')" title="列表">• List</button>
                                <button type="button" onclick="editorCommand('insertOrderedList')" title="编号">1. List</button>
                                <button type="button" onclick="editorCommand('formatBlock', 'H2')" title="H2">H2</button>
                                <button type="button" onclick="editorCommand('formatBlock', 'H3')" title="H3">H3</button>
                                <button type="button" onclick="editorCommand('createLink', prompt('Enter URL:'))" title="链接">🔗</button>
                                <button type="button" onclick="editorCommand('removeFormat')" title="清除">Clear</button>
                            </div>
                            <textarea name="content" class="form-control" rows="20" placeholder="输入新闻正文内容..."><?php echo e($article['content']); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- SEO -->
                <div class="tab-panel" id="tab-seo">
                    <div class="form-group">
                        <label>SEO标题</label>
                        <input type="text" name="meta_title" class="form-control" value="<?php echo e($article['meta_title']); ?>" data-seo-preview>
                    </div>
                    <div class="form-group">
                        <label>SEO关键词</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?php echo e($article['meta_keywords']); ?>">
                    </div>
                    <div class="form-group">
                        <label>SEO描述</label>
                        <textarea name="meta_description" class="form-control" rows="3" data-seo-preview><?php echo e($article['meta_description']); ?></textarea>
                    </div>
                    
                    <div class="seo-preview">
                        <div class="seo-preview-title"><?php echo e($article['meta_title'] ?: $article['title'] ?: 'Page Title'); ?></div>
                        <div class="seo-preview-url"><?php echo SITE_URL; ?>/news_detail.php?slug=<?php echo e($article['slug'] ?: 'example'); ?></div>
                        <div class="seo-preview-desc"><?php echo e($article['meta_description'] ?: $article['summary'] ?: 'Description...'); ?></div>
                    </div>
                </div>
            </div>
            
            <div style="margin-top:28px;padding-top:20px;border-top:2px solid var(--admin-border);display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary btn-lg">保存新闻</button>
                <a href="news.php" class="btn btn-light btn-lg">取消</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
