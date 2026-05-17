<?php
/**
 * ATV Export Website - Install Program
 * 安装程序 - 配置数据库、创建表、设置管理员
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// 检查是否已安装
if (file_exists('config.php') && $step == 1) {
    $config_content = file_get_contents('config.php');
    if (strpos($config_content, 'your_db_password') === false && strpos($config_content, 'DB_PASSWORD') !== false) {
        // 可能已经安装
    }
}

// Step 1: 数据库配置
if ($step == 1 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = trim($_POST['db_host'] ?? 'localhost');
    $db_name = trim($_POST['db_name'] ?? '');
    $db_user = trim($_POST['db_user'] ?? '');
    $db_pass = trim($_POST['db_pass'] ?? '');
    $db_prefix = trim($_POST['db_prefix'] ?? 'atv_');
    
    if (empty($db_name) || empty($db_user)) {
        $error = '请填写完整的数据库信息';
    } else {
        // 测试连接
        try {
            $pdo = new PDO("mysql:host={$db_host};charset=utf8mb4", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 创建数据库
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$db_name}`");
            
            // 保存配置到session
            $_SESSION['install'] = [
                'db_host' => $db_host,
                'db_name' => $db_name,
                'db_user' => $db_user,
                'db_pass' => $db_pass,
                'db_prefix' => $db_prefix
            ];
            
            header('Location: install.php?step=2');
            exit;
        } catch (PDOException $e) {
            $error = '数据库连接失败: ' . $e->getMessage();
        }
    }
}

// Step 2: 创建数据表
if ($step == 2) {
    if (!isset($_SESSION['install'])) {
        header('Location: install.php?step=1');
        exit;
    }
    
    $cfg = $_SESSION['install'];
    
    try {
        $pdo = new PDO("mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset=utf8mb4", $cfg['db_user'], $cfg['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $prefix = $cfg['db_prefix'];
        
        // 创建管理员表
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}admins` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `email` VARCHAR(100),
            `last_login` DATETIME,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建商品分类表
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}categories` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `name_en` VARCHAR(100) NOT NULL,
            `slug` VARCHAR(100) NOT NULL UNIQUE,
            `description` TEXT,
            `sort_order` INT(11) DEFAULT 0,
            `is_show` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建商品表
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}products` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT(11) UNSIGNED DEFAULT 0,
            `title` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(255) NOT NULL UNIQUE,
            `subtitle` VARCHAR(255),
            `summary` TEXT,
            `description` LONGTEXT,
            `specifications` LONGTEXT,
            `features` LONGTEXT,
            `image` VARCHAR(255),
            `gallery` TEXT,
            `price` DECIMAL(10,2) DEFAULT 0,
            `price_display` VARCHAR(50),
            `model_no` VARCHAR(100),
            `engine_type` VARCHAR(100),
            `displacement` VARCHAR(50),
            `power` VARCHAR(50),
            `transmission` VARCHAR(100),
            `drive_type` VARCHAR(50),
            `brakes` VARCHAR(100),
            `tires` VARCHAR(100),
            `dimensions` VARCHAR(200),
            `weight` VARCHAR(50),
            `colors` VARCHAR(200),
            `certifications` VARCHAR(200),
            `meta_title` VARCHAR(255),
            `meta_keywords` VARCHAR(500),
            `meta_description` VARCHAR(500),
            `sort_order` INT(11) DEFAULT 0,
            `is_show` TINYINT(1) DEFAULT 1,
            `is_featured` TINYINT(1) DEFAULT 0,
            `views` INT(11) DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建新闻表
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}news` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(255) NOT NULL UNIQUE,
            `summary` TEXT,
            `content` LONGTEXT,
            `image` VARCHAR(255),
            `author` VARCHAR(100),
            `meta_title` VARCHAR(255),
            `meta_keywords` VARCHAR(500),
            `meta_description` VARCHAR(500),
            `sort_order` INT(11) DEFAULT 0,
            `is_show` TINYINT(1) DEFAULT 1,
            `views` INT(11) DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建询盘表
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}inquiries` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT(11) UNSIGNED DEFAULT 0,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `phone` VARCHAR(50),
            `company` VARCHAR(200),
            `country` VARCHAR(100),
            `message` TEXT NOT NULL,
            `is_read` TINYINT(1) DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建SEO设置表
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}seo` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `page_name` VARCHAR(50) NOT NULL UNIQUE,
            `page_title` VARCHAR(255),
            `meta_title` VARCHAR(255),
            `meta_keywords` VARCHAR(500),
            `meta_description` VARCHAR(500),
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 创建网站设置表
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}settings` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // 插入默认分类
        $pdo->exec("INSERT INTO `{$prefix}categories` (`name`, `name_en`, `slug`, `description`) VALUES
            ('全地形车', 'ATV', 'atv', 'All Terrain Vehicles'),
            ('沙滩车', 'UTV', 'utv', 'Utility Task Vehicles'),
            ('卡丁车', 'Go Kart', 'go-kart', 'Racing Go Karts'),
            ('越野车', 'Dirt Bike', 'dirt-bike', 'Off-road Motorcycles'),
            ('配件', 'Parts & Accessories', 'parts', 'ATV/UTV Parts and Accessories')");
        
        // 插入默认SEO设置
        $pdo->exec("INSERT INTO `{$prefix}seo` (`page_name`, `page_title`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
            ('home', 'Home', 'ATV Manufacturer - Wholesale All Terrain Vehicles & UTV', 'ATV, UTV, all terrain vehicle, wholesale ATV, ATV manufacturer, quad bike, go kart, dirt bike, off road vehicle', 'Professional ATV manufacturer offering wholesale all terrain vehicles, UTV, go karts, dirt bikes. High quality off-road vehicles with CE certification.'),
            ('products', 'Products', 'ATV Products - Wholesale All Terrain Vehicles & UTV | ATV Manufacturer', 'ATV products, UTV catalog, go kart, dirt bike, quad bike, ATV wholesale, off-road vehicles', 'Browse our complete range of ATVs, UTVs, go karts and dirt bikes. Quality off-road vehicles at competitive wholesale prices.'),
            ('news', 'News', 'ATV Industry News - Latest Updates | ATV Manufacturer', 'ATV news, UTV industry, off-road vehicle updates, ATV technology, powersports news', 'Stay updated with the latest ATV industry news, product launches, and off-road vehicle technology updates.'),
            ('about', 'About Us', 'About Us - Professional ATV Manufacturer & Exporter', 'ATV manufacturer, ATV factory, UTV supplier, go kart exporter, powersports company', 'Leading ATV manufacturer with years of experience in producing quality all terrain vehicles, UTVs and go karts for global markets.'),
            ('contact', 'Contact Us', 'Contact Us - ATV Wholesale Inquiry | ATV Manufacturer', 'contact ATV manufacturer, ATV wholesale inquiry, UTV supplier contact, buy ATV', 'Contact us for ATV wholesale inquiries. Professional all terrain vehicle manufacturer ready to serve your business needs.')");
        
        // 插入默认网站设置
        $pdo->exec("INSERT INTO `{$prefix}settings` (`setting_key`, `setting_value`) VALUES
            ('site_name', 'ATV POWER'),
            ('site_slogan', 'Professional Off-Road Vehicle Manufacturer'),
            ('site_logo', ''),
            ('company_name', 'ATV Power Industries Co., Ltd.'),
            ('company_address', 'No.168 Industrial Zone, Yongkang City, Zhejiang Province, China'),
            ('company_phone', '+86-579-12345678'),
            ('company_email', 'sales@atvpower.com'),
            ('company_whatsapp', '+8613800138000'),
            ('company_wechat', 'ATVPower'),
            ('footer_about', 'Leading manufacturer of ATVs, UTVs, Go Karts and Dirt Bikes. We provide high quality off-road vehicles with CE certification.'),
            ('footer_copyright', '© 2026 ATV Power. All Rights Reserved.'),
            ('analytics_code', '')");
        
        $success = '数据表创建成功！';
        header('Location: install.php?step=3');
        exit;
        
    } catch (PDOException $e) {
        $error = '创建表失败: ' . $e->getMessage();
    }
}

// Step 3: 设置管理员
if ($step == 3 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['install'])) {
        header('Location: install.php?step=1');
        exit;
    }
    
    $admin_user = trim($_POST['admin_user'] ?? '');
    $admin_pass = trim($_POST['admin_pass'] ?? '');
    $admin_pass2 = trim($_POST['admin_pass2'] ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');
    
    if (empty($admin_user) || empty($admin_pass)) {
        $error = '请填写管理员账号和密码';
    } elseif ($admin_pass != $admin_pass2) {
        $error = '两次密码不一致';
    } elseif (strlen($admin_pass) < 6) {
        $error = '密码至少6位';
    } else {
        $cfg = $_SESSION['install'];
        
        try {
            $pdo = new PDO("mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset=utf8mb4", $cfg['db_user'], $cfg['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $prefix = $cfg['db_prefix'];
            $hash = password_hash($admin_pass, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}admins` (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$admin_user, $hash, $admin_email]);
            
            // 写入配置文件
            $config_content = "<?php\n";
            $config_content .= "/**\n";
            $config_content .= " * ATV Export Website - Configuration File\n";
            $config_content .= " * 配置文件 - 由install.php自动生成\n";
            $config_content .= " */\n\n";
            $config_content .= "define('DB_HOST', '{$cfg['db_host']}');\n";
            $config_content .= "define('DB_NAME', '{$cfg['db_name']}');\n";
            $config_content .= "define('DB_USER', '{$cfg['db_user']}');\n";
            $config_content .= "define('DB_PASS', '{$cfg['db_pass']}');\n";
            $config_content .= "define('DB_PREFIX', '{$cfg['db_prefix']}');\n\n";
            $config_content .= "\$protocol = isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https' : 'http';\n";
            $config_content .= "\$host = \$_SERVER['HTTP_HOST'] ?? 'localhost';\n";
            $config_content .= "\$scriptName = dirname(\$_SERVER['SCRIPT_NAME'] ?? '/');\n";
            $config_content .= "\$scriptName = rtrim(\$scriptName, '/');\n";
            $config_content .= "\$baseUrl = \$protocol . '://' . \$host . \$scriptName;\n\n";
            $config_content .= "if (!defined('SITE_URL')) define('SITE_URL', \$baseUrl);\n";
            $config_content .= "define('ADMIN_URL', SITE_URL . '/admin');\n";
            $config_content .= "define('UPLOAD_PATH', __DIR__ . '/uploads');\n";
            $config_content .= "define('UPLOAD_URL', SITE_URL . '/uploads');\n";
            $config_content .= "define('SITE_VERSION', '1.0.0');\n";
            $config_content .= "define('ITEMS_PER_PAGE', 12);\n\n";
            $config_content .= "date_default_timezone_set('Asia/Shanghai');\n\n";
            $config_content .= "if (session_status() === PHP_SESSION_NONE) {\n";
            $config_content .= "    session_start();\n";
            $config_content .= "}\n\n";
            $config_content .= "// 表名辅助函数（自动加前缀）\n";
            $config_content .= "if (!function_exists('table')) {\n";
            $config_content .= "    function table(\$name) {\n";
            $config_content .= "        return DB_PREFIX . \$name;\n";
            $config_content .= "    }\n";
            $config_content .= "}\n\n";
            $config_content .= "// 数据库连接函数\n";
            $config_content .= "if (!function_exists('getDB')) {\n";
            $config_content .= "    function getDB() {\n";
            $config_content .= "        static \$pdo = null;\n";
            $config_content .= "        if (\$pdo === null) {\n";
            $config_content .= "            try {\n";
            $config_content .= "                \$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';\n";
            $config_content .= "                \$pdo = new PDO(\$dsn, DB_USER, DB_PASS);\n";
            $config_content .= "                \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
            $config_content .= "                \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);\n";
            $config_content .= "                \$pdo->exec(\"SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci\");\n";
            $config_content .= "            } catch (PDOException \$e) {\n";
            $config_content .= "                die('Database connection failed. Please check your configuration or <a href=\"install.php\">reinstall</a>.');\n";
            $config_content .= "            }\n";
            $config_content .= "        }\n";
            $config_content .= "        return \$pdo;\n";
            $config_content .= "    }\n";
            $config_content .= "}\n";
            
            file_put_contents('config.php', $config_content);
            
            $success = '安装完成！';
            $_SESSION['install_done'] = true;
            
        } catch (PDOException $e) {
            $error = '设置管理员失败: ' . $e->getMessage();
        }
    }
}

// Step 4: 安装完成
if ($step == 4) {
    if (!isset($_SESSION['install_done'])) {
        header('Location: install.php?step=1');
        exit;
    }
    // 清除安装session
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATV外贸网站系统 - 安装</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .install-box {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 550px;
            padding: 40px;
        }
        .install-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .install-header h1 {
            color: #1a1a2e;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .install-header p {
            color: #666;
            font-size: 14px;
        }
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #e94560, #c23a51);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 36px;
            color: #fff;
            font-weight: bold;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 0;
        }
        .step-item {
            display: flex;
            align-items: center;
        }
        .step-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        .step-dot.active {
            background: linear-gradient(135deg, #e94560, #c23a51);
            color: #fff;
        }
        .step-dot.done {
            background: #28a745;
            color: #fff;
        }
        .step-line {
            width: 50px;
            height: 3px;
            background: #e0e0e0;
            margin: 0 5px;
        }
        .step-line.done {
            background: #28a745;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #e94560;
        }
        .form-group .hint {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #e94560, #c23a51);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(233, 69, 96, 0.3);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .success-box {
            text-align: center;
            padding: 20px 0;
        }
        .success-box .checkmark {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .success-box .checkmark::after {
            content: '✓';
            color: #fff;
            font-size: 40px;
        }
        .success-box h2 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .success-box p {
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-group .btn {
            flex: 1;
        }
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="install-box">
        <div class="install-header">
            <div class="logo-icon">A</div>
            <h1>ATV外贸网站系统</h1>
            <p>专业全地形车外销企业网站管理系统</p>
        </div>
        
        <div class="step-indicator">
            <div class="step-item">
                <div class="step-dot <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'done' : ''; ?>">1</div>
            </div>
            <div class="step-line <?php echo $step > 1 ? 'done' : ''; ?>"></div>
            <div class="step-item">
                <div class="step-dot <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'done' : ''; ?>">2</div>
            </div>
            <div class="step-line <?php echo $step > 2 ? 'done' : ''; ?>"></div>
            <div class="step-item">
                <div class="step-dot <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'done' : ''; ?>">3</div>
            </div>
            <div class="step-line <?php echo $step > 3 ? 'done' : ''; ?>"></div>
            <div class="step-item">
                <div class="step-dot <?php echo $step >= 4 ? 'active' : ''; ?>">4</div>
            </div>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
        <!-- Step 1: 数据库配置 -->
        <form method="post" action="">
            <div class="form-group">
                <label>数据库主机</label>
                <input type="text" name="db_host" value="localhost" required>
                <div class="hint">通常为 localhost</div>
            </div>
            <div class="form-group">
                <label>数据库名称</label>
                <input type="text" name="db_name" placeholder="atv_website" required>
            </div>
            <div class="form-group">
                <label>数据库用户名</label>
                <input type="text" name="db_user" placeholder="root" required>
            </div>
            <div class="form-group">
                <label>数据库密码</label>
                <input type="password" name="db_pass" placeholder="">
            </div>
            <div class="form-group">
                <label>表前缀</label>
                <input type="text" name="db_prefix" value="atv_">
                <div class="hint">建议使用默认前缀 atv_</div>
            </div>
            <button type="submit" class="btn btn-primary">下一步：创建数据表</button>
        </form>
        
        <?php elseif ($step == 2): ?>
        <!-- Step 2: 创建数据表 -->
        <div class="success-box">
            <p style="color: #666; margin-bottom: 20px;">正在创建数据库表结构...</p>
            <div style="background: #f0f0f0; border-radius: 8px; padding: 20px; text-align: left; margin-bottom: 20px;">
                <p style="font-size: 13px; color: #666; line-height: 2;">
                    ✓ 管理员表 (admins)<br>
                    ✓ 商品分类表 (categories)<br>
                    ✓ 商品表 (products)<br>
                    ✓ 新闻表 (news)<br>
                    ✓ 询盘表 (inquiries)<br>
                    ✓ SEO设置表 (seo)<br>
                    ✓ 网站设置表 (settings)
                </p>
            </div>
            <a href="install.php?step=3" class="btn btn-primary">下一步：设置管理员</a>
        </div>
        
        <?php elseif ($step == 3): ?>
        <!-- Step 3: 设置管理员 -->
        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (!isset($_SESSION['install_done'])): ?>
        <form method="post" action="">
            <div class="form-group">
                <label>管理员账号</label>
                <input type="text" name="admin_user" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label>管理员密码</label>
                <input type="password" name="admin_pass" placeholder="至少6位" required>
            </div>
            <div class="form-group">
                <label>确认密码</label>
                <input type="password" name="admin_pass2" placeholder="再次输入密码" required>
            </div>
            <div class="form-group">
                <label>管理员邮箱</label>
                <input type="email" name="admin_email" placeholder="admin@example.com">
            </div>
            <button type="submit" class="btn btn-primary">完成安装</button>
        </form>
        <?php else: ?>
        <script>window.location.href='install.php?step=4';</script>
        <?php endif; ?>
        
        <?php elseif ($step == 4): ?>
        <!-- Step 4: 安装完成 -->
        <div class="success-box">
            <div class="checkmark"></div>
            <h2>安装成功！</h2>
            <p>您的ATV外贸网站系统已安装完成。</p>
            <p style="font-size: 13px; margin-top: 15px;">
                后台地址：<strong><?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/admin/'; ?></strong><br>
                使用刚才设置的管理员账号登录后台。
            </p>
            <div class="btn-group">
                <a href="admin/" class="btn btn-primary">进入后台</a>
                <a href="index.php" class="btn btn-secondary">查看网站</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
