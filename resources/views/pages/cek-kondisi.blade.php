@extends('layouts.app')

@section('title', 'Cek Kondisi Larutan - HidroNutri')

@section('content')
<div class="max-w-5xl mx-auto space-y-12 animate-fade-in page-wrapper">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-brand-graylt pb-8 cek-header">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-brand-black">
                Diagnosis Kondisi Larutan Nutrisi
            </h1>
            <p class="mt-2 text-sm text-brand-gray">
                Geser slider parameter aktual larutan Anda untuk melihat diagnosis kondisi air secara real-time.
            </p>
        </div>
    </div>

    @if(!$tanaman)
        <!-- No Context Alert -->
        <div class="bg-brand-offwhite border border-brand-graylt rounded-3xl p-8 text-center max-w-xl mx-auto space-y-6">
            <span class="text-5xl block">⚠️</span>
            <h3 class="text-lg font-bold text-brand-black">Konteks Tanaman Belum Dipilih</h3>
            <p class="text-sm text-brand-gray leading-relaxed font-light">
                Anda perlu memilih tanaman dan fase pertumbuhan terlebih dahulu agar sistem pakar memiliki basis pengetahuan (rules) untuk melakukan diagnosis.
            </p>
            <div class="flex justify-center gap-3">
                <a href="/rekomendasi" class="bg-brand-black text-white rounded-xl px-5 py-2.5 text-xs font-semibold hover:bg-brand-green transition-all duration-200">
                    Pilih Rekomendasi
                </a>
                <a href="/riwayat" class="bg-white text-brand-black border border-brand-graylt rounded-xl px-5 py-2.5 text-xs font-medium hover:border-brand-green transition-all duration-200">
                    Lihat Riwayat Sesi Tanam
                </a>
            </div>
        </div>
    @else
        <!-- Context Card -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 konteks-card">
            <div class="flex items-center space-x-4">
                <span class="text-4xl bg-brand-offwhite w-16 h-16 rounded-2xl flex items-center justify-center border border-brand-graylt/50">
                    {{ $tanaman->emoji }}
                </span>
                <div>
                    <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider block">Konteks Monitoring Aktual</span>
                    <span class="font-extrabold text-lg text-brand-black block">
                        {{ $tanaman->nama }} (Fase {{ ucwords(str_replace('_', ' ', $fase)) }})
                    </span>
                    @if($sesiTanam)
                        <span class="inline-flex items-center gap-1.5 text-xs text-brand-green bg-brand-greenpal px-2.5 py-0.5 rounded-full border border-brand-greenlt/20 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-green animate-pulse"></span>
                            Sesi Tanam Aktif #{{ $sesiTanam->id }} (Mulai: {{ \Carbon\Carbon::parse($sesiTanam->tanggal_mulai)->translatedFormat('d M Y') }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs text-brand-amber bg-amber-50 px-2.5 py-0.5 rounded-full border border-brand-amber/20 mt-1">
                            <i class="fa-solid fa-clock-rotate-left"></i> Mode Simulasi (Belum Disimpan)
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Target Badges -->
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="bg-brand-offwhite border border-brand-graylt text-brand-black px-3 py-1.5 rounded-xl font-medium">
                    Target pH: <strong class="text-brand-green">{{ number_format($rule->ph_min, 1) }} - {{ number_format($rule->ph_max, 1) }}</strong>
                </span>
                <span class="bg-brand-offwhite border border-brand-graylt text-brand-black px-3 py-1.5 rounded-xl font-medium">
                    Target EC: <strong class="text-brand-green">{{ number_format($rule->ec_min, 1) }} - {{ number_format($rule->ec_max, 1) }}</strong>
                </span>
                <span class="bg-brand-offwhite border border-brand-graylt text-brand-black px-3 py-1.5 rounded-xl font-medium">
                    Target PPM: <strong class="text-brand-green">{{ $rule->ppm_min }} - {{ $rule->ppm_max }}</strong>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Slider Form (Col Span 3) -->
            <form action="/cek-kondisi/diagnosa" method="POST" class="lg:col-span-3 bg-white p-6 sm:p-8 rounded-2xl border border-brand-graylt space-y-8">
                @csrf
                @if($sesiTanam)
                    <input type="hidden" name="sesi_tanam_id" value="{{ $sesiTanam->id }}">
                @else
                    <input type="hidden" name="tanaman_id" value="{{ $tanaman->id }}">
                    <input type="hidden" name="fase" value="{{ $fase }}">
                @endif

                <!-- 1. pH Slider -->
                <div class="space-y-3 slider-card">
                    <div class="flex items-center justify-between">
                        <label for="ph_aktual" class="font-bold text-brand-black flex items-center gap-1.5">
                            <i class="fa-solid fa-droplet text-brand-green"></i> Parameter pH Aktual
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-black text-brand-black" id="ph-val-display">6.2</span>
                            <span id="ph-status-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Normal
                            </span>
                        </div>
                    </div>
                    <div class="relative py-2">
                        <input type="range" name="ph_aktual" id="ph_aktual" min="0" max="14" step="0.1" 
                               value="{{ old('ph_aktual', session('ph_input', 6.2)) }}"
                               class="custom-range-slider">
                    </div>
                    <div class="flex justify-between text-[10px] text-brand-gray font-medium px-1">
                        <span>0.0 (Asam Kuat)</span>
                        <span class="text-brand-green font-semibold">Ideal: {{ number_format($rule->ph_min, 1) }} - {{ number_format($rule->ph_max, 1) }}</span>
                        <span>14.0 (Basa Kuat)</span>
                    </div>
                </div>

                <!-- 2. EC Slider -->
                <div class="space-y-3 slider-card">
                    <div class="flex items-center justify-between">
                        <label for="ec_aktual" class="font-bold text-brand-black flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-brand-green"></i> Parameter EC Aktual
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-black text-brand-black" id="ec-val-display">1.4</span>
                            <span id="ec-status-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Normal
                            </span>
                        </div>
                    </div>
                    <div class="relative py-2">
                        <input type="range" name="ec_aktual" id="ec_aktual" min="0" max="5.0" step="0.1" 
                               value="{{ old('ec_aktual', session('ec_input', 1.4)) }}"
                               class="custom-range-slider">
                    </div>
                    <div class="flex justify-between text-[10px] text-brand-gray font-medium px-1">
                        <span>0.0 mS/cm</span>
                        <span class="text-brand-green font-semibold">Ideal: {{ number_format($rule->ec_min, 1) }} - {{ number_format($rule->ec_max, 1) }}</span>
                        <span>5.0 mS/cm</span>
                    </div>
                </div>

                <!-- 3. PPM Slider -->
                <div class="space-y-3 slider-card">
                    <div class="flex items-center justify-between">
                        <label for="ppm_aktual" class="font-bold text-brand-black flex items-center gap-1.5">
                            <i class="fa-solid fa-gauge-high text-brand-green"></i> Parameter PPM Aktual
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-black text-brand-black" id="ppm-val-display">700</span>
                            <span id="ppm-status-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Normal
                            </span>
                        </div>
                    </div>
                    <div class="relative py-2">
                        <input type="range" name="ppm_aktual" id="ppm_aktual" min="0" max="2500" step="10" 
                               value="{{ old('ppm_aktual', session('ppm_input', 700)) }}"
                               class="custom-range-slider">
                    </div>
                    <div class="flex justify-between text-[10px] text-brand-gray font-medium px-1">
                        <span>0 PPM</span>
                        <span class="text-brand-green font-semibold">Ideal: {{ $rule->ppm_min }} - {{ $rule->ppm_max }} PPM</span>
                        <span>2500 PPM</span>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="btn-diagnosa w-full bg-brand-black hover:bg-brand-green text-white font-semibold px-6 py-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-stethoscope text-xs"></i> <span>Diagnosa Kondisi Aktual</span>
                    </button>
                </div>
            </form>

            <!-- Diagnosis Result Card (Col Span 2) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-brand-graylt h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-brand-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-prescription-bottle-medical text-brand-green"></i> Hasil Diagnosa Pakar
                        </h3>
                        
                        @if(!session('success_diagnosa'))
                            <div class="flex flex-col items-center justify-center text-center py-12 text-brand-gray">
                                <span class="text-4xl mb-3">📋</span>
                                <p class="text-sm font-semibold">Belum ada diagnosis.</p>
                                <p class="text-xs font-light mt-1 max-w-[200px]">Sesuaikan slider larutan Anda lalu klik tombol "Diagnosa Kondisi" untuk memproses.</p>
                            </div>
                        @else
                            @php
                                $diagnosaResult = session('diagnosa_result', []);
                            @endphp

                            @if(count($diagnosaResult) === 0)
                                <!-- Normal Result [DIAGNOSA CARD - NORMAL] -->
                                <div class="border border-brand-green rounded-xl overflow-hidden animate-fade-in diagnosa-result-card">
                                    <div class="bg-brand-greenpal px-4 py-3 text-sm font-bold text-brand-green flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-brand-green"></span> Semua Parameter Normal
                                    </div>
                                    <div class="bg-white p-4 text-xs text-brand-gray leading-relaxed font-light">
                                        Selamat! Larutan nutrisi Anda berada pada kisaran ideal untuk mendukung tumbuh kembang tanaman secara optimal.
                                    </div>
                                </div>
                            @else
                                <!-- Abnormal Results [DIAGNOSA CARD - WARNING & DANGER] -->
                                <div class="space-y-4">
                                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center animate-fade-in diagnosa-result-card">
                                        <span class="text-2xl block mb-1">⚠️</span>
                                        <h4 class="font-bold text-red-800 text-sm">Abnormalitas Terdeteksi</h4>
                                        <p class="text-[10px] text-red-600 font-light mt-0.5">Ditemukan {{ count($diagnosaResult) }} ketidaksesuaian parameter larutan.</p>
                                    </div>

                                    @foreach($diagnosaResult as $err)
                                        @php
                                            $isWarning = $err['parameter'] === 'pH';
                                        @endphp
                                        
                                        <div class="border {{ $isWarning ? 'border-brand-amber' : 'border-red-300' }} rounded-xl overflow-hidden animate-fade-in diagnosa-result-card">
                                            <!-- Card Header -->
                                            <div class="px-4 py-3 text-xs font-semibold {{ $isWarning ? 'bg-amber-50 text-brand-amber' : 'bg-red-50 text-red-600' }} flex justify-between items-center">
                                                <span class="uppercase font-bold tracking-wider">Parameter: {{ $err['parameter'] }}</span>
                                                <span class="uppercase font-black text-[10px]">{{ $err['kondisi'] }}</span>
                                            </div>
                                            <!-- Card Body -->
                                            <div class="bg-white p-4 space-y-3">
                                                <div class="text-xs text-brand-gray">
                                                    Nilai Aktual: <strong class="text-brand-black">{{ $err['nilai_aktual'] }}</strong> (Target ideal: {{ $err['nilai_target'] }})
                                                </div>
                                                <div class="bg-brand-offwhite p-3 rounded-xl border border-brand-graylt text-xs text-brand-black leading-relaxed">
                                                    <i class="fa-solid fa-hand-holding-medical mr-1.5 text-brand-green"></i>
                                                    <strong>Tindakan Korektif:</strong> {{ $err['tindakan'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>

                    @if($sesiTanam && session('success_diagnosa'))
                        <div class="mt-6 border-t border-brand-graylt pt-4 flex items-center justify-between text-[11px] text-brand-gray">
                            <span><i class="fa-solid fa-cloud-arrow-up mr-1 opacity-60"></i> Disimpan ke Riwayat Sesi</span>
                            <a href="/riwayat" class="font-bold text-brand-green hover:underline">Lihat Riwayat</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@if($tanaman)
<!-- Interactive Vanilla JS for Range Slider real-time rendering -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Range Target boundaries
        const phMin = parseFloat("{{ $rule->ph_min ?? 6.0 }}");
        const phMax = parseFloat("{{ $rule->ph_max ?? 6.5 }}");
        const ecMin = parseFloat("{{ $rule->ec_min ?? 1.2 }}");
        const ecMax = parseFloat("{{ $rule->ec_max ?? 1.6 }}");
        const ppmMin = parseInt("{{ $rule->ppm_min ?? 600 }}");
        const ppmMax = parseInt("{{ $rule->ppm_max ?? 800 }}");

        // Elements
        const phInput = document.getElementById('ph_aktual');
        const phDisplay = document.getElementById('ph-val-display');
        const phBadge = document.getElementById('ph-status-badge');

        const ecInput = document.getElementById('ec_aktual');
        const ecDisplay = document.getElementById('ec-val-display');
        const ecBadge = document.getElementById('ec-status-badge');

        const ppmInput = document.getElementById('ppm_aktual');
        const ppmDisplay = document.getElementById('ppm-val-display');
        const ppmBadge = document.getElementById('ppm-status-badge');

        function evaluateSlider(input, display, badge, min, max, label) {
            const val = parseFloat(input.value);
            display.textContent = val;

            // Reset classes
            badge.className = "text-[10px] font-bold px-2 py-0.5 rounded-full border transition-colors duration-200";

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

        // Bind events
        phInput.addEventListener('input', () => evaluateSlider(phInput, phDisplay, phBadge, phMin, phMax, 'pH'));
        ecInput.addEventListener('input', () => evaluateSlider(ecInput, ecDisplay, ecBadge, ecMin, ecMax, 'EC'));
        ppmInput.addEventListener('input', () => evaluateSlider(ppmInput, ppmDisplay, ppmBadge, ppmMin, ppmMax, 'PPM'));

        // Initialize values
        evaluateSlider(phInput, phDisplay, phBadge, phMin, phMax, 'pH');
        evaluateSlider(ecInput, ecDisplay, ecBadge, ecMin, ecMax, 'EC');
        evaluateSlider(ppmInput, ppmDisplay, ppmBadge, ppmMin, ppmMax, 'PPM');

        // Masuk halaman
        gsap.from('.cek-header', { y: -30, opacity: 0, duration: 0.5 });
        gsap.from('.konteks-card', { 
          scale: 0.95, opacity: 0, duration: 0.5, delay: 0.2,
          ease: 'back.out(1.2)' 
        });
        gsap.from('.slider-card', { 
          y: 30, opacity: 0, stagger: 0.15, duration: 0.5, delay: 0.3 
        });
        gsap.from('.btn-diagnosa', { 
          y: 20, opacity: 0, duration: 0.4, delay: 0.7 
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

        // Panggil animateDiagnosaResult() jika hasil diagnosa muncul
        animateDiagnosaResult();

        // Animasi slider thumb
        document.querySelectorAll('input[type=range]').forEach(slider => {
          slider.addEventListener('input', function() {
            gsap.to(this, { '--thumb-scale': 1.2, duration: 0.1 });
            setTimeout(() => gsap.to(this, { '--thumb-scale': 1, duration: 0.1 }), 150);
          });
        });
    });

    // Animasi status badge real-time (saat slider bergerak) - di luar karena dipanggil dinamis di helper luar
    function animateStatus(statusEl, isNormal) {
      gsap.from(statusEl, { 
        scale: 0.8, opacity: 0, duration: 0.3, 
        ease: 'back.out(1.4)' 
      });
    }
</script>
@endif
@endsection
