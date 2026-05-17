<?php
$currentNav = 'settings';
$pageTitle = '网站设置';
require_once __DIR__ . '/header.php';

// 保存处理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = getDB();
        $settings = [
            'site_name', 'site_slogan', 'company_name', 'company_address',
            'company_phone', 'company_email', 'company_whatsapp', 'company_wechat',
            'footer_about', 'footer_copyright', 'analytics_code'
        ];
        
        foreach ($settings as $key) {
            $value = $_POST[$key] ?? '';
            $stmt = $pdo->prepare("INSERT INTO " . table('settings') . " (setting_key, setting_value) VALUES (?, ?) 
                                   ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$key, $value]);
        }
        $success = '网站设置已保存';
    } catch (Exception $e) {
        $error = '保存失败: ' . $e->getMessage();
    }
}

// 获取当前设置
$settings = [];
try {
    $pdo = getDB();
    $results = $pdo->query("SELECT * FROM " . table('settings'))->fetchAll();
    foreach ($results as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {}

function s($key, $default = '') {
    global $settings;
    return $settings[$key] ?? $default;
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
        <h3>网站基本设置</h3>
    </div>
    <div class="card-body">
        <form method="post" action="">
            
            <div class="tabs">
                <button type="button" class="tab active" data-target="tab-site">网站信息</button>
                <button type="button" class="tab" data-target="tab-company">公司信息</button>
                <button type="button" class="tab" data-target="tab-footer">页脚设置</button>
                <button type="button" class="tab" data-target="tab-advanced">高级</button>
            </div>
            
            <div class="tab-content">
                <!-- Site Info -->
                <div class="tab-panel active" id="tab-site">
                    <div class="form-group">
                        <label>网站名称</label>
                        <input type="text" name="site_name" class="form-control" value="<?php echo e(s('site_name', 'ATV POWER')); ?>">
                    </div>
                    <div class="form-group">
                        <label>网站标语</label>
                        <input type="text" name="site_slogan" class="form-control" value="<?php echo e(s('site_slogan', 'Professional Off-Road Vehicle Manufacturer')); ?>">
                    </div>
                </div>
                
                <!-- Company Info -->
                <div class="tab-panel" id="tab-company">
                    <div class="form-group">
                        <label>公司名称</label>
                        <input type="text" name="company_name" class="form-control" value="<?php echo e(s('company_name', 'ATV Power Industries Co., Ltd.')); ?>">
                    </div>
                    <div class="form-group">
                        <label>公司地址</label>
                        <textarea name="company_address" class="form-control" rows="2"><?php echo e(s('company_address', 'No.168 Industrial Zone, Yongkang City, Zhejiang Province, China')); ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>电话</label>
                            <input type="text" name="company_phone" class="form-control" value="<?php echo e(s('company_phone', '+86-579-12345678')); ?>">
                        </div>
                        <div class="form-group">
                            <label>邮箱</label>
                            <input type="email" name="company_email" class="form-control" value="<?php echo e(s('company_email', 'sales@atvpower.com')); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>WhatsApp</label>
                            <input type="text" name="company_whatsapp" class="form-control" value="<?php echo e(s('company_whatsapp', '+8613800138000')); ?>">
                        </div>
                        <div class="form-group">
                            <label>微信号</label>
                            <input type="text" name="company_wechat" class="form-control" value="<?php echo e(s('company_wechat', 'ATVPower')); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="tab-panel" id="tab-footer">
                    <div class="form-group">
                        <label>页脚关于我们</label>
                        <textarea name="footer_about" class="form-control" rows="3"><?php echo e(s('footer_about', 'Leading manufacturer of ATVs, UTVs, Go Karts and Dirt Bikes. We provide high quality off-road vehicles with CE certification.')); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>版权信息</label>
                        <input type="text" name="footer_copyright" class="form-control" value="<?php echo e(s('footer_copyright', '© 2026 ATV Power. All Rights Reserved.')); ?>">
                    </div>
                </div>
                
                <!-- Advanced -->
                <div class="tab-panel" id="tab-advanced">
                    <div class="form-group">
                        <label>统计代码 (Analytics Code)</label>
                        <textarea name="analytics_code" class="form-control" rows="5" placeholder="Google Analytics 或其他统计代码"><?php echo e(s('analytics_code', '')); ?></textarea>
                        <div style="font-size:12px;color:var(--admin-text-light);margin-top:6px;">支持 Google Analytics、百度统计等代码</div>
                    </div>
                </div>
            </div>
            
            <div style="margin-top:28px;padding-top:20px;border-top:2px solid var(--admin-border);">
                <button type="submit" class="btn btn-primary btn-lg">保存设置</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
