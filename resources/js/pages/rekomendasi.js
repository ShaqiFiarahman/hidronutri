/**
 * Logika dan animasi halaman Rekomendasi
 */
export function initRekomendasi() {
    if (!window.rekomendasiData) return;

    const { durasiMap, tanamanMap } = window.rekomendasiData;

    const faseMetadata = {
        'semai':           { label: 'Semai',           desc: 'Penyemaian benih',     icon: 'fa-seedling' },
        'vegetatif_awal':  { label: 'Vegetatif Awal',  desc: 'Daun awal tumbuh',     icon: 'fa-leaf' },
        'vegetatif_akhir': { label: 'Vegetatif Akhir', desc: 'Pertumbuhan rimbun',   icon: 'fa-plant-wilt' },
        'panen':           { label: 'Panen',           desc: 'Kematangan optimal',   icon: 'fa-basket-shopping' },
        'vegetatif':       { label: 'Vegetatif',       desc: 'Pertumbuhan daun',     icon: 'fa-leaf' },
        'pembungaan':      { label: 'Pembungaan',      desc: 'Pembentukan bunga',    icon: 'fa-sun' },
        'pembuahan':       { label: 'Pembuahan',       desc: 'Pengisian buah',       icon: 'fa-apple-whole' },
        'pembesaran':      { label: 'Pembesaran',      desc: 'Pembesaran buah',      icon: 'fa-expand' },
        'transisi':        { label: 'Transisi',        desc: 'Menuju generatif',     icon: 'fa-arrows-turn-right' },
        'pematangan':      { label: 'Pematangan',      desc: 'Pematangan buah',      icon: 'fa-hourglass-half' },
    };

    function updatePhasePreview() {
        const tanamanId = document.getElementById('selected-tanaman-id').value;
        const tanggalMulai = document.getElementById('tanggal_mulai').value;
        const previewBox = document.getElementById('phase-preview-box');
        
        if (!tanamanId || !tanggalMulai) {
            previewBox.classList.add('hidden');
            return;
        }

        const namaTanaman = tanamanMap[tanamanId];
        const mapFase = durasiMap[namaTanaman];

        if (!mapFase) {
            previewBox.classList.add('hidden');
            return;
        }

        // Hitung usia dalam hari
        const tglMulaiObj = new Date(tanggalMulai);
        tglMulaiObj.setHours(0,0,0,0);
        const today = new Date();
        today.setHours(0,0,0,0);
        
        const diffTime = Math.max(0, today.getTime() - tglMulaiObj.getTime());
        const usiaHari = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        // Tentukan fase
        let determinedFaseKey = 'semai';
        let found = false;
        
        for (const [key, data] of Object.entries(mapFase)) {
            if (usiaHari <= data.kumulatif) {
                determinedFaseKey = key;
                found = true;
                break;
            }
        }
        
        if (!found) {
            // Ambil fase terakhir
            const keys = Object.keys(mapFase);
            determinedFaseKey = keys[keys.length - 1];
        }

        // Update UI
        const meta = faseMetadata[determinedFaseKey] || { label: determinedFaseKey.replace(/_/g, ' '), icon: 'fa-leaf' };
        
        document.getElementById('preview-usia').innerText = `Usia: ${usiaHari} Hari`;
        document.getElementById('preview-fase').innerText = meta.label;
        document.getElementById('preview-icon').className = `fa-solid ${meta.icon} text-sm`;
        
        previewBox.classList.remove('hidden');
        
        // Animasi kecil
        if (typeof gsap !== 'undefined') {
            gsap.fromTo(previewBox, { scale: 0.95, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.3, ease: 'back.out(1.5)' });
        }
    }

    // Global function to update system availability based on selected plant
    function updateSistemAvailability(tanamanNama) {
        if (!tanamanNama) return;
        const isRestricted = (tanamanNama === 'cabai' || tanamanNama === 'melon');
        const restrictedSystems = ['wick', 'rakit_apung'];
        
        const selectedSistemInput = document.getElementById('sistem_hidroponik');
        let currentSelected = selectedSistemInput.value;

        document.querySelectorAll('.sistem-card').forEach(card => {
            const val = card.getAttribute('data-value');
            const imgContainer = card.querySelector('.relative.h-40');
            const info = card.querySelector('.p-4');
            const title = card.querySelector('.font-semibold');
            
            if (isRestricted && restrictedSystems.includes(val)) {
                // Nonaktifkan kartu sistem
                card.classList.add('pointer-events-none', 'relative');
                card.classList.remove('cursor-pointer', 'hover:border-brand-green', 'hover:shadow-sm');
                
                if (imgContainer) imgContainer.classList.add('grayscale', 'opacity-50');
                if (info) info.classList.add('opacity-50');
                
                if (title) {
                    title.classList.remove('text-brand-black');
                    title.classList.add('text-brand-gray');
                }
                
                // Deselect jika sebelumnya terpilih
                if (currentSelected === val) {
                    card.classList.remove('border-brand-green', 'bg-brand-offwhite');
                    card.classList.add('border-brand-graylt');
                    const check = card.querySelector('.selected-check');
                    if (check) {
                        check.classList.add('hidden');
                        check.classList.remove('flex');
                    }
                    selectedSistemInput.value = '';
                }
                
                let badge = card.querySelector('.tidak-disarankan-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'tidak-disarankan-badge absolute top-3 left-3 bg-red-500 text-white rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider z-20 shadow-sm';
                    badge.innerText = 'Tidak Disarankan';
                    card.appendChild(badge);
                }
            } else {
                // Aktifkan kembali kartu sistem
                card.classList.remove('pointer-events-none', 'relative');
                card.classList.add('cursor-pointer', 'hover:border-brand-green', 'hover:shadow-sm');
                
                if (imgContainer) imgContainer.classList.remove('grayscale', 'opacity-50');
                if (info) info.classList.remove('opacity-50');
                
                if (title) {
                    title.classList.remove('text-brand-gray');
                    title.classList.add('text-brand-black');
                }
                
                const badge = card.querySelector('.tidak-disarankan-badge');
                if (badge) {
                    badge.remove();
                }
            }
        });
    }

    // Global function for system selection
    window.selectSistem = function(el, nilai) {
        document.querySelectorAll('.sistem-card').forEach(card => {
            card.classList.remove('border-brand-green', 'bg-brand-offwhite');
            card.classList.add('border-brand-graylt');
            const check = card.querySelector('.selected-check');
            if (check) {
                check.classList.add('hidden');
                check.classList.remove('flex');
            }
        });

        el.classList.add('border-brand-green', 'bg-brand-offwhite');
        el.classList.remove('border-brand-graylt');
        const check = el.querySelector('.selected-check');
        if (check) {
            check.classList.remove('hidden');
            check.classList.add('flex');
        }

        document.getElementById('sistem_hidroponik').value = nilai;
        
        const errorEl = document.getElementById('sistem-error');
        if (errorEl) {
            errorEl.classList.add('hidden');
        }
    };

    // Initialize on page load (support old/existing value)
    const initialSistem = document.getElementById('sistem_hidroponik');
    if (initialSistem && initialSistem.value) {
        const card = document.querySelector(`.sistem-card[data-value="${initialSistem.value}"]`);
        if (card) {
            window.selectSistem(card, initialSistem.value);
        }
    }

    // Validasi sebelum submit
    const form = document.getElementById('rekomendasi-form') || document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const sistemHidro = document.getElementById('sistem_hidroponik');
            if (sistemHidro && !sistemHidro.value) {
                e.preventDefault();
                const errorEl = document.getElementById('sistem-error');
                if (errorEl) {
                    errorEl.classList.remove('hidden');
                    errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    const tanamanCards = document.querySelectorAll('.tanaman-card');
    const hiddenTanamanInput = document.getElementById('selected-tanaman-id');
    const tglMulai = document.getElementById('tanggal_mulai');
    
    if (tglMulai) {
        tglMulai.addEventListener('change', updatePhasePreview);
    }

    // Animasi saat tanaman dipilih
    function animateCardSelect(el) {
        if (typeof gsap === 'undefined') return;
        gsap.timeline()
        .to(el, { scale: 0.96, duration: 0.1, ease: 'power2.in' })
        .to(el, { scale: 1.02, duration: 0.15, ease: 'power2.out' })
        .to(el, { scale: 1, duration: 0.1 });
    }

    // Handle plant cards click
    tanamanCards.forEach(card => {
        card.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            
            tanamanCards.forEach(c => {
                c.classList.remove('border-brand-green', 'bg-brand-greenpal', 'ring-2', 'ring-brand-greenpal/20');
                c.classList.add('border-brand-graylt');
                
                const emojiCont = c.querySelector('.w-14');
                if (emojiCont) {
                    emojiCont.classList.remove('bg-white');
                    emojiCont.classList.add('bg-brand-offwhite');
                }
                
                const check = c.querySelector('.checkmark-indicator');
                if (check) {
                    check.classList.add('opacity-0', 'scale-75');
                    check.classList.remove('opacity-100', 'scale-100');
                }
            });
            
            this.classList.remove('border-brand-graylt');
            this.classList.add('border-brand-green', 'bg-brand-greenpal', 'ring-2', 'ring-brand-greenpal/20');
            
            const emojiCont = this.querySelector('.w-14');
            if (emojiCont) {
                emojiCont.classList.remove('bg-brand-offwhite');
                emojiCont.classList.add('bg-white');
            }
            
            const check = this.querySelector('.checkmark-indicator');
            if (check) {
                check.classList.remove('opacity-0', 'scale-75');
                check.classList.add('opacity-100', 'scale-100');
            }
            
            if (hiddenTanamanInput) {
                hiddenTanamanInput.value = id;
            }
            
            animateCardSelect(this);
            updatePhasePreview();
            updateSistemAvailability(nama);
        });
    });

    // Jika ada old value tanaman, render preview dan update ketersediaan sistem
    if (hiddenTanamanInput && hiddenTanamanInput.value) {
        updatePhasePreview();
        const activeCard = document.querySelector(`.tanaman-card[data-id="${hiddenTanamanInput.value}"]`);
        if (activeCard) {
            const nama = activeCard.getAttribute('data-nama');
            updateSistemAvailability(nama);
        }
    }

    // ─── GSAP PAGE-SPECIFIC ANIMATIONS ──────────────────────────
    if (typeof gsap !== 'undefined') {
        // Heading hero
        gsap.fromTo('.hero-heading', 
            { y: 60, opacity: 0 },
            {
                y: 0,
                opacity: 1,
                duration: 1,
                ease: 'power4.out',
                delay: 0.2
            }
        );

        // Card tanaman muncul stagger
        gsap.fromTo('.tanaman-card', 
            { y: 40, opacity: 0, scale: 0.95 },
            {
                y: 0,
                opacity: 1,
                scale: 1,
                duration: 0.5,
                ease: 'power3.out',
                stagger: 0.08,
                delay: 0.4
            }
        );

        // Card sistem hidroponik
        gsap.fromTo('.sistem-card', 
            { y: 30, opacity: 0 },
            {
                y: 0,
                opacity: 1,
                duration: 0.5,
                ease: 'power3.out',
                stagger: 0.1,
                delay: 0.8
            }
        );

        // Tombol submit pulse saat hover
        const btnSubmit = document.querySelector('.btn-submit');
        if (btnSubmit) {
            btnSubmit.addEventListener('mouseenter', () => {
                gsap.to(btnSubmit, { scale: 1.02, duration: 0.2 });
            });
            btnSubmit.addEventListener('mouseleave', () => {
                gsap.to(btnSubmit, { scale: 1, duration: 0.2 });
            });
        }
    }
}
