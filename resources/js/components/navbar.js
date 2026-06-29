/**
 * Logika mobile menu dan scroll efek navbar.
 */
export function initNavbar() {
    window.toggleMobileMenu = function() {
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('menu-icon');
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        }
    };

    // ubah tampilan bilah navigasi menjadi solid saat halaman digulir
    window.addEventListener('scroll', function() {
        const header = document.getElementById('navbar-header');
        if (!header) return;
        
        if (window.scrollY > 10) {
            header.classList.remove('border-transparent');
            header.classList.add('border-brand-graylt');
        } else {
            header.classList.remove('border-brand-graylt');
            header.classList.add('border-transparent');
        }
    });

    if (typeof ScrollTrigger !== 'undefined') {
        ScrollTrigger.create({
            start: 'top -60',
            onEnter: () => document.getElementById('navbar-header')?.classList.add('navbar-scrolled'),
            onLeaveBack: () => document.getElementById('navbar-header')?.classList.remove('navbar-scrolled'),
        });
    }
}
