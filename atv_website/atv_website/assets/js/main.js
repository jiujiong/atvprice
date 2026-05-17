/**
 * ATV Export Website - Main JavaScript
 * 主交互脚本
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ==================== Mobile Menu ====================
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileClose = document.getElementById('mobileClose');
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    function openMobileMenu() {
        mobileMenu.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileMenu() {
        mobileMenu.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (mobileToggle) mobileToggle.addEventListener('click', openMobileMenu);
    if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileMenu);
    
    // ==================== Sticky Header ====================
    const mainHeader = document.getElementById('mainHeader');
    let lastScrollY = window.scrollY;
    
    function handleScroll() {
        const scrollY = window.scrollY;
        
        if (mainHeader) {
            if (scrollY > 50) {
                mainHeader.classList.add('scrolled');
            } else {
                mainHeader.classList.remove('scrolled');
            }
        }
        
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            if (scrollY > 500) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }
        
        lastScrollY = scrollY;
    }
    
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // ==================== Back to Top ====================
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // ==================== Smooth Scroll ====================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // ==================== Gallery Thumbnails ====================
    const galleryMain = document.querySelector('.product-gallery-main img');
    const galleryThumbs = document.querySelectorAll('.gallery-thumb');
    
    galleryThumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const imgSrc = this.querySelector('img').src;
            if (galleryMain) {
                galleryMain.src = imgSrc;
            }
            galleryThumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // ==================== Scroll Animations ====================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll('.product-card, .news-card, .feature-card, .value-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
    
    // ==================== Form Validation ====================
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger)';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Show error message
                let alertBox = form.querySelector('.form-alert');
                if (!alertBox) {
                    alertBox = document.createElement('div');
                    alertBox.className = 'form-alert error';
                    form.insertBefore(alertBox, form.firstChild);
                }
                alertBox.textContent = 'Please fill in all required fields.';
            }
        });
    });
    
    // ==================== Lazy Loading Images ====================
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // ==================== Counter Animation ====================
    function animateCounter(el, target, duration) {
        let start = 0;
        const increment = target / (duration / 16);
        
        function updateCounter() {
            start += increment;
            if (start < target) {
                el.textContent = Math.floor(start).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                el.textContent = target.toLocaleString();
            }
        }
        
        updateCounter();
    }
    
    const counterObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('[data-counter]');
                counters.forEach(counter => {
                    const target = parseInt(counter.dataset.counter);
                    animateCounter(counter, target, 1500);
                });
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const heroStats = document.querySelector('.hero-stats');
    if (heroStats) counterObserver.observe(heroStats);
    
    // ==================== Product Card Hover Effect ====================
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '';
        });
    });
    
    // ==================== Search Toggle ====================
    const searchToggle = document.getElementById('searchToggle');
    const searchBox = document.getElementById('searchBox');
    
    if (searchToggle && searchBox) {
        searchToggle.addEventListener('click', function() {
            searchBox.classList.toggle('active');
            if (searchBox.classList.contains('active')) {
                searchBox.querySelector('input').focus();
            }
        });
    }
    
    // ==================== Toast Notification ====================
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--info)'};
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Make showToast globally available
    window.showToast = showToast;
    
    // ==================== Parallax Effect ====================
    const parallaxElements = document.querySelectorAll('[data-parallax]');
    
    if (parallaxElements.length > 0 && !window.matchMedia('(pointer: coarse)').matches) {
        window.addEventListener('scroll', function() {
            const scrollY = window.scrollY;
            parallaxElements.forEach(el => {
                const speed = parseFloat(el.dataset.parallax) || 0.5;
                el.style.transform = `translateY(${scrollY * speed}px)`;
            });
        }, { passive: true });
    }
    
});

// ==================== CSS Animations (Injected) ====================
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
`;
document.head.appendChild(styleSheet);
