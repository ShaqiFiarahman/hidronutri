/**
 * Logika dan animasi halaman Cek Kondisi
 */
export function initCekKondisi() {
    if (!window.cekKondisiData || typeof gsap === 'undefined') return;

    const { phMin, phMax, ecMin, ecMax, ppmMin, ppmMax, suhuMin, suhuMax } = window.cekKondisiData;

    // inisialisasi elemen DOM dari input dan tampilan
    const phInput = document.getElementById('ph_aktual');
    const phDisplay = document.getElementById('ph-val-display');
    const phBadge = document.getElementById('ph-status-badge');

    const ecInput = document.getElementById('ec_aktual');
    const ecDisplay = document.getElementById('ec-val-display');
    const ecBadge = document.getElementById('ec-status-badge');

    const ppmInput = document.getElementById('ppm_aktual');
    const ppmDisplay = document.getElementById('ppm-val-display');
    const ppmBadge = document.getElementById('ppm-status-badge');

    const suhuInput = document.getElementById('suhu_aktual');
    const suhuDisplay = document.getElementById('suhu-val-display');
    const suhuBadge = document.getElementById('suhu-status-badge');

    function evaluateSlider(input, display, badge, min, max, label) {
        if (!input || !display || !badge) return;
        const val = parseFloat(input.value);
        display.textContent = val;

        // kembalikan styling badge ke bentuk awal sebelum penyesuaian kondisi
        badge.className = "text-[10px] font-bold px-2 py-0.5 rounded-full border transition-colors duration-200";

        // atur tampilan warna dan teks berdasarkan rentang nilai ideal
        if (val >= min && val <= max) {
            badge.textContent = "Ideal";
            badge.classList.add('bg-brand-greenpal', 'text-brand-green', 'border-brand-green/20');
            input.style.setProperty('--thumb-color', '#2D6A0F');
        } else if (val < min) {
            badge.textContent = "Rendah";
            badge.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
            input.style.setProperty('--thumb-color', '#EF4444');
        } else {
            badge.textContent = "Tinggi";
            badge.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
            input.style.setProperty('--thumb-color', '#EF4444');
        }
    }

    // daftarkan pendengar event untuk memperbarui tampilan saat input berubah
    if (phInput) phInput.addEventListener('input', () => evaluateSlider(phInput, phDisplay, phBadge, phMin, phMax, 'pH'));
    
    if (ecInput && ppmInput) {
        ecInput.addEventListener('input', () => {
            evaluateSlider(ecInput, ecDisplay, ecBadge, ecMin, ecMax, 'EC');
            ppmInput.value = Math.round(parseFloat(ecInput.value) * 500);
            evaluateSlider(ppmInput, ppmDisplay, ppmBadge, ppmMin, ppmMax, 'PPM');
        });
        
        ppmInput.addEventListener('input', () => {
            evaluateSlider(ppmInput, ppmDisplay, ppmBadge, ppmMin, ppmMax, 'PPM');
            ecInput.value = (parseFloat(ppmInput.value) / 500).toFixed(1);
            evaluateSlider(ecInput, ecDisplay, ecBadge, ecMin, ecMax, 'EC');
        });
    }

    if (suhuInput) suhuInput.addEventListener('input', () => evaluateSlider(suhuInput, suhuDisplay, suhuBadge, suhuMin, suhuMax, 'Suhu'));

    // pasang nilai awal pada antarmuka saat halaman dimuat
    if (phInput) evaluateSlider(phInput, phDisplay, phBadge, phMin, phMax, 'pH');
    if (ecInput) evaluateSlider(ecInput, ecDisplay, ecBadge, ecMin, ecMax, 'EC');
    if (ppmInput) evaluateSlider(ppmInput, ppmDisplay, ppmBadge, ppmMin, ppmMax, 'PPM');
    if (suhuInput) evaluateSlider(suhuInput, suhuDisplay, suhuBadge, suhuMin, suhuMax, 'Suhu');

    // Masuk halaman
    gsap.from('.cek-header', { y: -30, opacity: 0, duration: 0.5 });
    gsap.from('.konteks-card', { 
        scale: 0.95, opacity: 0, duration: 0.5, delay: 0.2,
        ease: 'back.out(1.2)' 
    });
    gsap.from('.slider-card', { 
        y: 30, opacity: 0, stagger: 0.15, duration: 0.5, delay: 0.3 
    });

    // Animasi saat diagnosa selesai
    function animateDiagnosaResult() {
        const cards = document.querySelectorAll('.diagnosa-result-card');
        if (cards.length > 0) {
            gsap.from(cards, {
                y: 30, opacity: 0, scale: 0.95,
                stagger: 0.15, duration: 0.5,
                ease: 'back.out(1.2)'
            });
        }
    }
    animateDiagnosaResult();

    // berikan efek denyut pada pegangan slider saat digeser
    document.querySelectorAll('input[type=range]').forEach(slider => {
        // terapkan animasi pembesaran sementara pada elemen
        slider.addEventListener('input', function() {
            gsap.to(this, { '--thumb-scale': 1.2, duration: 0.1 });
            setTimeout(() => gsap.to(this, { '--thumb-scale': 1, duration: 0.1 }), 150);
        });
    });
}

// daftarkan fungsi animasi ke window agar bisa diakses dari atribut onclick HTML
window.animateStatus = function(statusEl, isNormal) {
    if (typeof gsap === 'undefined') return;
    gsap.from(statusEl, { 
        scale: 0.8, opacity: 0, duration: 0.3, 
        ease: 'back.out(1.4)' 
    });
};
