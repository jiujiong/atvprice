<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'contact';

$message = '';
$messageType = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $inquiryMessage = trim($_POST['message'] ?? '');
    $productId = intval($_POST['product_id'] ?? 0);
    
    if (empty($name) || empty($email) || empty($inquiryMessage)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("INSERT INTO " . table('inquiries') . " 
                (product_id, name, email, phone, company, country, message) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$productId, $name, $email, $phone, $company, $country, $inquiryMessage]);
            
            $message = 'Thank you for your inquiry! We will get back to you within 24 hours.';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Sorry, there was an error submitting your inquiry. Please try again.';
            $messageType = 'error';
        }
    }
}

// 获取关联产品
$productSlug = $_GET['product'] ?? '';
$productInfo = null;
if ($productSlug) {
    $productInfo = getProduct($productSlug);
}

$pageTitle = pageTitle('contact');
$metaKeywords = metaKeywords('contact');
$metaDescription = metaDescription('contact');

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">/</span>
            <span>Contact Us</span>
        </div>
    </div>
</section>

<!-- Contact Page -->
<section class="contact-page">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Info -->
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>
                    Have a question about our products or interested in becoming a distributor? 
                    Fill out the form and our team will respond within 24 hours.
                </p>
                
                <div class="contact-item">
                    <div class="contact-item-icon">🏭</div>
                    <div class="contact-item-info">
                        <h4>Factory Address</h4>
                        <p><?php echo e(getSetting('company_address', 'No.168 Industrial Zone, Yongkang City, Zhejiang Province, China')); ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-item-icon">☎️</div>
                    <div class="contact-item-info">
                        <h4>Phone</h4>
                        <p><?php echo e(getSetting('company_phone', '+86-579-12345678')); ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-item-icon">✉️</div>
                    <div class="contact-item-info">
                        <h4>Email</h4>
                        <p><?php echo e(getSetting('company_email', 'sales@atvpower.com')); ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-item-icon">💬</div>
                    <div class="contact-item-info">
                        <h4>WhatsApp / WeChat</h4>
                        <p><?php echo e(getSetting('company_whatsapp', '+8613800138000')); ?></p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-item-icon">🕐</div>
                    <div class="contact-item-info">
                        <h4>Working Hours</h4>
                        <p>Monday - Saturday: 8:00 AM - 6:00 PM (GMT+8)</p>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form-container">
                <h3>Send Inquiry</h3>
                
                <?php if ($message): ?>
                <div class="form-alert <?php echo $messageType; ?>"><?php echo e($message); ?></div>
                <?php endif; ?>
                
                <?php if ($productInfo): ?>
                <div style="background:var(--light);padding:16px;border-radius:8px;margin-bottom:20px;border-left:4px solid var(--primary);">
                    <p style="font-size:13px;color:var(--gray);margin-bottom:4px;">Inquiry about:</p>
                    <p style="font-weight:600;color:var(--secondary);"><?php echo e($productInfo['title']); ?></p>
                </div>
                <?php endif; ?>
                
                <form method="post" action="" data-validate>
                    <?php if ($productInfo): ?>
                    <input type="hidden" name="product_id" value="<?php echo $productInfo['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Your Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="John Smith">
                        </div>
                        <div class="form-group">
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="john@company.com">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+1 234 567 8900">
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company" class="form-control" placeholder="Your Company Ltd.">
                        </div>
                        <div class="form-group full-width">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control" placeholder="United States">
                        </div>
                        <div class="form-group full-width">
                            <label>Message <span class="required">*</span></label>
                            <textarea name="message" class="form-control" required placeholder="Please tell us about your requirements, target market, estimated order quantity, etc."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">Send Inquiry</button>
                </form>
            </div>
        </div>
        
        <!-- Map -->
        <div class="map-container">
            🗺️ Google Maps Embed - Add your map in admin settings
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
