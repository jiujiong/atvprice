# ATV Foreign Trade Enterprise Website System
## Introduction
A professional ATV (All-Terrain Vehicle) foreign trade enterprise website management system developed based on PHP+MySQL. It is suitable for foreign trade display of powersports products including ATVs, UTVs, go-karts and off-road vehicles.

## Functional Features
### Frontend Functions (English)
- 🏠 Elegant Homepage - Hero banner section, product display, company profile, latest news
- 🏍️ Product List Page - Category filtering and pagination display
- 📄 Product Detail Page - Image gallery, specifications, rich-text descriptions and related product recommendations
- 📰 News List Page - Article listing with pagination
- 📄 News Detail Page - Full content, share buttons and sidebar recommended articles
- 📞 About Us / Contact Us Pages
- 📨 Online Inquiry Form
- 🔍 SEO Optimization - Independent page titles, keywords and descriptions for each page

### Backend Functions (Chinese)
- 🔐 Secure system login
- 📊 Dashboard - Core data overview
- 🏍️ Product Management - Full CRUD operations, rich-text editing, multi-image upload and parameter configuration
- 📰 News Management - Full CRUD operations and rich-text editing
- 📨 Inquiry Management - View, mark as read and delete inquiries
- 🔍 SEO Settings - Independent Meta information configuration for all pages
- ⚙️ Website Settings - Company information, contact details and statistical code embedding

## System Requirements
- PHP 7.4+ (PHP 8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Apache / Nginx Web Server
- mod_rewrite module enabled (for Apache)

## Installation Guide
1. **Upload Files**
Upload all website files to the root directory or subdirectory of your server.

2. **Create Database**
Create a new MySQL database (e.g. atv_website).

3. **Run Installation Wizard**
Visit [shturl.cc/DxA4XNHSLlgBZsiYQBrKM4a6](shturl.cc/DxA4XNHSLlgBZsiYQBrKM4a6)
- Fill in database connection credentials
- Automatically generate data tables
- Set up administrator account

4. **Complete Installation**
The system will automatically generate the config.php configuration file after installation.
- Backend Access URL: [shturl.cc/XQGVlpOJz7ZrSMjuveh](shturl.cc/XQGVlpOJz7ZrSMjuveh)
- Log in with the administrator account created during installation

5. **Security Tips**
- Delete install.php after successful installation
- Rename the admin directory to enhance website security (update relevant configurations synchronously)

## Directory Structure
```plaintext
atv_website/
├── install.php          # Installation script
├── config.php           # Configuration file (generated after installation)
├── .htaccess            # URL rewrite & SEO optimization rules
├── index.php            # Homepage
├── products.php         # Product list page
├── product.php          # Product detail page
├── news.php             # News list page
├── news_detail.php      # News detail page
├── about.php            # About Us page
├── contact.php          # Contact Us page
├── includes/            # Core functional files
│   ├── functions.php    # Common public functions
│   ├── header.php       # Frontend page header
│   └── footer.php       # Frontend page footer
├── admin/               # Backend management module
│   ├── login.php        # Admin login page
│   ├── index.php        # Admin dashboard
│   ├── products.php     # Product management
│   ├── product_edit.php # Product editing page
│   ├── news.php         # News management
│   ├── news_edit.php    # News editing page
│   ├── inquiries.php    # Inquiry management
│   ├── seo.php          # SEO configuration
│   ├── settings.php     # Global website settings
│   ├── header.php       # Backend public header
│   └── footer.php       # Backend public footer
├── assets/              # Static resource files
│   ├── css/style.css    # Main stylesheet
│   └── js/main.js       # Core frontend script
└── uploads/             # File upload directory
    ├── products/        # Product image storage
    └── news/            # News image storage
```

## SEO Advantages
- Independent Meta Title / Keywords / Description for every single page
- Semantic HTML5 structure
- Schema.org structured data support
- Open Graph social sharing tags
- Friendly pseudo-static URL support
- Image lazy loading function
- Gzip compression and browser cache optimization

## Technical Specifications
- System Version: 1.0.0
- Core Language: PHP + MySQL
- Frontend Technology: HTML5 + CSS3 + Vanilla JavaScript
- No requirements for Node.js, Webpack or Composer
- Traditional server-side rendering, highly friendly for search engine indexing

## Security Mechanisms
- User passwords encrypted via password_hash algorithm
- PDO prepared statements to prevent SQL injection
- Standard XSS output escaping
- Basic CSRF protection mechanism
- Secure HTTP response headers configuration
- Directory browsing access restriction

---

# Company Profile: Yongkang Modou Vehicle Co., Ltd.
## Main Products
‪ATV‬, ‪Go-Kart‬, ‪Farm ATV‬, ‪ATV Acc‬

## Address
No. 12, Keyuan 5th Street, Changcheng Village, Dongcheng Subdistrict, Yongkang City, Jinhua, Zhejiang Province, China, 1st Floor

## Main Markets
South America, Southeast Asia, Africa, Oceania, Mid East, Eastern Asia

## International Commercial Terms(Incoterms)
FOB, EXW, DDP

## Terms of Payment
T/T

## Average Lead Time
- Peak Season Lead Time: 1-3 months
- Off Season Lead Time: one month

## BV Audit Report No.
MIC-ASI2532002
[Verify Now] | [Read Free Report]

## Average Response Time
≤8.78h

## Company Introduction
Yongkang Modou Vehicle Co., Ltd.: Your ATV and go kart business partner

For 17 years, Yongkang Modou Vehicle Co., Ltd. Has been dedicated to the design, development, production, and sales of agricultural vehicles, atvs, go karts, and other products.

We can provide OEM and ODM services for our customers. The company has the ability to design and develop, and can turn customers' dreams into reality.

Our state-of-the-art manufacturing factory has a seamless production line, from carefully procuring raw materials, precision mold manufacturing, and expert frame welding, to perfect painting, meticulous assembly, and rigorous testing. This end-to-end process ensures that every vehicle taken offline is of the highest quality and meets strict international safety and performance standards.

At YONGKANG MODOU VEHICLE Co., Ltd., we are not just selling products; We are engaged in realizing our dreams. MD: Achieve Your Dreams "is not only a catchy slogan, but also a philosophy that permeates all aspects of our operations. We strive to establish long-term mutually beneficial relationships with our clients. Every satisfied customer is not just a one-time buyer, but a returning customer, a loyal supporter, and most importantly, a friend. We are committed to continuous improvement and innovation. We firmly focus on quality, attention to detail, and customer satisfaction. We look forward to your joining the YONGKANG MODOU VEHICLE Co., Ltd family and embarking on a journey of success together.

---

### 下载方法
1. 全选以上所有文本
2. 复制到记事本/VS Code等编辑器
3. 保存为 **`atv-website-system.md`** 文件即可
