/**
 * Logika dan animasi halaman Riwayat
 */
export function initRiwayat() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    gsap.from('.riwayat-header', { y: -20, opacity: 0, duration: 0.4 });

    // beri animasi merayap pada batang progres untuk setiap sesi tanaman
    document.querySelectorAll('.riwayat-progress').forEach(bar => {
        const target = bar.style.width;
        bar.style.width = '0%';
        gsap.to(bar, {
            width: target,
            duration: 1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: bar,
                start: 'top 90%'
            }
        });
    });

    // berikan efek melayang saat kursor menyentuh kartu riwayat
    document.querySelectorAll('.riwayat-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, { y: -4, duration: 0.2, ease: 'power2.out' });
        });
        card.addEventListener('mouseleave', () => {
            gsap.to(card, { y: 0, duration: 0.2 });
        });
    });
}
