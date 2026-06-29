/**
 * Logika dan animasi halaman Hasil Rekomendasi
 */
export function initHasil() {
    if (!window.hasilData || typeof gsap === 'undefined') return;

    const { kalenderData, sesiAktifId, csrfToken, dosisA, dosisB } = window.hasilData;

    // --- Kalkulator AB Mix ---
    const volumeInput = document.getElementById('volume-air');
    const calcA = document.getElementById('kalkulasi-a');
    const calcB = document.getElementById('kalkulasi-b');

    function updateKalkulasi() {
        if (!volumeInput || !calcA || !calcB) return;
        const volume = parseFloat(volumeInput.value) || 0;
        const resA = (dosisA * volume).toFixed(1);
        const resB = (dosisB * volume).toFixed(1);

        calcA.textContent = resA + " ml";
        calcB.textContent = resB + " ml";
    }

    if (volumeInput) {
        volumeInput.addEventListener('input', updateKalkulasi);
    }

    // --- Timeline Animasi Masuk ---
    const tlHasil = gsap.timeline({ delay: 0.2 });

    tlHasil
        .from('.breadcrumb', { y: -20, opacity: 0, duration: 0.4 })
        .from('.phase-timeline .pt-step', {
            y: 20, opacity: 0, stagger: 0.12, duration: 0.4,
            ease: 'power2.out'
        }, '-=0.2')
        .from('.metric-card', {
            y: 30, opacity: 0, scale: 0.9, stagger: 0.1, duration: 0.5,
            ease: 'back.out(1.4)'
        }, '-=0.2')
        .from('.formula-card', {
            x: -30, opacity: 0, duration: 0.5
        }, '-=0.3')
        .from('.jadwal-card', {
            x: 30, opacity: 0, duration: 0.5
        }, '-=0.5')
        .from('.warning-box', {
            y: 20, opacity: 0, duration: 0.4
        }, '-=0.2');

    // Animasi nilai metric (count up)
    document.querySelectorAll('.metric-value').forEach(el => {
        const val = parseFloat(el.textContent);
        if (!isNaN(val)) {
            const obj = { v: 0 };
            gsap.to(obj, {
                v: val, duration: 1.2, ease: 'power2.out', delay: 0.8,
                onUpdate: function () {
                    el.textContent = obj.v.toFixed(1);
                }
            });
        }
    });

    // GSAP Calendar elements
    tlHasil
        .from('#calendar-container', { y: 20, opacity: 0, duration: 0.5 }, '-=0.3')
        .from('.progress-card', { y: 20, opacity: 0, duration: 0.5 }, '-=0.3')
        .from('.next-phase-card', { x: 20, opacity: 0, duration: 0.4 }, '-=0.2');

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

    // --- Fungsi Kalender & Detail Hari ---
    window.openDayDetail = function(dateStr) {
        if (!kalenderData) return;
        const dayData = kalenderData.find(d => d.date === dateStr);
        if (!dayData) return;

        const panel = document.getElementById('day-detail-panel');
        const title = document.getElementById('detail-date-title');
        const container = document.getElementById('detail-tasks-container');

        if (!panel || !title || !container) return;

        // Format Title
        const d = new Date(dateStr);
        title.textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

        container.innerHTML = '';

        if (dayData.kegiatan.length === 0) {
            container.innerHTML = '<div class="text-sm text-brand-gray italic">Tidak ada agenda perawatan pada tanggal ini.</div>';
        } else {
            dayData.kegiatan.forEach((keg) => {
                const logExists = dayData.logs.find(l => l.tipe === keg.tipe);
                const isDone = logExists && logExists.status === 'selesai';
                const isWarning = logExists && logExists.status === 'perlu_perhatian';
                const isToday = dayData.isToday;
                const isPast = dayData.isPast;

                let cardBg = isDone ? 'bg-brand-greenpal/20 border-brand-green/30' : (isWarning ? 'bg-amber-50/70 border-amber-300' : (isPast && !isToday ? 'bg-red-50/50 border-red-200' : 'bg-brand-offwhite'));
                let iconHtml = isDone ? '<i class="fa-solid fa-circle-check text-lg text-brand-green"></i>' : (isWarning ? '<i class="fa-solid fa-triangle-exclamation text-lg text-amber-600"></i>' : (isPast && !isToday ? '<i class="fa-solid fa-clock text-lg text-red-500"></i>' : (keg.tipe === 'cek' ? '<i class="fa-solid fa-eye-dropper text-lg text-brand-gray"></i>' : '<i class="fa-solid fa-flask text-lg text-brand-gray"></i>')));

                let html = `
                <div class="border border-brand-graylt rounded-xl p-4 transition-colors ${cardBg}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">${iconHtml}</div>
                            <div>
                                <h5 class="font-bold text-brand-black text-sm">${keg.judul}</h5>
                                <p class="text-xs text-brand-gray mt-1">${keg.deskripsi}</p>
                            </div>
                        </div>
                    </div>`;

                if (isDone || isWarning) {
                    if (isDone) {
                        html += `<div class="mt-3 text-xs text-brand-green font-semibold bg-white inline-block px-2.5 py-1 rounded-md border border-brand-green/20 shadow-2xs"><i class="fa-solid fa-check mr-1"></i>Selesai (Normal)</div>`;
                    } else {
                        html += `<div class="mt-3 text-xs text-amber-700 font-bold bg-amber-100 inline-block px-2.5 py-1 rounded-md border border-amber-300 shadow-2xs"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Selesai (Perlu Tindakan Koreksi)</div>`;
                    }

                    if (keg.tipe === 'cek') {
                        html += `<div class="mt-2 text-[10px] text-brand-gray grid grid-cols-3 gap-2">
                            <div class="bg-white p-1.5 rounded text-center border border-brand-graylt">pH: <strong>${logExists.ph ?? '-'}</strong></div>
                            <div class="bg-white p-1.5 rounded text-center border border-brand-graylt">PPM: <strong>${logExists.ppm ?? '-'}</strong></div>
                            <div class="bg-white p-1.5 rounded text-center border border-brand-graylt">Suhu: <strong>${logExists.suhu ?? '-'}°C</strong></div>
                        </div>`;
                    }
                } else if (isToday || (isPast && !isToday)) {
                    if (isPast && !isToday) {
                        html += `<div class="mt-3 text-xs text-red-700 font-bold bg-red-100 inline-block px-2.5 py-1 rounded-md border border-red-300 shadow-2xs"><i class="fa-solid fa-clock mr-1"></i>Terlewat</div>`;
                    }
                    
                    if (isToday) {
                        if (keg.tipe === 'cek') {
                            html += `<div class="mt-4 border-t border-brand-graylt pt-4">
                                <a href="/cek-kondisi" class="bg-brand-black hover:bg-brand-green text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors duration-200 inline-block">
                                    Lakukan Pengecekan
                                </a>
                            </div>`;
                        } else {
                            html += `<div class="mt-4 border-t border-brand-graylt pt-4">
                                <form onsubmit="window.submitLog(event, '${keg.tipe}', '${dateStr}')" class="mt-4">
                                    <button type="submit" class="bg-brand-black hover:bg-brand-green text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors duration-200">
                                        Tandai Selesai
                                    </button>
                                </form>
                            </div>`;
                        }
                    }
                }
                html += `</div>`;
                container.innerHTML += html;
            });
        }

        panel.classList.remove('hidden');
        gsap.fromTo(panel, { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.3, ease: 'power2.out' });

        document.querySelectorAll('[id^="cal-cell-"]').forEach(el => el.classList.remove('ring-4', 'ring-brand-greenpal'));
        document.getElementById(`cal-cell-${dateStr}`).classList.add('ring-4', 'ring-brand-greenpal');
    };

    window.submitLog = async function(e, tipe, dateStr) {
        e.preventDefault();
        
        const payload = {
            sesi_tanam_id: window.hasilData.sesiAktifId,
            tanggal: dateStr,
            tipe: tipe,
            _token: window.hasilData.csrfToken
        };

        try {
            const response = await fetch('/log-perawatan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            
            const res = await response.json();
            
            if (res.success) {
                // Update data in JS variable
                const dayData = window.hasilData.kalenderData.find(d => d.date === dateStr);
                const existIdx = dayData.logs.findIndex(l => l.tipe === res.data.tipe);
                if (existIdx >= 0) {
                    dayData.logs[existIdx] = res.data;
                } else {
                    dayData.logs.push(res.data);
                }
                
                // Re-render panel
                window.openDayDetail(dateStr);

                // Update kalender cell UI
                const indicator = document.getElementById(`cal-indicator-${dateStr}`);
                if (indicator) {
                    let indicatorHtml = '';
                    dayData.kegiatan.forEach(k => {
                        const logEx = dayData.logs.find(l => l.tipe === k.tipe);
                        const done = logEx && logEx.status === 'selesai';
                        const warn = logEx && logEx.status === 'perlu_perhatian';
                        const iconCls = k.tipe === 'cek' ? 'fa-eye-dropper' : 'fa-flask';
                        let colCls = '';
                        if (done) colCls = 'bg-brand-green text-white border-brand-green';
                        else if (warn) colCls = 'bg-amber-500 text-white border-amber-600 ring-2 ring-amber-300';
                        else if (dayData.isPast && !dayData.isToday) colCls = 'bg-red-100 text-red-600 border-red-300';
                        else if (dayData.isToday) colCls = 'bg-amber-100 text-amber-700 border-amber-400 ring-1 ring-amber-400 animate-pulse';
                        else colCls = 'bg-blue-50 text-blue-600 border-blue-200';

                        indicatorHtml += `<div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full border flex items-center justify-center text-[10px] sm:text-xs shadow-2xs transition-transform hover:scale-110 ${colCls}" title="${k.judul} ${done ? '(Normal)' : (warn ? '(Perlu Koreksi)' : '')}"><i class="fa-solid ${iconCls}"></i></div>`;
                    });
                    indicator.innerHTML = indicatorHtml;
                    gsap.fromTo(indicator.children, { scale: 0 }, { scale: 1, duration: 0.4, stagger: 0.1, ease: "back.out(2)" });
                }
            } else {
                alert('Gagal menyimpan data.');
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi.');
            console.error(err);
        }
    };

    window.closeDayDetail = function() {
        const panel = document.getElementById('day-detail-panel');
        if (panel) {
            gsap.to(panel, { y: 20, opacity: 0, duration: 0.2, onComplete: () => panel.classList.add('hidden') });
        }
        document.querySelectorAll('[id^="cal-cell-"]').forEach(el => el.classList.remove('ring-4', 'ring-brand-greenpal'));
    };
}
