@extends('layouts.app')

@section('title', 'Hasil Rekomendasi - HidroNutri')

@section('content')
<div class="max-w-5xl mx-auto space-y-12 animate-fade-in page-wrapper">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-xs text-brand-gray font-medium mb-2 breadcrumb">
        <a href="/rekomendasi" class="hover:text-brand-black transition-colors">Rekomendasi</a>
        <span>/</span>
        <span>{{ $tanaman->nama }}</span>
        <span>/</span>
        <span class="text-brand-black font-semibold">Fase {{ ucwords(str_replace('_', ' ', $fase)) }}</span>
    </div>

    <!-- Header Summary -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-brand-graylt pb-8">
        <div class="space-y-2">
            <span class="bg-brand-greenpal text-brand-green font-bold text-xs px-3 py-1 rounded-full uppercase tracking-wider">
                Hasil Rekomendasi
            </span>
            <h1 class="text-4xl font-extrabold tracking-tight text-brand-black">
                {{ $tanaman->emoji }} {{ $tanaman->nama }}
            </h1>
            <p class="text-brand-gray text-sm font-light">
                Usia: <span class="font-semibold text-brand-black">{{ $usiaHari }} Hari</span> • 
                Metode: <span class="font-semibold text-brand-black uppercase">{{ $sistem }}</span> • Kategori: Tanaman Sayur {{ ucwords($tanaman->kategori) }}
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="/rekomendasi" class="bg-white text-brand-black border border-brand-graylt rounded-xl px-6 py-3 text-sm font-medium hover:border-brand-green hover:text-brand-green transition-colors duration-200 flex items-center gap-2">
                <i class="fa-solid fa-rotate-left text-xs"></i> Sesuaikan Input
            </a>
            <a href="/cek-kondisi" class="bg-brand-black text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-brand-green transition-colors duration-200 flex items-center gap-2">
                <i class="fa-solid fa-vial-virus text-xs"></i> Cek Kondisi Aktual
            </a>
        </div>
    </div>

    <!-- Phase Timeline (4 Steps) -->
    <div class="space-y-6">
        <div class="flex items-center">
            <span class="w-7 h-7 rounded-lg bg-brand-black text-white text-xs font-bold flex items-center justify-center mr-3 flex-shrink-0">
                <i class="fa-solid fa-circle-nodes"></i>
            </span>
            <h2 class="text-xl font-bold text-brand-black">Timeline Fase Pertumbuhan</h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-4 lg:grid-cols-5 gap-4 phase-timeline">
            @php
                // Ambil semua fase untuk tanaman ini dari rule_nutrisi
                $allFasesDB = \App\Models\RuleNutrisi::where('tanaman_id', $tanaman->id)
                    ->pluck('fase')
                    ->toArray();

                // Mapping icon per fase
                $faseIcons = [
                    'semai' => 'fa-seedling',
                    'vegetatif_awal' => 'fa-leaf',
                    'vegetatif_akhir' => 'fa-plant-wilt',
                    'panen' => 'fa-basket-shopping',
                    'vegetatif' => 'fa-leaf',
                    'pembungaan' => 'fa-sun',
                    'pembuahan' => 'fa-apple-whole',
                    'pembesaran' => 'fa-expand',
                    'transisi' => 'fa-arrows-turn-right',
                    'pematangan' => 'fa-hourglass-half',
                ];

                $currentIdx = array_search($fase, $allFasesDB);
            @endphp
            
            @foreach($allFasesDB as $stepIdx => $stepFase)
                @php
                    $isActive = $stepFase == $fase;
                    $isSelesai = $stepIdx < $currentIdx;
                    $stepIcon = $faseIcons[$stepFase] ?? 'fa-circle';
                    $stepLabel = ucwords(str_replace('_', ' ', $stepFase));
                @endphp
                <div class="flex sm:flex-col items-center gap-4 sm:gap-3 p-4 rounded-xl border transition-all duration-200 pt-step
                    {{ $isActive ? 'bg-brand-black text-white border-brand-black' : ($isSelesai ? 'bg-brand-greenpal text-brand-green border-brand-green' : 'bg-brand-offwhite text-brand-gray border-brand-graylt') }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center 
                        {{ $isActive ? 'bg-brand-green text-white' : ($isSelesai ? 'bg-white text-brand-green' : 'bg-white text-brand-gray') }}">
                        <i class="fa-solid {{ $stepIcon }} text-sm"></i>
                    </div>
                    <div class="text-left sm:text-center">
                        <span class="block font-bold text-sm">
                            {{ $stepLabel }}
                        </span>
                        @if($isActive)
                            <span class="inline-block mt-1 bg-brand-greenpal text-brand-green text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">
                                Aktif
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- pH Metric -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-3 block">Kadar pH</span>
                <span class="block text-4xl font-black text-brand-black leading-none">
                    <span class="metric-value">{{ number_format($rekomendasi['ph_min'], 1) }}</span>
                    @if($rekomendasi['ph_min'] != $rekomendasi['ph_max'])
                        - <span class="metric-value">{{ number_format($rekomendasi['ph_max'], 1) }}</span>
                    @endif
                </span>
            </div>
            <div class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> Target Ideal
            </div>
        </div>

        <!-- EC Metric -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-3 block">Nilai EC</span>
                <span class="block text-4xl font-black text-brand-black leading-none">
                    <span class="metric-value">{{ number_format($rekomendasi['ec_min'], 1) }}</span>
                    @if($rekomendasi['ec_min'] != $rekomendasi['ec_max'])
                        - <span class="metric-value">{{ number_format($rekomendasi['ec_max'], 1) }}</span>
                    @endif
                </span>
                <span class="text-xs text-brand-gray block mt-1">mS/cm</span>
            </div>
            <div class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> Target Konduktivitas
            </div>
        </div>

        <!-- PPM Metric -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-3 block">Nilai PPM</span>
                <span class="block text-4xl font-black text-brand-black leading-none font-sans">
                    <span class="metric-value">{{ $rekomendasi['ppm_min'] }}</span>
                    @if($rekomendasi['ppm_min'] != $rekomendasi['ppm_max'])
                        - <span class="metric-value">{{ $rekomendasi['ppm_max'] }}</span>
                    @endif
                </span>
                <span class="text-xs text-brand-gray block mt-1">PPM</span>
            </div>
            <div class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> Target Kepekatan
            </div>
        </div>

        <!-- Suhu Air Metric -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-3 block">Suhu Air</span>
                <span class="block text-4xl font-black text-brand-black leading-none">
                    <span class="metric-value">{{ $rekomendasi['suhu_min'] ?? 20 }}</span>
                    @if(($rekomendasi['suhu_min'] ?? 20) != ($rekomendasi['suhu_max'] ?? 28))
                        - <span class="metric-value">{{ $rekomendasi['suhu_max'] ?? 28 }}</span>
                    @endif
                </span>
                <span class="text-xs text-brand-gray block mt-1">Celcius (°C)</span>
            </div>
            <div class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> Rentang Suhu Ideal
            </div>
        </div>
    </div>

    <!-- Peringatan Box -->
    @if(!empty($rekomendasi['peringatan']))
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3 items-start warning-box">
            <div class="flex-shrink-0 text-brand-amber mt-0.5">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-brand-amber">Perhatian Khusus</h3>
                <p class="mt-1 text-sm text-amber-800 leading-relaxed font-medium">
                    "{{ $rekomendasi['peringatan'] }}"
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Dosis Nutrisi AB Mix Card (Formula Card) -->
        <div class="bg-white border border-brand-graylt rounded-2xl overflow-hidden flex flex-col justify-between formula-card">
            <div>
                <div class="bg-brand-black px-6 py-4 flex items-center justify-between">
                    <span class="text-sm font-semibold text-white">Kalkulator Dosis AB Mix</span>
                    <span class="text-xs text-white/60">Anjuran: A = {{ $rekomendasi['dosis_a'] }} ml, B = {{ $rekomendasi['dosis_b'] }} ml per Liter</span>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="bg-brand-offwhite p-4 rounded-xl border border-brand-graylt/50">
                        <label for="volume-air" class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">
                            Volume Air Wadah Penampung:
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="volume-air" min="1" value="10" 
                                   class="w-full bg-white border border-brand-graylt rounded-xl py-2 px-3 focus:border-brand-green ring-2 ring-brand-greenpal outline-none font-bold text-brand-black text-lg transition-all duration-200">
                            <span class="font-bold text-brand-black text-lg">Liter</span>
                        </div>
                    </div>

                    <div class="divide-y divide-brand-graylt">
                        <div class="flex justify-between items-center py-4 hover:bg-brand-offwhite px-4 rounded-xl transition-all duration-150">
                            <span class="text-sm font-medium text-brand-black">Kebutuhan Nutrisi A</span>
                            <span class="text-base font-bold text-brand-green" id="kalkulasi-a">
                                {{ $rekomendasi['dosis_a'] * 10 }} ml
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-4 hover:bg-brand-offwhite px-4 rounded-xl transition-all duration-150">
                            <span class="text-sm font-medium text-brand-black">Kebutuhan Nutrisi B</span>
                            <span class="text-base font-bold text-brand-green" id="kalkulasi-b">
                                {{ $rekomendasi['dosis_b'] * 10 }} ml
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PH Adjustment Tip -->
            <div class="p-6 bg-brand-offwhite border-t border-brand-graylt flex items-center space-x-3 text-xs text-brand-gray">
                <span class="text-lg">💡</span>
                <span>Gunakan <strong>pH Down</strong> secukupnya jika hasil ukur pH larutan lebih tinggi dari target ideal.</span>
            </div>
        </div>

        <!-- Jadwal Perawatan Card -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 flex flex-col justify-between jadwal-card">
            <div>
                <h3 class="text-base font-bold text-brand-black mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-brand-green"></i> Jadwal Pemeliharaan Rutin
                </h3>
                <p class="text-xs text-brand-gray mb-6 leading-relaxed">
                    Sistem merekomendasikan jadwal pengecekan dan penggantian air nutrisi berikut berdasarkan rules fase ini:
                </p>

                <!-- Jadwal Rows -->
                <div class="space-y-3">
                    <!-- Ganti Larutan -->
                    <div class="flex items-center justify-between p-4 rounded-xl hover:bg-brand-offwhite transition-colors duration-150 border border-brand-graylt">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-offwhite border border-brand-graylt flex items-center justify-center text-xs font-bold text-brand-gray">
                                <i class="fa-solid fa-dumpster-fire text-red-500"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-sm text-brand-black">Kuras & Ganti Air</span>
                                <span class="block text-[10px] text-brand-gray">Penggantian total larutan</span>
                            </div>
                        </div>
                        <span class="bg-brand-greenpal text-brand-green text-xs font-semibold px-3 py-1 rounded-full border border-brand-green/30">
                            Setiap {{ $rekomendasi['ganti_larutan'] }} Hari
                        </span>
                    </div>

                    <!-- Isi Ulang Air -->
                    <div class="flex items-center justify-between p-4 rounded-xl hover:bg-brand-offwhite transition-colors duration-150 border border-brand-graylt">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-offwhite border border-brand-graylt flex items-center justify-center text-xs font-bold text-brand-gray">
                                <i class="fa-solid fa-fill-drip text-blue-500"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-sm text-brand-black">Isi Ulang Nutrisi</span>
                                <span class="block text-[10px] text-brand-gray">Pemberian air & pupuk susulan</span>
                            </div>
                        </div>
                        <span class="bg-brand-greenpal text-brand-green text-xs font-semibold px-3 py-1 rounded-full border border-brand-green/30">
                            Setiap {{ $rekomendasi['isi_ulang'] ?? 2 }} Hari
                        </span>
                    </div>

                    <!-- Cek pH/EC -->
                    <div class="flex items-center justify-between p-4 rounded-xl hover:bg-brand-offwhite transition-colors duration-150 border border-brand-graylt">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-offwhite border border-brand-graylt flex items-center justify-center text-xs font-bold text-brand-gray">
                                <i class="fa-solid fa-magnifying-glass-chart text-amber-500"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-sm text-brand-black">Cek pH & EC Larutan</span>
                                <span class="block text-[10px] text-brand-gray">Monitoring rutin berkala</span>
                            </div>
                        </div>
                        <span class="bg-brand-greenpal text-brand-green text-xs font-semibold px-3 py-1 rounded-full border border-brand-green/30">
                            Setiap {{ $rekomendasi['cek_ph_ec'] ?? 1 }} Hari
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="/jadwal" class="text-sm font-semibold text-brand-green hover:text-brand-greenlt transition-colors flex items-center gap-1">
                    Lihat Selengkapnya <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Start Sesi Tanam Form -->
    <div class="bg-brand-offwhite border border-brand-graylt rounded-3xl p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2 max-w-xl">
            <h3 class="text-xl font-bold text-brand-black flex items-center gap-2">
                <span class="text-2xl">🚀</span> Mulai Sesi Monitoring Tanam Baru
            </h3>
            <p class="text-sm text-brand-gray leading-relaxed font-light">
                Simpan rekomendasi ini ke database sebagai **Sesi Tanam Aktif**. Kami akan membuatkan grafik pertumbuhan, mencatat riwayat diagnosa berkala, dan menampilkan pengingat jadwal harian untuk Anda.
            </p>
        </div>
        
        <form action="/sesi-tanam" method="POST" class="bg-white p-6 rounded-2xl border border-brand-graylt flex flex-col sm:flex-row items-end gap-4 w-full lg:w-auto">
            @csrf
            <input type="hidden" name="tanaman_id" value="{{ $tanaman->id }}">
            <input type="hidden" name="sistem_hidroponik" value="{{ $sistem }}">
            <input type="hidden" name="fase_saat_ini" value="{{ $fase }}">

            <div class="w-full sm:w-auto">
                <label for="tanggal_mulai" class="block text-xs font-bold text-brand-gray uppercase mb-2">Tanggal Mulai Tanam:</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $tanggalMulai }}" readonly
                       class="px-4 py-2 rounded-xl border border-brand-graylt bg-brand-offwhite text-sm text-brand-gray font-medium w-full cursor-not-allowed" title="Tanggal tanam disalin dari input Anda sebelumnya">
            </div>

            <button type="submit" 
                    class="w-full sm:w-auto bg-brand-black text-white rounded-xl px-8 py-3 text-sm font-semibold hover:bg-brand-green transition-colors duration-200 flex items-center justify-center gap-2 whitespace-nowrap">
                <span>Mulai Monitoring</span>
                <span>→</span>
            </button>
        </form>
    </div>
</div>

<!-- Vanilla JS calculator integration -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const volumeInput = document.getElementById('volume-air');
        const calcA = document.getElementById('kalkulasi-a');
        const calcB = document.getElementById('kalkulasi-b');
        
        const dosisA = parseFloat("{{ $rekomendasi['dosis_a'] }}");
        const dosisB = parseFloat("{{ $rekomendasi['dosis_b'] }}");

        function updateKalkulasi() {
            const volume = parseFloat(volumeInput.value) || 0;
            const resA = (dosisA * volume).toFixed(1);
            const resB = (dosisB * volume).toFixed(1);
            
            calcA.textContent = resA + " ml";
            calcB.textContent = resB + " ml";
        }

        volumeInput.addEventListener('input', updateKalkulasi);

        // Timeline masuk halaman hasil
        const tlHasil = gsap.timeline({ delay: 0.2 });

        tlHasil
          .from('.breadcrumb',    { y: -20, opacity: 0, duration: 0.4 })
          .from('.phase-timeline .pt-step', { 
            y: 20, opacity: 0, stagger: 0.12, duration: 0.4,
            ease: 'power2.out' }, '-=0.2')
          .from('.metric-card', { 
            y: 30, opacity: 0, scale: 0.9, stagger: 0.1, duration: 0.5,
            ease: 'back.out(1.4)' }, '-=0.2')
          .from('.formula-card', { 
            x: -30, opacity: 0, duration: 0.5 }, '-=0.3')
          .from('.jadwal-card', { 
            x: 30, opacity: 0, duration: 0.5 }, '-=0.5')
          .from('.warning-box', { 
            y: 20, opacity: 0, duration: 0.4 }, '-=0.2');

        // Animasi nilai metric (count up)
        document.querySelectorAll('.metric-value').forEach(el => {
          const val = parseFloat(el.textContent);
          if (!isNaN(val)) {
            const obj = { v: 0 };
            gsap.to(obj, {
              v: val, duration: 1.2, ease: 'power2.out', delay: 0.8,
              onUpdate: function() {
                el.textContent = obj.v.toFixed(1);
              }
            });
          }
        });
    });
</script>
@endsection
