/**
 * Logika dan animasi halaman Jadwal
 */
export function initJadwal() {
    if (typeof gsap === 'undefined') return;

    const tlJadwal = gsap.timeline({ delay: 0.15 });

    tlJadwal
        .from('.jadwal-header', { y: -20, opacity: 0, duration: 0.4 })
        .from('.progress-card', { 
            scale: 0.95, opacity: 0, duration: 0.5, 
            ease: 'back.out(1.2)' 
        }, '-=0.1');

    // Animasi progress bar
    const progressFill = document.querySelector('.progress-fill');
    if (progressFill) {
        const targetWidth = progressFill.style.width;
        progressFill.style.width = '0%';
        gsap.to(progressFill, {
            width: targetWidth,
            duration: 1.2,
            ease: 'power2.out',
            delay: 0.5
        });
    }
}
