/**
 * Animasi dan logika halaman dashboard.
 */
export function initDashboard() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    // Zoom-in hero-img
    gsap.from('.hero-img', {
        scale: 1.1,
        duration: 1.5,
        ease: 'power2.out'
    });

    // Animasi untuk kata "Cerdas" menggunakan teknik Reveal Mask
    gsap.fromTo('.gsap-cerdas', 
        { 
            y: '100%',
            opacity: 0
        },
        {
            y: '0%',
            opacity: 1,
            duration: 1.0, 
            ease: 'power4.out',
            delay: 0.35,
            clearProps: 'transform'
        }
    );

    // Parallax foto hero saat scroll
    gsap.to('.hero-img', {
        yPercent: 20,
        ease: 'none',
        scrollTrigger: {
            trigger: '.hero-section',
            start: 'top top',
            end: 'bottom top',
            scrub: true
        }
    });

    // Animasi angka statistik count up
    gsap.utils.toArray('.stat-number').forEach(el => {
        const target = parseFloat(el.getAttribute('data-target'));
        const suffix = el.getAttribute('data-suffix') || '';
        const obj = { val: 0 };
        gsap.to(obj, {
            val: target,
            duration: 2,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 95%' },
            onUpdate: function() {
                el.textContent = Math.round(obj.val) + suffix;
            }
        });
    });

    // Heading section muncul dengan split per kata
    gsap.utils.toArray('.gsap-heading').forEach(el => {
        const words = el.innerHTML.replace(/<br\s*\/?>/gi, ' <br> ').split(/\s+/);
        el.innerHTML = words.map(w => {
            if (w.toLowerCase() === '<br>') return '<br>';
            return `<span class="inline-block overflow-hidden">
               <span class="inline-block gsap-word">${w}</span>
             </span>`;
        }).join(' ');
        
        gsap.from(el.querySelectorAll('.gsap-word'), {
            y: '100%', opacity: 0,
            duration: 0.7,
            ease: 'power3.out',
            stagger: 0.08,
            scrollTrigger: { trigger: el, start: 'top 88%' }
        });
    });

    // Animasi gradient bergerak halus
    gsap.to('.cta-section', {
        backgroundPosition: '100% 50%',
        duration: 8,
        ease: 'none',
        repeat: -1,
        yoyo: true,
    });

    // Lingkaran dekorasi bergerak lambat
    gsap.to('.cta-circle-1', {
        x: 20, y: -20,
        duration: 6,
        ease: 'sine.inOut',
        repeat: -1,
        yoyo: true
    });

    gsap.to('.cta-circle-2', {
        x: -15, y: 15,
        duration: 8,
        ease: 'sine.inOut',
        repeat: -1,
        yoyo: true
    });

    // Tombol CTA pulse halus
    gsap.to('.cta-btn', {
        scale: 1.02,
        duration: 2,
        ease: 'sine.inOut',
        repeat: -1,
        yoyo: true
    });
}
