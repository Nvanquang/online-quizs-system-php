// assets/js/main.js

document.addEventListener('DOMContentLoaded', function () {

    // ===== SCROLL EFFECTS =====
    const navbar = document.querySelector('.navbar');
    const scrollToTopBtn = document.getElementById('scrollToTop');

    window.addEventListener('scroll', function () {
        // Navbar shadow on scroll
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Show/hide scroll to top button
        if (scrollToTopBtn) {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('show');
            } else {
                scrollToTopBtn.classList.remove('show');
            }
        }
    });

    // Scroll to top functionality
    if (scrollToTopBtn) {
        scrollToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ===== CATEGORY NAVIGATION =====
    const categoryItems = document.querySelectorAll('.category-item');

    categoryItems.forEach(item => {
        item.addEventListener('click', function () {
            // Remove active class from all items
            categoryItems.forEach(cat => cat.classList.remove('active'));
            // Add active class to clicked item
            this.classList.add('active');

            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.classList.add('ripple-effect');
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        // Hover sound effect (visual feedback)
        item.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });

        item.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });


    // ===== SECTION TITLES ANIMATION =====
    const sectionTitles = document.querySelectorAll('.section-title');

    const titleObserver = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInLeft 0.8s ease forwards';
                titleObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    sectionTitles.forEach(title => {
        titleObserver.observe(title);
    });

    // ===== JOIN GAME VIA PIN =====
    const joinInput = document.querySelector('.join-input');
    if (joinInput) {
        // Sanitize input to digits and spaces only
        joinInput.addEventListener('input', function () {
            const caret = this.selectionStart;
            this.value = this.value.replace(/[^0-9\s]/g, '');
            this.setSelectionRange(caret, caret);
        });

        // On Enter key -> navigate to waiting page
        joinInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const code = this.value.replace(/\s+/g, '').trim();
                if (!code) {
                    this.classList.remove('shake');
                    // force reflow to restart animation
                    void this.offsetWidth;
                    this.classList.add('shake');
                    return;
                }
                window.location.href = `/game/waiting/${encodeURIComponent(code)}`;
            }
        });
    }

    // ===== DISCORD BUTTON EFFECT =====
    const discordBtn = document.querySelector('.btn-discord');

    if (discordBtn) {
        discordBtn.addEventListener('mouseenter', function () {
            this.innerHTML = '<i class="fab fa-discord"></i> THAM GIA NGAY!';
        });

        discordBtn.addEventListener('mouseleave', function () {
            this.innerHTML = 'THAM GIA QUIZZ VỚI CHÚNG TÔI';
        });
    }

    // ===== RANDOM GRADIENT FOR QUIZ IMAGES =====
    const gradients = [
        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
        'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)'
    ];

    const quizImages = document.querySelectorAll('.quiz-image');
    quizImages.forEach(img => {
        if (!img.style.background || img.style.background === '') {
            const randomGradient = gradients[Math.floor(Math.random() * gradients.length)];
            img.style.background = randomGradient;
        }
    });

    // ===== SMOOTH SCROLL FOR LINKS =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===== ADD LOADING ANIMATION =====
    window.addEventListener('load', function () {
        document.body.style.opacity = '0';
        setTimeout(() => {
            document.body.style.transition = 'opacity 0.5s ease';
            document.body.style.opacity = '1';
        }, 100);
    });

    // ===== KEYBOARD NAVIGATION =====
    document.addEventListener('keydown', function (e) {
        // Press 'S' to focus on search
        if (e.key === 's' || e.key === 'S') {
            if (e.target.tagName !== 'INPUT') {
                e.preventDefault();
                document.querySelector('.btn-search')?.click();
            }
        }

        // Press 'ESC' to scroll to top
        if (e.key === 'Escape') {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    });

    // ===== PERFORMANCE: Lazy load images =====
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// ===== ADD SHAKE ANIMATION FOR INPUT =====
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .shake {
        animation: shake 0.5s ease;
    }
    
    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        width: 10px;
        height: 10px;
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple {
        to {
            width: 100px;
            height: 100px;
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ===== Reusable Quiz Carousel for Multiple Sections =====
(function ($) {
    
    // Class để quản lý một carousel
    class QuizCarousel {
        constructor(container) {
            this.$container = $(container);
            this.$track = this.$container.find('.quiz-carousel-track');
            this.$viewport = this.$container.find('.quiz-carousel-viewport');
            this.$items = this.$track.children('.quiz-col');
            this.$empty = this.$container.siblings('.quiz-empty-alert');
            
            // Tìm buttons trong cùng section
            const $section = this.$container.closest('.quiz-section');
            this.$prev = $section.find('.carousel-prev-btn');
            this.$next = $section.find('.carousel-next-btn');
            
            this.currentIndex = 0;
            this.slideWidth = 0;
            this.touchStartX = null;
            this.touchDX = 0;
            
            // Kiểm tra nếu không có items
            if (this.$items.length === 0) {
                if (this.$empty.length) {
                    this.$empty.removeClass('d-none');
                }
                return;
            }
            
            this.init();
        }
        
        getItemsPerSlide() {
            const w = window.innerWidth;
            if (w >= 1200) return 6;
            if (w >= 992) return 5;
            if (w >= 768) return 4;
            if (w >= 576) return 3;
            return 2;
        }
        
        getTotalSlides() {
            return Math.max(1, Math.ceil(this.$items.length / this.itemsPerSlide));
        }
        
        updateNav() {
            const maxSlides = this.getTotalSlides();
            this.$prev.prop('disabled', this.currentIndex === 0);
            this.$next.prop('disabled', this.currentIndex >= maxSlides - 1);
        }
        
        jumpToIndex(slideIndex, animate = true) {
            const offsetCols = slideIndex * this.itemsPerSlide;
            const tx = -(offsetCols * this.slideWidth);
            
            this.$track.css({
                'transition': animate ? 'transform 400ms ease' : 'none',
                'transform': `translateX(${tx}px)`
            });
            
            this.$container.attr('aria-live', 'polite');
        }
        
        layout() {
            this.itemsPerSlide = this.getItemsPerSlide();
            const vpWidth = this.$viewport.innerWidth();
            this.slideWidth = vpWidth / this.itemsPerSlide;
            
            // Set width cho mỗi item
            this.$items.css('width', `${100 / this.itemsPerSlide}%`);
            
            // Điều chỉnh currentIndex nếu cần
            const maxSlides = this.getTotalSlides();
            if (this.currentIndex >= maxSlides) {
                this.currentIndex = Math.max(0, maxSlides - 1);
            }
            
            this.jumpToIndex(this.currentIndex, false);
            this.updateNav();
        }
        
        slideNext() {
            const maxSlides = this.getTotalSlides();
            if (this.currentIndex < maxSlides - 1) {
                this.currentIndex++;
                this.jumpToIndex(this.currentIndex, true);
                this.updateNav();
            }
        }
        
        slidePrev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.jumpToIndex(this.currentIndex, true);
                this.updateNav();
            }
        }
        
        bindEvents() {
            // Navigation buttons
            this.$next.on('click', () => this.slideNext());
            this.$prev.on('click', () => this.slidePrev());
            
            // Keyboard navigation
            this.$container.on('keydown', (e) => {
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.slideNext();
                }
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.slidePrev();
                }
            });
            
            // Touch events
            this.$viewport.on('touchstart', (e) => {
                if (!e.originalEvent.touches || !e.originalEvent.touches[0]) return;
                this.touchStartX = e.originalEvent.touches[0].clientX;
                this.touchDX = 0;
            });
            
            this.$viewport.on('touchmove', (e) => {
                if (!this.touchStartX || !e.originalEvent.touches || !e.originalEvent.touches[0]) return;
                const x = e.originalEvent.touches[0].clientX;
                this.touchDX = x - this.touchStartX;
            });
            
            this.$viewport.on('touchend', () => {
                if (Math.abs(this.touchDX) > 40) {
                    if (this.touchDX < 0) {
                        this.slideNext();
                    } else {
                        this.slidePrev();
                    }
                }
                this.touchStartX = null;
                this.touchDX = 0;
            });
            
            // Resize handling với debounce
            let resizeTimer = null;
            $(window).on('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => this.layout(), 150);
            });
        }
        
        init() {
            this.layout();
            this.bindEvents();
        }
    }
    
    // Khởi tạo tất cả carousels khi DOM ready
    $(document).ready(function() {
        const carousels = [];
        
        // Tìm tất cả carousel containers và khởi tạo
        $('.quiz-carousel-container').each(function() {
            const carousel = new QuizCarousel(this);
            if (carousel.$items && carousel.$items.length > 0) {
                carousels.push(carousel);
            }
        });
        
        // Optional: Expose ra global scope nếu cần
        window.quizCarousels = carousels;
        
        console.log(`Đã khởi tạo ${carousels.length} carousel(s)`);
    });
    
})(jQuery);