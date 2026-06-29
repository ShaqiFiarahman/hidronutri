/**
 * Logika footer animasi saat scroll.
 */
export function initFooter() {
    if (typeof ScrollTrigger !== 'undefined' && typeof gsap !== 'undefined') {
        // Deteksi saat footer mulai terlihat
        ScrollTrigger.create({
            trigger: '.footer-reveal',
            start: 'top bottom',
            end: 'top 50%',
            onEnter: () => {
                gsap.to('.main-content', {
                    borderBottomLeftRadius: '0px',
                    borderBottomRightRadius: '0px',
                    duration: 0.4,
                    ease: 'power2.out'
                });
            },
            onLeaveBack: () => {
                gsap.to('.main-content', {
                    borderBottomLeftRadius: '2.5rem',
                    borderBottomRightRadius: '2.5rem',
                    duration: 0.4,
                    ease: 'power2.out'
                });
            }
        });

        // Animasi konten footer saat muncul
        gsap.from('.footer-reveal .grid > div', {
            y: 30,
            opacity: 0,
            stagger: 0.1,
            duration: 0.6,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: '.footer-reveal',
                start: 'top 80%',
            }
        });

        // Link footer hover dengan GSAP
        document.querySelectorAll('footer a').forEach(link => {
            link.addEventListener('mouseenter', () => {
                gsap.to(link, { x: 4, duration: 0.2, ease: 'power2.out' });
            });
            link.addEventListener('mouseleave', () => {
                gsap.to(link, { x: 0, duration: 0.2 });
            });
        });
    }
}
