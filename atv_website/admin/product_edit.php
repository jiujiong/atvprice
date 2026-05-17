<?php
$currentNav = 'products';
$pageTitle = '编辑商品';
require_once __DIR__ . '/header.php';

$id = intval($_GET['id'] ?? 0);
$isEdit = $id > 0;
$pageTitle = $isEdit ? '编辑商品' : '添加商品';

// 获取分类
$categories = getCategories();

// 初始化数据
$product = [
    'id' => 0, 'category_id' => '', 'title' => '', 'slug' => '', 'subtitle' => '',
    'summary' => '', 'description' => '', 'specifications' => '', 'features' => '',
    'image' => '', 'gallery' => '', 'price' => '', 'price_display' => '',
    'model_no' => '', 'engine_type' => '', 'displacement' => '', 'power' => '',
    'transmission' => '', 'drive_type' => '', 'brakes' => '', 'tires' => '',
    'dimensions' => '', 'weight' => '', 'colors' => '', 'certifications' => '',
    'meta_title' => '', 'meta_keywords' => '', 'meta_description' => '',
    'sort_order' => 0, 'is_show' => 1, 'is_featured' => 0
];

// 获取现有数据
if ($isEdit) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM " . table('products') . " WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();
        if ($existing) {
            $product = array_merge($product, $existing);
        } else {
            $error = '商品不存在';
            $isEdit = false;
        }
    } catch (Exception $e) {
        $error = '数据加载失败';
    }
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 基础信息
    $data = [
        'category_id' => intval($_POST['category_id'] ?? 0),
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'subtitle' => trim($_POST['subtitle'] ?? ''),
        'summary' => trim($_POST['summary'] ?? ''),
        'description' => $_POST['description'] ?? '',
        'specifications' => $_POST['specifications'] ?? '',
        'features' => $_POST['features'] ?? '',
        'price' => floatval($_POST['price'] ?? 0),
        'price_display' => trim($_POST['price_display'] ?? ''),
        'model_no' => trim($_POST['model_no'] ?? ''),
        'engine_type' => trim($_POST['engine_type'] ?? ''),
        'displacement' => trim($_POST['displacement'] ?? ''),
        'power' => trim($_POST['power'] ?? ''),
        'transmission' => trim($_POST['transmission'] ?? ''),
        'drive_type' => trim($_POST['drive_type'] ?? ''),
        'brakes' => trim($_POST['brakes'] ?? ''),
        'tires' => trim($_POST['tires'] ?? ''),
        'dimensions' => trim($_POST['dimensions'] ?? ''),
        'weight' => trim($_POST['weight'] ?? ''),
        'colors' => trim($_POST['colors'] ?? ''),
        'certifications' => trim($_POST['certifications'] ?? ''),
        'meta_title' => trim($_POST['meta_title'] ?? ''),
        'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
        'meta_description' => trim($_POST['meta_description'] ?? ''),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
        'is_show' => isset($_POST['is_show']) ? 1 : 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
    ];
    
    // 验证
    if (empty($data['title'])) {
        $error = '请输入商品名称';
    } elseif (empty($data['slug'])) {
        $error = '请输入URL别名';
    } else {
        // 确保slug唯一
        $data['slug'] = uniqueSlug(table('products'), slugify($data['slug']), $id);
        
        // 处理主图上传
        $imageUrl = $product['image'];
        if (!empty($_FILES['image']['tmp_name'])) {
            $upload = uploadImage($_FILES['image'], 'products');
            if ($upload['success']) {
                if ($imageUrl) deleteImage($imageUrl, 'products');
                $imageUrl = $upload['path'];
            } else {
                $error = '主图上传失败: ' . $upload['message'];
            }
        }
        $data['image'] = $imageUrl;
        
        // 处理图库
        $gallery = [];
        if (!empty($_POST['existing_gallery'])) {
            $gallery = $_POST['existing_gallery'];
        }
        if (!empty($_FILES['gallery'])) {
            $fileCount = count($_FILES['gallery']['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if (!empty($_FILES['gallery']['tmp_name'][$i])) {
                    $file = [
                        'name' => $_FILES['gallery']['name'][$i],
                        'type' => $_FILES['gallery']['type'][$i],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                        'error' => $_FILES['gallery']['error'][$i],
                        'size' => $_FILES['gallery']['size'][$i]
                    ];
                    $upload = uploadImage($file, 'products');
                    if ($upload['success']) {
                        $gallery[] = $upload['path'];
                    }
                }
            }
        }
        $data['gallery'] = json_encode(array_values($gallery));
        
        if (!isset($error)) {
            try {
                $pdo = getDB();
                
                if ($isEdit) {
                    // 更新
                    $fields = [];
                    $values = [];
                    foreach ($data as $k => $v) {
                        $fields[] = "{$k} = ?";
                        $values[] = $v;
                    }
                    $values[] = $id;
                    
                    $sql = "UPDATE " . table('products') . " SET " . implode(', ', $fields) . " WHERE id = ?";
                    $pdo->prepare($sql)->execute($values);
                    $success = '商品已更新';
                } else {
                    // 插入
                    $fields = array_keys($data);
                    $placeholders = array_fill(0, count($fields), '?');
                    $sql = "INSERT INTO " . table('products') . " (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $pdo->prepare($sql)->execute(array_values($data));
                    $id = $pdo->lastInsertId();
                    $isEdit = true;
                    $success = '商品已创建';
                }
                
                // 刷新数据
                $stmt = $pdo->prepare("SELECT * FROM " . table('products') . " WHERE id = ?");
                $stmt->execute([$id]);
                $product = array_merge($product, $stmt->fetch());
                
            } catch (Exception $e) {
                $error = '保存失败: ' . $e->getMessage();
            }
        }
    }
}

// 处理图库
$gallery = [];
if ($product['gallery']) {
    $gallery = json_decode($product['gallery'], true) ?: [];
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
        <h3><?php echo $isEdit ? '编辑商品' : '添加商品'; ?></h3>
        <div>
            <?php if ($isEdit): ?>
            <a href="../product.php?slug=<?php echo e($product['slug']); ?>" target="_blank" class="btn btn-light btn-sm">预览</a>
            <?php endif; ?>
            <a href="products.php" class="btn btn-light btn-sm">返回列表</a>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="" enctype="multipart/form-data">
            
            <!-- Tabs -->
            <div class="tabs">
                <button type="button" class="tab active" data-target="tab-basic">基本信息</button>
                <button type="button" class="tab" data-target="tab-content">详情内容</button>
                <button type="button" class="tab" data-target="tab-specs">规格参数</button>
                <button type="button" class="tab" data-target="tab-media">图片</button>
                <button type="button" class="tab" data-target="tab-seo">SEO设置</button>
            </div>
            
            <div class="tab-content">
                <!-- Basic Info -->
                <div class="tab-panel active" id="tab-basic">
                    <div class="form-row">
                        <div class="form-group">
                            <label>商品名称 <span class="required">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?php echo e($product['title']); ?>" required placeholder="例如: 200cc ATV Quad Bike">
                        </div>
                        <div class="form-group">
                            <label>URL别名 (Slug) <span class="required">*</span></label>
                            <input type="text" name="slug" class="form-control" value="<?php echo e($product['slug']); ?>" required 
                                   data-slug-source="title" placeholder="200cc-atv-quad-bike">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>副标题</label>
                        <input type="text" name="subtitle" class="form-control" value="<?php echo e($product['subtitle']); ?>" placeholder="简短描述语">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>分类</label>
                            <select name="category_id" class="form-control">
                                <option value="0">未分类</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($cat['name_en']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>型号</label>
                            <input type="text" name="model_no" class="form-control" value="<?php echo e($product['model_no']); ?>" placeholder="ATV-200">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>价格 (数字)</label>
                            <input type="number" name="price" class="form-control" value="<?php echo $product['price'] ?: ''; ?>" step="0.01" placeholder="1999.00">
                        </div>
                        <div class="form-group">
                            <label>价格显示文字</label>
                            <input type="text" name="price_display" class="form-control" value="<?php echo e($product['price_display']); ?>" placeholder="Contact for Price 或 $1,999">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>摘要</label>
                        <textarea name="summary" class="form-control" rows="3" placeholder="简短描述，显示在商品列表页"><?php echo e($product['summary']); ?></textarea>
                    </div>
                    
                    <div class="form-row three">
                        <div class="form-group">
                            <label>排序</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $product['sort_order']; ?>" placeholder="0">
                        </div>
                        <div class="form-group" style="display:flex;align-items:flex-end;gap:24px;padding-bottom:10px;">
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="checkbox" name="is_show" value="1" <?php echo $product['is_show'] ? 'checked' : ''; ?>> 显示
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="checkbox" name="is_featured" value="1" <?php echo $product['is_featured'] ? 'checked' : ''; ?>> 首页精选
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="tab-panel" id="tab-content">
                    <div class="form-group">
                        <label>商品详情 (支持HTML)</label>
                        <div class="editor">
                            <div class="editor-toolbar">
                                <button type="button" onclick="editorCommand('bold')" title="加粗"><b>B</b></button>
                                <button type="button" onclick="editorCommand('italic')" title="斜体"><i>I</i></button>
                                <button type="button" onclick="editorCommand('underline')" title="下划线"><u>U</u></button>
                                <button type="button" onclick="editorCommand('insertUnorderedList')" title="列表">• List</button>
                                <button type="button" onclick="editorCommand('insertOrderedList')" title="编号列表">1. List</button>
                                <button type="button" onclick="editorCommand('formatBlock', 'H2')" title="标题">H2</button>
                                <button type="button" onclick="editorCommand('formatBlock', 'H3')" title="小标题">H3</button>
                                <button type="button" onclick="editorCommand('createLink', prompt('Enter URL:'))" title="链接">🔗 Link</button>
                                <button type="button" onclick="editorCommand('removeFormat')" title="清除格式">Clear</button>
                            </div>
                            <textarea name="description" class="form-control" rows="15" placeholder="输入商品详细介绍..."><?php echo e($product['description']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>产品特点 (支持HTML)</label>
                        <textarea name="features" class="form-control" rows="8" placeholder="输入产品特点，可使用HTML格式..."><?php echo e($product['features']); ?></textarea>
                    </div>
                </div>
                
                <!-- Specifications -->
                <div class="tab-panel" id="tab-specs">
                    <div class="form-row">
                        <div class="form-group">
                            <label>发动机类型</label>
                            <input type="text" name="engine_type" class="form-control" value="<?php echo e($product['engine_type']); ?>" placeholder="4-stroke, Single Cylinder">
                        </div>
                        <div class="form-group">
                            <label>排量</label>
                            <input type="text" name="displacement" class="form-control" value="<?php echo e($product['displacement']); ?>" placeholder="200cc">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>最大功率</label>
                            <input type="text" name="power" class="form-control" value="<?php echo e($product['power']); ?>" placeholder="11kW / 7500rpm">
                        </div>
                        <div class="form-group">
                            <label>变速箱</label>
                            <input type="text" name="transmission" class="form-control" value="<?php echo e($product['transmission']); ?>" placeholder="Automatic CVT">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>驱动方式</label>
                            <input type="text" name="drive_type" class="form-control" value="<?php echo e($product['drive_type']); ?>" placeholder="2WD / 4WD">
                        </div>
                        <div class="form-group">
                            <label>制动系统</label>
                            <input type="text" name="brakes" class="form-control" value="<?php echo e($product['brakes']); ?>" placeholder="Front/Rear Disc">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>轮胎规格</label>
                            <input type="text" name="tires" class="form-control" value="<?php echo e($product['tires']); ?>" placeholder="AT 22x7-10 / 22x10-10">
                        </div>
                        <div class="form-group">
                            <label>整车重量</label>
                            <input type="text" name="weight" class="form-control" value="<?php echo e($product['weight']); ?>" placeholder="280kg">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>外形尺寸</label>
                        <input type="text" name="dimensions" class="form-control" value="<?php echo e($product['dimensions']); ?>" placeholder="1800 x 1050 x 1150 mm">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>可选颜色</label>
                            <input type="text" name="colors" class="form-control" value="<?php echo e($product['colors']); ?>" placeholder="Red, Black, Blue, Green">
                        </div>
                        <div class="form-group">
                            <label>认证</label>
                            <input type="text" name="certifications" class="form-control" value="<?php echo e($product['certifications']); ?>" placeholder="CE, EEC, EPA">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>详细规格表 (支持HTML表格)</label>
                        <textarea name="specifications" class="form-control" rows="10" placeholder="&lt;table&gt;&lt;tr&gt;&lt;th&gt;参数&lt;/th&gt;&lt;th&gt;规格&lt;/th&gt;&lt;/tr&gt;..."><?php echo e($product['specifications']); ?></textarea>
                    </div>
                </div>
                
                <!-- Media -->
                <div class="tab-panel" id="tab-media">
                    <div class="form-group">
                        <label>主图</label>
                        <div style="display:flex;gap:16px;align-items:flex-start;">
                            <div>
                                <input type="file" name="image" class="form-control" accept="image/*" data-preview="mainImagePreview">
                                <div style="font-size:12px;color:var(--admin-text-light);margin-top:6px;">建议尺寸: 800x600px, 最大5MB</div>
                            </div>
                            <?php if ($product['image']): ?>
                            <img id="mainImagePreview" src="<?php echo e($product['image']); ?>" style="max-width:200px;max-height:150px;border-radius:8px;">
                            <?php else: ?>
                            <img id="mainImagePreview" style="max-width:200px;max-height:150px;border-radius:8px;display:none;">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>图片库</label>
                        <!-- Existing Gallery -->
                        <?php if (!empty($gallery)): ?>
                        <div style="margin-bottom:12px;">
                            <p style="font-size:13px;color:var(--admin-text-light);margin-bottom:8px;">已有图片 (取消勾选删除):</p>
                            <div class="gallery-grid">
                                <?php foreach ($gallery as $i => $img): ?>
                                <div class="gallery-item">
                                    <img src="<?php echo e($img); ?>" alt="">
                                    <label style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.7);color:white;font-size:11px;padding:4px;text-align:center;cursor:pointer;">
                                        <input type="checkbox" name="existing_gallery[]" value="<?php echo e($img); ?>" checked style="display:none;"> 保留
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Upload New -->
                        <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
                        <div style="font-size:12px;color:var(--admin-text-light);margin-top:6px;">可选择多张图片上传</div>
                    </div>
                </div>
                
                <!-- SEO -->
                <div class="tab-panel" id="tab-seo">
                    <div class="form-group">
                        <label>SEO标题 (Meta Title)</label>
                        <input type="text" name="meta_title" class="form-control" value="<?php echo e($product['meta_title']); ?>" 
                               placeholder="200cc ATV Quad Bike - Wholesale Off-Road Vehicle" data-seo-preview>
                    </div>
                    <div class="form-group">
                        <label>SEO关键词 (Meta Keywords)</label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?php echo e($product['meta_keywords']); ?>" 
                               placeholder="200cc ATV, quad bike, wholesale ATV, off-road vehicle">
                    </div>
                    <div class="form-group">
                        <label>SEO描述 (Meta Description)</label>
                        <textarea name="meta_description" class="form-control" rows="3" data-seo-preview
                                  placeholder="简短描述，160字符以内"><?php echo e($product['meta_description']); ?></textarea>
                    </div>
                    
                    <!-- Preview -->
                    <div class="seo-preview">
                        <div class="seo-preview-title"><?php echo e($product['meta_title'] ?: $product['title'] ?: 'Page Title'); ?></div>
                        <div class="seo-preview-url"><?php echo SITE_URL; ?>/product.php?slug=<?php echo e($product['slug'] ?: 'example'); ?></div>
                        <div class="seo-preview-desc"><?php echo e($product['meta_description'] ?: $product['summary'] ?: 'Page description will appear here...'); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div style="margin-top:28px;padding-top:20px;border-top:2px solid var(--admin-border);display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary btn-lg">保存商品</button>
                <a href="products.php" class="btn btn-light btn-lg">取消</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
