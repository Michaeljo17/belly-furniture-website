// public/assets/js/script.js - FINAL VERSION

document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Inisialisasi semua fungsionalitas utama
     */
    function init() {
        initHeader();
        initSmoothScroll();
        initNavHighlighting();
        initFadeInObserver();
        initBackToTopButton();
        initSliders();
    }

    /**
     * HEADER: Efek bayangan saat scroll & Navigasi Responsif (Hamburger)
     */
    function initHeader() {
        const header = document.querySelector('header');
        const hamburgerMenu = document.querySelector('.hamburger-menu');
        const nav = document.querySelector('nav');

        if (header) {
            window.addEventListener('scroll', () => {
                header.classList.toggle('scrolled', window.pageYOffset > 50);
            });
        }

        if (hamburgerMenu && nav) {
            hamburgerMenu.addEventListener('click', () => {
                hamburgerMenu.classList.toggle('active');
                nav.classList.toggle('nav-open');
            });

            nav.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', () => {
                    hamburgerMenu.classList.remove('active');
                    nav.classList.remove('nav-open');
                });
            });
        }
    }

    /**
     * SMOOTH SCROLL: Scroll mulus ke section dengan offset header
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (!targetId || targetId === '#') return;
                
                e.preventDefault();
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                    history.pushState(null, null, targetId);
                }
            });
        });
    }

    /**
     * NAV HIGHLIGHTING: Menyorot link navigasi yang aktif saat scroll
     */
    function initNavHighlighting() {
        const navLinks = document.querySelectorAll('nav ul li a[href^="#"]');
        const sections = document.querySelectorAll('main section[id]');
        if (navLinks.length === 0 || sections.length === 0) return;

        const activateNavOnScroll = () => {
            let currentSectionId = '';
            const headerHeight = 85;

            sections.forEach(section => {
                if(window.pageYOffset >= section.offsetTop - headerHeight) {
                    currentSectionId = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + currentSectionId) {
                    link.classList.add('active');
                }
            });
        };
        window.addEventListener('scroll', activateNavOnScroll);
        activateNavOnScroll();
    }

    /**
     * FADE-IN OBSERVER: Animasi fade-in untuk elemen saat masuk viewport
     */
    function initFadeInObserver() {
        const observerOptions = { root: null, rootMargin: '0px', threshold: 0.1 };
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-on-scroll').forEach(el => observer.observe(el));
    }
    
    /**
     * SLIDERS: Fungsionalitas untuk tombol next/previous
     */
    function initSliders() {
        const sliderSections = document.querySelectorAll('#products-section, #portfolio-section');
        sliderSections.forEach(section => {
            const prevBtn = section.querySelector('.prev-btn');
            const nextBtn = section.querySelector('.next-btn');
            const grid = section.querySelector('.slider-grid');
            const container = section.querySelector('.slider-container');

            if (!prevBtn || !nextBtn || !grid || !container) return;

            const card = grid.querySelector('.card');
            if (!card) return;
            
            const gap = parseFloat(window.getComputedStyle(grid).gap);
            const cardWidth = card.offsetWidth;

            nextBtn.addEventListener('click', () => {
                container.scrollBy({ left: cardWidth + gap, behavior: 'smooth' });
            });

            prevBtn.addEventListener('click', () => {
                container.scrollBy({ left: -(cardWidth + gap), behavior: 'smooth' });
            });
        });
    }

    /**
     * BACK TO TOP BUTTON: Tombol kembali ke atas
     */
    function initBackToTopButton() {
        const backToTopButton = document.getElementById('back-to-top');
        if (!backToTopButton) return;
        
        window.addEventListener('scroll', () => {
            backToTopButton.classList.toggle('show', window.pageYOffset > 300);
        });

        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Jalankan semua fungsi
    init();

});