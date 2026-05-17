<?php
/**
 * ATV Export Website - Configuration File
 * 配置文件 - 安装后自动生成
 */

// 这些常量将在install.php安装过程中自动替换为实际值
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', '');
    define('DB_USER', '');
    define('DB_PASS', '');
    define('DB_PREFIX', 'atv_');
}

// 动态定义
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$scriptName = rtrim($scriptName, '/');
$baseUrl = $protocol . '://' . $host . $scriptName;

if (!defined('SITE_URL')) define('SITE_URL', $baseUrl);
if (!defined('ADMIN_URL')) define('ADMIN_URL', SITE_URL . '/admin');
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', __DIR__ . '/uploads');
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', SITE_URL . '/uploads');
if (!defined('SITE_VERSION')) define('SITE_VERSION', '1.0.0');
if (!defined('ITEMS_PER_PAGE')) define('ITEMS_PER_PAGE', 12);

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 会话启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 表名辅助函数（自动加前缀）- 必须在 getDB 之前定义
if (!function_exists('table')) {
    function table($name) {
        return DB_PREFIX . $name;
    }
}

// 数据库连接函数
if (!function_exists('getDB')) {
    function getDB() {
        static $pdo = null;
        if ($pdo === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                $pdo = new PDO($dsn, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (PDOException $e) {
                die('Database connection failed. Please check your configuration or <a href="install.php">reinstall</a>.');
            }
        }
        return $pdo;
    }
}
