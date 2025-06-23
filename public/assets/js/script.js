// public/assets/js/script.js - VERSI UPGRADE FUTURISTIK & FINAL

document.addEventListener('DOMContentLoaded', function() {
    // ======================================================================
    // Smooth Scroll untuk Tautan Navigasi & Tombol Internal
    // ======================================================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault(); // Mencegah perilaku default (langsung melompat)

            const targetId = this.getAttribute('href'); // Ambil ID target (misal: #about-section)
            const targetElement = document.querySelector(targetId); // Dapatkan elemen target

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth', // Efek scroll yang mulus
                    block: 'start'       // Scroll sampai bagian atas elemen terlihat
                });
                history.pushState(null, null, targetId); // Perbarui URL
            }
        });
    });

    // ======================================================================
    // Efek Fade-in saat Elemen Masuk ke Viewport
    // ======================================================================
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in-on-scroll').forEach(section => {
        observer.observe(section);
    });

    // ======================================================================
    // Menyoroti Tautan Navigasi Aktif saat Menggulir
    // ======================================================================
    const navLinks = document.querySelectorAll('nav ul li a[href^="#"]');
    const sections = document.querySelectorAll('.main-content-section, #home-section');

    window.addEventListener('scroll', () => {
        let currentSectionId = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - (window.innerHeight / 3);
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop && pageYOffset < sectionTop + sectionHeight) {
                currentSectionId = section.getAttribute('id');
            }
        });
        if (pageYOffset < sections[0].offsetTop - (window.innerHeight / 3)) {
            currentSectionId = 'home-section';
        }
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + currentSectionId) {
                link.classList.add('active');
            }
        });
    });
    window.dispatchEvent(new Event('scroll'));

    // ======================================================================
    // Smart Header (Hide on Scroll Down, Show on Scroll Up)
    // ======================================================================
    const header = document.querySelector('header');
    let lastScrollY = window.pageYOffset;

    window.addEventListener('scroll', () => {
        const headerFullHeight = header.offsetHeight;
        const navHeight = document.querySelector('nav').offsetHeight;

        // Logika aktif di SEMUA UKURAN LAYAR
        if (window.pageYOffset > lastScrollY && window.pageYOffset > headerFullHeight) {
            header.classList.add('header-hidden');
        } else if (window.pageYOffset < lastScrollY && window.pageYOffset > navHeight) {
            header.classList.remove('header-hidden');
        } else if (window.pageYOffset <= navHeight) {
            header.classList.remove('header-hidden');
        }
        lastScrollY = window.pageYOffset;
    });

    // ======================================================================
    // Efek Parallax Sederhana untuk Hero Section
    // ======================================================================
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        window.addEventListener('scroll', () => {
            const scrollPosition = window.pageYOffset;
            heroSection.style.backgroundPositionY = -scrollPosition * 0.5 + 'px';
        });
    }

    // ======================================================================
    // Tombol "Kembali ke Atas" (Back to Top Button)
    // ======================================================================
    const backToTopButton = document.getElementById('back-to-top');
    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ======================================================================
    // Scroll ke Hash Saat Halaman Dimuat (Jika ada hash di URL)
    // ======================================================================
    if (window.location.hash) {
        const initialTargetElement = document.querySelector(window.location.hash);
        if (initialTargetElement) {
            setTimeout(() => {
                initialTargetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }
    }

    // ======================================================================
    // Hamburger Menu (Mobile Only) - Logic
    // ======================================================================
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const nav = document.querySelector('nav');

    if (hamburgerMenu && nav) {
        hamburgerMenu.addEventListener('click', () => {
            hamburgerMenu.classList.toggle('active');
            nav.classList.toggle('nav-open');
        });

        // Close nav when a link is clicked (for single-page nav)
        nav.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', () => {
                hamburgerMenu.classList.remove('active');
                nav.classList.remove('nav-open');
            });
        });
    }
});