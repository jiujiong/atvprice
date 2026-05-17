<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
checkAdminLogin();

$admin = getCurrentAdmin();
$stats = getStats();
$currentNav = $currentNav ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? '管理后台'; ?> - ATV外贸网站系统</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    <style>
        :root {
            --admin-primary: #e94560;
            --admin-primary-dark: #c23a51;
            --admin-secondary: #1a1a2e;
            --admin-dark: #16213e;
            --admin-light: #f5f6fa;
            --admin-sidebar: #1e1e2d;
            --admin-sidebar-hover: #27273a;
            --admin-text: #2d2d3a;
            --admin-text-light: #6c757d;
            --admin-border: #e4e6ef;
            --admin-success: #28a745;
            --admin-warning: #ffc107;
            --admin-danger: #dc3545;
            --admin-info: #17a2b8;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
            background: var(--admin-light);
            color: var(--admin-text);
            line-height: 1.6;
        }
        
        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: var(--admin-sidebar);
            color: rgba(255,255,255,0.7);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }
        
        .sidebar-brand h3 {
            color: white;
            font-size: 16px;
            font-weight: 600;
        }
        .sidebar-brand span {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
        }
        
        .nav-section {
            padding: 16px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .nav-label {
            padding: 0 20px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.35);
            margin-bottom: 8px;
        }
        
        .nav-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .nav-menu li a:hover {
            background: var(--admin-sidebar-hover);
            color: white;
        }
        
        .nav-menu li.active a {
            background: var(--admin-sidebar-hover);
            color: var(--admin-primary);
            border-left-color: var(--admin-primary);
        }
        
        .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .nav-badge {
            margin-left: auto;
            background: var(--admin-primary);
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
        }
        
        /* Top Header */
        .admin-header {
            background: white;
            padding: 0 28px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--admin-border);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--admin-text);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .header-link {
            color: var(--admin-text-light);
            font-size: 13px;
        }
        .header-link:hover { color: var(--admin-primary); }
        
        .header-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px;
            background: var(--admin-light);
            border-radius: 8px;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .user-info {
            font-size: 13px;
        }
        .user-info strong { color: var(--admin-text); }
        .user-info span { color: var(--admin-text-light); font-size: 11px; }
        
        /* Content */
        .admin-content {
            padding: 28px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        .stat-icon.blue { background: rgba(23, 162, 184, 0.1); }
        .stat-icon.green { background: rgba(40, 167, 69, 0.1); }
        .stat-icon.orange { background: rgba(255, 193, 7, 0.1); }
        .stat-icon.red { background: rgba(233, 69, 96, 0.1); }
        
        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--admin-text);
            margin-bottom: 2px;
        }
        .stat-info p {
            font-size: 13px;
            color: var(--admin-text-light);
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }
        
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header h3 {
            font-size: 16px;
            font-weight: 600;
        }
        
        .card-body {
            padding: 24px;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-sm { padding: 6px 14px; font-size: 13px; }
        .btn-lg { padding: 14px 28px; font-size: 15px; }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
            color: white;
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        
        .btn-success { background: var(--admin-success); color: white; }
        .btn-danger { background: var(--admin-danger); color: white; }
        .btn-warning { background: var(--admin-warning); color: #333; }
        .btn-info { background: var(--admin-info); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-light { background: var(--admin-light); color: var(--admin-text); border: 1px solid var(--admin-border); }
        
        .btn:hover { opacity: 0.9; }
        
        /* Table */
        .table-responsive { overflow-x: auto; }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
        }
        
        .data-table th {
            background: var(--admin-light);
            font-weight: 600;
            color: var(--admin-text);
            border-bottom: 2px solid var(--admin-border);
            white-space: nowrap;
        }
        
        .data-table td {
            border-bottom: 1px solid var(--admin-border);
            color: var(--admin-text);
        }
        
        .data-table tr:hover td {
            background: rgba(233, 69, 96, 0.02);
        }
        
        .data-table img {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        /* Status */
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
        }
        .status.active { background: rgba(40, 167, 69, 0.1); color: var(--admin-success); }
        .status.inactive { background: rgba(220, 53, 69, 0.1); color: var(--admin-danger); }
        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }
        
        /* Alert */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        
        /* Form */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--admin-text);
        }
        .form-group label .required {
            color: var(--admin-primary);
            margin-left: 2px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--admin-border);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
            font-family: inherit;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--admin-primary);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-row.three {
            grid-template-columns: 1fr 1fr 1fr;
        }
        
        /* Image Upload */
        .image-upload {
            border: 2px dashed var(--admin-border);
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .image-upload:hover {
            border-color: var(--admin-primary);
        }
        .image-upload input[type="file"] {
            display: none;
        }
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 12px;
        }
        
        /* Gallery */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }
        .gallery-item {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 8px;
            overflow: hidden;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .gallery-item .remove {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 24px;
            height: 24px;
            background: var(--admin-danger);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--admin-border);
        }
        .tab {
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 500;
            color: var(--admin-text-light);
            cursor: pointer;
            border: none;
            background: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .tab:hover { color: var(--admin-primary); }
        .tab.active {
            color: var(--admin-primary);
            border-bottom-color: var(--admin-primary);
        }
        
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        
        /* Search & Filter */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-box {
            position: relative;
            flex: 1;
            max-width: 320px;
        }
        .search-box input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 2px solid var(--admin-border);
            border-radius: 8px;
            font-size: 14px;
        }
        .search-box::before {
            content: '🔍';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            opacity: 0.5;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 24px;
        }
        .pagination a, .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 6px;
            font-size: 14px;
            color: var(--admin-text);
            background: white;
            border: 1px solid var(--admin-border);
        }
        .pagination a:hover {
            background: var(--admin-primary);
            color: white;
            border-color: var(--admin-primary);
        }
        .pagination .current {
            background: var(--admin-primary);
            color: white;
            border-color: var(--admin-primary);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.4;
        }
        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--admin-text);
        }
        .empty-state p {
            color: var(--admin-text-light);
            font-size: 14px;
        }
        
        /* SEO Preview */
        .seo-preview {
            background: white;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 20px;
            margin-top: 12px;
        }
        .seo-preview-title {
            color: #1a0dab;
            font-size: 18px;
            margin-bottom: 4px;
        }
        .seo-preview-url {
            color: #006621;
            font-size: 14px;
            margin-bottom: 4px;
        }
        .seo-preview-desc {
            color: #545454;
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .form-row, .form-row.three { grid-template-columns: 1fr; }
        }
        
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.active { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
        
        /* Rich Text Editor */
        .editor {
            border: 2px solid var(--admin-border);
            border-radius: 8px;
            overflow: hidden;
        }
        .editor-toolbar {
            background: var(--admin-light);
            padding: 8px 12px;
            border-bottom: 1px solid var(--admin-border);
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        .editor-toolbar button {
            padding: 6px 10px;
            border: 1px solid var(--admin-border);
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }
        .editor-toolbar button:hover {
            background: var(--admin-light);
        }
        .editor-content {
            min-height: 200px;
            padding: 14px;
            outline: none;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">A</div>
            <div class="sidebar-brand">
                <h3>ATV管理系统</h3>
                <span>外贸网站后台</span>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-label">主菜单</div>
            <ul class="nav-menu">
                <li class="<?php echo $currentNav == 'dashboard' ? 'active' : ''; ?>">
                    <a href="index.php"><span class="nav-icon">📊</span> 仪表盘</a>
                </li>
                <li class="<?php echo $currentNav == 'products' ? 'active' : ''; ?>">
                    <a href="products.php"><span class="nav-icon">🏍️</span> 商品管理</a>
                </li>
                <li class="<?php echo $currentNav == 'news' ? 'active' : ''; ?>">
                    <a href="news.php"><span class="nav-icon">📰</span> 新闻管理</a>
                </li>
                <li class="<?php echo $currentNav == 'inquiries' ? 'active' : ''; ?>">
                    <a href="inquiries.php">
                        <span class="nav-icon">📨</span> 询盘管理
                        <?php if ($stats['unread_inquiries'] > 0): ?>
                        <span class="nav-badge"><?php echo $stats['unread_inquiries']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="nav-section">
            <div class="nav-label">设置</div>
            <ul class="nav-menu">
                <li class="<?php echo $currentNav == 'seo' ? 'active' : ''; ?>">
                    <a href="seo.php"><span class="nav-icon">🔍</span> SEO设置</a>
                </li>
                <li class="<?php echo $currentNav == 'settings' ? 'active' : ''; ?>">
                    <a href="settings.php"><span class="nav-icon">⚙️</span> 网站设置</a>
                </li>
                <li>
                    <a href="logout.php"><span class="nav-icon">🚪</span> 退出登录</a>
                </li>
            </ul>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-left">
                <h2><?php echo $pageTitle ?? '管理后台'; ?></h2>
            </div>
            <div class="header-right">
                <a href="../index.php" target="_blank" class="header-link">查看网站 →</a>
                <div class="header-user">
                    <div class="user-avatar"><?php echo strtoupper(substr($admin['username'], 0, 1)); ?></div>
                    <div class="user-info">
                        <strong><?php echo e($admin['username']); ?></strong><br>
                        <span>管理员</span>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <div class="admin-content">
