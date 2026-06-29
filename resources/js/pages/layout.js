/**
 * Inisialisasi animasi GSAP global dan utilitas layout.
 */
export function initLayout() {
    if (typeof gsap === 'undefined') return;

    // 1. Page Enter Animation
    gsap.to('.page-wrapper', {
        opacity: 1,
        duration: 0.5,
        ease: 'power2.out'
    });

    // 2. Scroll Progress Bar
    if (typeof ScrollTrigger !== 'undefined') {
        gsap.to('#pageProgress', {
            width: '100%',
            ease: 'none',
            scrollTrigger: {
                trigger: 'body',
                start: 'top top',
                end: 'bottom bottom',
                scrub: 0.3,
            }
        });
    }

    // 3. Cursor Follower (desktop only)
    if (window.innerWidth > 768) {
        const dot  = document.getElementById('cursorDot');
        const ring = document.getElementById('cursorRing');
        
        if (dot && ring) {
            window.addEventListener('mousemove', e => {
                gsap.to(dot,  { x: e.clientX - 4,  y: e.clientY - 4,  duration: 0.1 });
                gsap.to(ring, { x: e.clientX - 16, y: e.clientY - 16, duration: 0.4, ease: 'power2.out' });
            });
            
            document.querySelectorAll('a, button, .cursor-grow').forEach(el => {
                el.addEventListener('mouseenter', () => {
                    gsap.to(ring, { scale: 2, opacity: 0.3, duration: 0.3 });
                    gsap.to(dot,  { scale: 0, duration: 0.3 });
                });
                el.addEventListener('mouseleave', () => {
                    gsap.to(ring, { scale: 1, opacity: 0.5, duration: 0.3 });
                    gsap.to(dot,  { scale: 1, duration: 0.3 });
                });
            });
        }
    }

    // 4. Page Exit Animation
    document.querySelectorAll('a[href]').forEach(link => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (link.hostname !== window.location.hostname) return;
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            gsap.to('.page-wrapper', {
                opacity: 0,
                y: -20,
                duration: 0.3,
                ease: 'power2.in',
                onComplete: () => window.location.href = href
            });
        });
    });

    // 5. Universal Scroll Animations
    if (typeof ScrollTrigger !== 'undefined') {
        gsap.utils.toArray('.gsap-up').forEach((el, i) => {
            gsap.to(el, {
                opacity: 1, y: 0,
                duration: 0.7,
                ease: 'power3.out',
                delay: i * 0.05,
                scrollTrigger: {
                    trigger: el,
                    start: 'top 88%',
                    toggleActions: 'play none none none'
                }
            });
        });

        gsap.utils.toArray('.gsap-left').forEach(el => {
            gsap.to(el, {
                opacity: 1, x: 0,
                duration: 0.7,
                ease: 'power3.out',
                scrollTrigger: { trigger: el, start: 'top 88%' }
            });
        });

        gsap.utils.toArray('.gsap-right').forEach(el => {
            gsap.to(el, {
                opacity: 1, x: 0,
                duration: 0.7,
                ease: 'power3.out',
                scrollTrigger: { trigger: el, start: 'top 88%' }
            });
        });

        gsap.utils.toArray('.gsap-scale').forEach(el => {
            gsap.to(el, {
                opacity: 1, scale: 1,
                duration: 0.6,
                ease: 'back.out(1.4)',
                scrollTrigger: { trigger: el, start: 'top 88%' }
            });
        });

        gsap.utils.toArray('.gsap-stagger').forEach(parent => {
            const children = parent.children;
            gsap.fromTo(children, 
                { opacity: 0, y: 30 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power3.out',
                    stagger: 0.1,
                    scrollTrigger: { trigger: parent, start: 'top 85%' }
                }
            );
        });

        // Marquee section fade in
        if (document.querySelector('.marquee-section')) {
            gsap.from('.marquee-section', {
                opacity: 0, y: 40,
                duration: 0.8,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '.marquee-section',
                    start: 'top 90%'
                }
            });
        }
    }

    // Smooth scroll internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target && typeof gsap !== 'undefined' && typeof ScrollToPlugin !== 'undefined') {
                gsap.to(window, {
                    scrollTo: { y: target, offsetY: 80 },
                    duration: 1,
                    ease: 'power3.inOut'
                });
            }
        });
    });
}
