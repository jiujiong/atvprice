<?php
/**
 * ATV Export Website - Common Functions
 * 公共函数库
 */

require_once __DIR__ . '/../config.php';

/**
 * 安全输出HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * 生成URL友好的slug
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'item';
}

/**
 * 确保slug唯一
 */
function uniqueSlug($table, $slug, $excludeId = null) {
    $pdo = getDB();
    $originalSlug = $slug;
    $counter = 1;
    
    do {
        $sql = "SELECT id FROM {$table} WHERE slug = ?";
        if ($excludeId) {
            $sql .= " AND id != ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$slug]);
        }
        
        if (!$stmt->fetch()) {
            break;
        }
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    } while (true);
    
    return $slug;
}

/**
 * 获取设置项
 */
function getSetting($key, $default = '') {
    static $settings = null;
    
    if ($settings === null) {
        $settings = [];
        try {
            $pdo = getDB();
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM " . table('settings'));
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            // 表可能不存在
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * 获取SEO设置
 */
function getSEO($pageName) {
    static $seo = null;
    
    if ($seo === null) {
        $seo = [];
        try {
            $pdo = getDB();
            $stmt = $pdo->query("SELECT * FROM " . table('seo'));
            while ($row = $stmt->fetch()) {
                $seo[$row['page_name']] = $row;
            }
        } catch (Exception $e) {
            // 表可能不存在
        }
    }
    
    return $seo[$pageName] ?? null;
}

/**
 * 获取页面标题
 */
function pageTitle($pageName, $customTitle = '') {
    $seo = getSEO($pageName);
    $siteName = getSetting('site_name', 'ATV POWER');
    
    if ($customTitle) {
        return e($customTitle) . ' | ' . e($siteName);
    }
    
    if ($seo && $seo['meta_title']) {
        return e($seo['meta_title']);
    }
    
    return e($siteName);
}

/**
 * 获取Meta Keywords
 */
function metaKeywords($pageName, $custom = '') {
    if ($custom) return e($custom);
    $seo = getSEO($pageName);
    return e($seo['meta_keywords'] ?? 'ATV, UTV, all terrain vehicle, wholesale ATV, ATV manufacturer, quad bike, go kart');
}

/**
 * 获取Meta Description
 */
function metaDescription($pageName, $custom = '') {
    if ($custom) return e($custom);
    $seo = getSEO($pageName);
    return e($seo['meta_description'] ?? 'Professional ATV manufacturer offering wholesale all terrain vehicles, UTV, go karts. High quality off-road vehicles.');
}

/**
 * 分页函数
 */
function pagination($total, $page, $perPage, $urlPattern) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    
    $page = max(1, min($page, $totalPages));
    $html = '<div class="pagination">';
    
    // 上一页
    if ($page > 1) {
        $html .= '<a href="' . str_replace('{page}', $page - 1, $urlPattern) . '" class="page-link prev">&laquo; Prev</a>';
    }
    
    // 页码
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    
    if ($start > 1) {
        $html .= '<a href="' . str_replace('{page}', 1, $urlPattern) . '" class="page-link">1</a>';
        if ($start > 2) $html .= '<span class="page-dots">...</span>';
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            $html .= '<span class="page-link current">' . $i . '</span>';
        } else {
            $html .= '<a href="' . str_replace('{page}', $i, $urlPattern) . '" class="page-link">' . $i . '</a>';
        }
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<span class="page-dots">...</span>';
        $html .= '<a href="' . str_replace('{page}', $totalPages, $urlPattern) . '" class="page-link">' . $totalPages . '</a>';
    }
    
    // 下一页
    if ($page < $totalPages) {
        $html .= '<a href="' . str_replace('{page}', $page + 1, $urlPattern) . '" class="page-link next">Next &raquo;</a>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * 上传图片
 */
function uploadImage($file, $directory = 'products') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => '没有上传文件'];
    }
    
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        return ['success' => false, 'message' => '只允许上传 JPG, PNG, GIF, WebP 图片'];
    }
    
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => '图片大小不能超过5MB'];
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = date('Ymd') . '_' . uniqid() . '.' . strtolower($ext);
    $uploadPath = UPLOAD_PATH . '/' . $directory . '/' . $filename;
    
    if (!is_dir(UPLOAD_PATH . '/' . $directory)) {
        mkdir(UPLOAD_PATH . '/' . $directory, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $url = UPLOAD_URL . '/' . $directory . '/' . $filename;
        return ['success' => true, 'path' => $url, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => '文件上传失败'];
}

/**
 * 删除图片
 */
function deleteImage($url, $directory = 'products') {
    $filename = basename($url);
    $path = UPLOAD_PATH . '/' . $directory . '/' . $filename;
    if (file_exists($path)) {
        unlink($path);
    }
}

/**
 * 检查后台登录
 */
function checkAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

/**
 * 获取当前管理员
 */
function getCurrentAdmin() {
    if (isset($_SESSION['admin_id'])) {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, username, email, last_login FROM " . table('admins') . " WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    }
    return null;
}

/**
 * 显示后台消息
 */
function showMessage($type, $message) {
    $class = $type === 'success' ? 'alert-success' : ($type === 'error' ? 'alert-error' : 'alert-info');
    return '<div class="alert ' . $class . '">' . e($message) . '</div>';
}

/**
 * 截取文本
 */
function truncate($text, $length = 150) {
    $text = strip_tags($text ?? '');
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * 格式化日期
 */
function formatDate($date, $format = 'Y-m-d') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

/**
 * 获取分类列表
 */
function getCategories($isShow = true) {
    try {
        $pdo = getDB();
        $sql = "SELECT * FROM " . table('categories');
        if ($isShow) {
            $sql .= " WHERE is_show = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        return $pdo->query($sql)->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * 获取商品列表
 */
function getProducts($categoryId = 0, $featured = false, $limit = 0, $page = 1, $perPage = 12) {
    try {
        $pdo = getDB();
        $where = ["is_show = 1"];
        $params = [];
        
        if ($categoryId) {
            $where[] = "category_id = ?";
            $params[] = $categoryId;
        }
        if ($featured) {
            $where[] = "is_featured = 1";
        }
        
        $sql = "SELECT p.*, c.name_en as category_name FROM " . table('products') . " p 
                LEFT JOIN " . table('categories') . " c ON p.category_id = c.id 
                WHERE " . implode(' AND ', $where) . " ORDER BY sort_order ASC, id DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        } elseif ($page > 0 && $perPage > 0) {
            $offset = ($page - 1) * $perPage;
            $sql .= " LIMIT {$offset}, " . (int)$perPage;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * 获取商品总数
 */
function getProductCount($categoryId = 0) {
    try {
        $pdo = getDB();
        $sql = "SELECT COUNT(*) FROM " . table('products') . " WHERE is_show = 1";
        $params = [];
        if ($categoryId) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * 获取单个商品
 */
function getProduct($slug) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT p.*, c.name_en as category_name FROM " . table('products') . " p 
                               LEFT JOIN " . table('categories') . " c ON p.category_id = c.id 
                               WHERE p.slug = ? AND p.is_show = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * 增加浏览量
 */
function incrementViews($table, $id) {
    try {
        $pdo = getDB();
        $pdo->prepare("UPDATE {$table} SET views = views + 1 WHERE id = ?")->execute([$id]);
    } catch (Exception $e) {
        // 忽略错误
    }
}

/**
 * 获取新闻列表
 */
function getNewsList($limit = 0, $page = 1, $perPage = 10) {
    try {
        $pdo = getDB();
        $sql = "SELECT * FROM " . table('news') . " WHERE is_show = 1 ORDER BY created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        } elseif ($page > 0 && $perPage > 0) {
            $offset = ($page - 1) * $perPage;
            $sql .= " LIMIT {$offset}, " . (int)$perPage;
        }
        
        return $pdo->query($sql)->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * 获取新闻总数
 */
function getNewsCount() {
    try {
        $pdo = getDB();
        return $pdo->query("SELECT COUNT(*) FROM " . table('news') . " WHERE is_show = 1")->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * 获取单条新闻
 */
function getNews($slug) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM " . table('news') . " WHERE slug = ? AND is_show = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * 获取相关商品
 */
function getRelatedProducts($categoryId, $excludeId, $limit = 4) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM " . table('products') . " 
                               WHERE category_id = ? AND id != ? AND is_show = 1 
                               ORDER BY RAND() LIMIT " . (int)$limit);
        $stmt->execute([$categoryId, $excludeId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * 获取相关新闻
 */
function getRelatedNews($excludeId, $limit = 5) {
    try {
        $pdo = getDB();
        return $pdo->query("SELECT * FROM " . table('news') . " 
                            WHERE id != " . (int)$excludeId . " AND is_show = 1 
                            ORDER BY created_at DESC LIMIT " . (int)$limit)->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * 获取询盘数量
 */
function getInquiryCount($unreadOnly = false) {
    try {
        $pdo = getDB();
        $sql = "SELECT COUNT(*) FROM " . table('inquiries');
        if ($unreadOnly) {
            $sql .= " WHERE is_read = 0";
        }
        return $pdo->query($sql)->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * 生成导航菜单
 */
function navMenu($currentPage = '') {
    $items = [
        ['url' => 'index.php', 'label' => 'Home', 'page' => 'home'],
        ['url' => 'products.php', 'label' => 'Products', 'page' => 'products'],
        ['url' => 'news.php', 'label' => 'News', 'page' => 'news'],
        ['url' => 'about.php', 'label' => 'About Us', 'page' => 'about'],
        ['url' => 'contact.php', 'label' => 'Contact Us', 'page' => 'contact'],
    ];
    
    $html = '';
    foreach ($items as $item) {
        $active = ($currentPage == $item['page']) ? 'active' : '';
        $html .= '<li class="' . $active . '"><a href="' . $item['url'] . '">' . $item['label'] . '</a></li>';
    }
    return $html;
}

/**
 * 获取统计信息
 */
function getStats() {
    try {
        $pdo = getDB();
        $stats = [
            'products' => $pdo->query("SELECT COUNT(*) FROM " . table('products'))->fetchColumn(),
            'news' => $pdo->query("SELECT COUNT(*) FROM " . table('news'))->fetchColumn(),
            'inquiries' => $pdo->query("SELECT COUNT(*) FROM " . table('inquiries'))->fetchColumn(),
            'unread_inquiries' => $pdo->query("SELECT COUNT(*) FROM " . table('inquiries') . " WHERE is_read = 0")->fetchColumn(),
            'categories' => $pdo->query("SELECT COUNT(*) FROM " . table('categories'))->fetchColumn(),
        ];
        return $stats;
    } catch (Exception $e) {
        return ['products' => 0, 'news' => 0, 'inquiries' => 0, 'unread_inquiries' => 0, 'categories' => 0];
    }
}
