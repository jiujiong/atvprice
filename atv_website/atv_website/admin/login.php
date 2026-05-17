<?php
/**
 * ATV外贸网站 - 后台登录
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// 已登录则跳转
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT * FROM " . table('admins') . " WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // 更新最后登录时间
                $pdo->prepare("UPDATE " . table('admins') . " SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);
                
                header('Location: index.php');
                exit;
            } else {
                $error = '用户名或密码错误';
            }
        } catch (Exception $e) {
            $error = '登录失败，请检查数据库配置';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录 - ATV外贸网站系统</title>
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
        .login-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #e94560, #c23a51);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .login-header h1 {
            font-size: 22px;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .login-header p {
            color: #999;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #e94560;
        }
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #e94560, #c23a51);
            color: white;
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
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #999;
            font-size: 13px;
        }
        .back-link a:hover {
            color: #e94560;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <div class="login-logo">A</div>
            <h1>后台管理系统</h1>
            <p>ATV外贸网站管理后台</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" class="form-control" placeholder="请输入用户名" required autofocus>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" class="form-control" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn btn-primary">登录</button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">← 返回网站首页</a>
        </div>
    </div>
</body>
</html>
