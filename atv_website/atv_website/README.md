# ATV外贸企业网站系统

## 简介
专业的ATV（全地形车）外贸企业网站管理系统，基于PHP+MySQL开发，适合ATV、UTV、卡丁车、越野车等powersports产品的外贸展示。

## 功能特性

### 前台功能（英文）
- 🏠 美观大气的首页 - Hero区域、产品展示、公司介绍、新闻动态
- 🏍️ 商品列表页 - 分类筛选、分页展示
- 📄 商品详情页 - 图片画廊、规格参数、富文本详情、相关产品推荐
- 📰 新闻列表页 - 文章列表、分页
- 📄 新闻详情页 - 正文、分享按钮、侧边栏推荐
- 📞 关于我们 / 联系我们页面
- 📨 在线询盘表单
- 🔍 SEO优化 - 每页独立的标题、关键词、描述

### 后台功能（中文）
- 🔐 安全登录系统
- 📊 仪表盘 - 数据统计概览
- 🏍️ 商品管理 - 增删改查、富文本编辑、多图上传、规格参数
- 📰 新闻管理 - 增删改查、富文本编辑
- 📨 询盘管理 - 查看、标记已读、删除
- 🔍 SEO设置 - 各页面独立设置Meta信息
- ⚙️ 网站设置 - 公司信息、联系方式、统计代码

## 系统要求
- PHP 7.4+ (推荐 PHP 8.0+)
- MySQL 5.7+ 或 MariaDB 10.3+
- Apache/Nginx 服务器
- mod_rewrite 支持（Apache）

## 安装步骤

1. **上传文件**
   将网站文件上传到服务器根目录或子目录

2. **创建数据库**
   在MySQL中创建一个新数据库（如 `atv_website`）

3. **运行安装程序**
   访问 `http://your-domain.com/install.php`
   - 填写数据库连接信息
   - 创建数据表
   - 设置管理员账号

4. **完成安装**
   安装完成后会自动创建 `config.php` 配置文件
   - 后台地址: `http://your-domain.com/admin/`
   - 使用安装时设置的管理员账号登录

5. **安全提示**
   - 安装完成后建议删除 `install.php` 文件
   - 修改后台目录名可增强安全性（需同步修改配置）

## 目录结构
```
atv_website/
├── install.php          # 安装程序
├── config.php           # 配置文件（安装后生成）
├── .htaccess           # URL重写和SEO优化
├── index.php           # 首页
├── products.php        # 商品列表
├── product.php         # 商品详情
├── news.php            # 新闻列表
├── news_detail.php     # 新闻详情
├── about.php           # 关于我们
├── contact.php         # 联系我们
├── includes/           # 核心文件
│   ├── functions.php   # 公共函数
│   ├── header.php      # 前台头部
│   └── footer.php      # 前台底部
├── admin/              # 后台管理
│   ├── login.php       # 登录页
│   ├── index.php       # 仪表盘
│   ├── products.php    # 商品管理
│   ├── product_edit.php # 商品编辑
│   ├── news.php        # 新闻管理
│   ├── news_edit.php   # 新闻编辑
│   ├── inquiries.php   # 询盘管理
│   ├── seo.php         # SEO设置
│   ├── settings.php    # 网站设置
│   ├── header.php      # 后台头部
│   └── footer.php      # 后台底部
├── assets/             # 静态资源
│   ├── css/style.css   # 主样式表
│   └── js/main.js      # 主脚本
└── uploads/            # 上传目录
    ├── products/       # 商品图片
    └── news/           # 新闻图片
```

## SEO特性
- 每页可独立设置 Meta Title / Keywords / Description
- 语义化HTML5标签
- Schema.org 结构化数据
- Open Graph 社交分享标签
- URL伪静态支持
- 图片懒加载
- Gzip压缩和浏览器缓存

## 技术支持
- 系统版本: 1.0.0
- 开发语言: PHP + MySQL
- 前端: HTML5 + CSS3 + JavaScript (原生)
- 无需Node.js、无需Webpack、无需Composer
- 传统服务器端渲染，利于搜索引擎收录

## 安全特性
- 密码使用 password_hash 加密
- PDO预处理语句防止SQL注入
- XSS输出转义
- CSRF防护基础
- 安全响应头
- 目录禁止浏览
