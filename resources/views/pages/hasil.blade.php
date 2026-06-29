@extends('layouts.app')

@section('title', 'Hasil Rekomendasi - HidroNutri')

@section('content')
<style>
    #tanamanDropdownMenu {
        transform: translateY(-8px) scale(0.98);
        opacity: 0;
        transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }
    #tanamanDropdownMenu.open {
        transform: translateY(0) scale(1);
        opacity: 1;
        pointer-events: all;
    }
</style>
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
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-brand-graylt pb-8 mt-6">
            <div class="space-y-2">
                @php
                    $activeSessions = \App\Models\SesiTanam::where('status', 'aktif')->with('tanaman')->get();
                @endphp
                @if($activeSessions->count() > 0)
                    <div class="relative inline-block text-left" id="tanamanDropdownContainer">
                        <!-- Trigger Button -->
                        <button type="button" onclick="toggleTanamanDropdown()" class="group inline-flex items-center gap-3.5 text-left focus:outline-none">
                            <h1 class="text-4xl font-extrabold tracking-tight text-brand-black group-hover:text-brand-green transition-colors duration-200 flex items-center gap-3">
                                <span>{{ $tanaman->emoji }} {{ $tanaman->nama }}</span>
                            </h1>
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-brand-graylt shadow-2xs group-hover:border-brand-green group-hover:text-brand-green transition-all duration-200">
                                <span class="text-xs font-bold text-brand-black group-hover:text-brand-green">{{ $activeSessions->count() }} Sesi</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-brand-gray group-hover:text-brand-green transition-transform duration-200" id="dropdownArrow"></i>
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="tanamanDropdownMenu" class="absolute left-0 mt-3 w-80 z-50">
                            <div class="bg-white rounded-2xl border border-brand-graylt shadow-xl overflow-hidden p-2 ring-1 ring-black/5">
                                <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-brand-gray border-b border-brand-graylt/60 mb-1 flex items-center justify-between">
                                    <span>Pilih Sesi Tanam Aktif</span>
                                    <span class="bg-brand-grayultra text-brand-black px-2 py-0.5 rounded-md font-extrabold text-[10px]">{{ $activeSessions->count() }}</span>
                                </div>
                                <div class="max-h-60 overflow-y-auto space-y-1">
                                    @foreach($activeSessions as $s)
                                        @php
                                            $isCurrent = ($s->tanaman_id == $tanaman->id && $s->sistem_hidroponik == $sistem);
                                            $usiaItem = \Carbon\Carbon::parse($s->tanggal_mulai)->diffInDays(\Carbon\Carbon::today());
                                        @endphp
                                        <a href="/hasil?sesi_id={{ $s->id }}" class="flex items-center justify-between p-2.5 rounded-xl transition-all duration-150 {{ $isCurrent ? 'bg-brand-greenpal/50 border border-brand-green/30 text-brand-black font-bold' : 'hover:bg-brand-grayultra text-brand-gray hover:text-brand-black' }}">
                                            <div class="flex items-center gap-3">
                                                <span class="text-2xl w-10 h-10 rounded-xl bg-brand-offwhite border border-brand-graylt/60 flex items-center justify-center shadow-2xs flex-shrink-0">{{ $s->tanaman->emoji }}</span>
                                                <div>
                                                    <div class="text-sm font-bold text-brand-black">{{ $s->tanaman->nama }}</div>
                                                    <div class="text-xs text-brand-gray font-normal flex items-center gap-1.5 mt-0.5">
                                                        <span class="uppercase font-semibold text-brand-black/80">{{ $s->sistem_hidroponik }}</span>
                                                        <span>•</span>
                                                        <span>{{ $usiaItem }} hari</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($isCurrent)
                                                <span class="flex items-center gap-1 text-[10px] font-bold text-brand-green bg-white px-2 py-1 rounded-lg border border-brand-green/20 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-green animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function toggleTanamanDropdown() {
                            const menu   = document.getElementById('tanamanDropdownMenu');
                            const arrow  = document.getElementById('dropdownArrow');
                            const isOpen = menu.classList.contains('open');

                            if (isOpen) {
                                menu.classList.remove('open');
                                arrow.style.transform = 'rotate(0deg)';
                            } else {
                                menu.classList.add('open');
                                arrow.style.transform = 'rotate(180deg)';
                            }
                        }

                        window.addEventListener('click', function(e) {
                            const container = document.getElementById('tanamanDropdownContainer');
                            if (container && !container.contains(e.target)) {
                                const menu  = document.getElementById('tanamanDropdownMenu');
                                const arrow = document.getElementById('dropdownArrow');
                                if (menu && menu.classList.contains('open')) {
                                    menu.classList.remove('open');
                                    arrow.style.transform = 'rotate(0deg)';
                                }
                            }
                        });
                    </script>
                @else
                    <h1 class="text-4xl font-extrabold tracking-tight text-brand-black">
                        {{ $tanaman->emoji }} {{ $tanaman->nama }}
                    </h1>
                @endif
                <p class="text-brand-gray text-sm font-light mt-3">
                    Usia: <span class="font-semibold text-brand-black">{{ $usiaHari }} Hari</span> •
                    Metode: <span class="font-semibold text-brand-black uppercase">{{ $sistem }}</span> • Kategori: Tanaman
                    Sayur {{ ucwords($tanaman->kategori) }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">

                <a href="/cek-kondisi"
                    class="bg-brand-black text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-brand-green transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-vial-virus text-xs"></i> Cek Kondisi Aktual
                </a>
            </div>
        </div>

        <!-- Phase Timeline: Horizontal Modern Card -->
        <div class="bg-white border border-brand-graylt rounded-3xl p-6 md:p-8 shadow-sm mt-8 space-y-6">
            <div class="flex items-center justify-between border-b border-brand-graylt pb-4">
                <div class="flex items-center">
                    <span class="w-8 h-8 rounded-xl bg-brand-greenpal text-brand-green text-sm font-bold flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fa-solid fa-circle-nodes"></i>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-brand-black">Timeline Fase Pertumbuhan</h2>
                        <p class="text-xs text-brand-gray">Pantau tahapan nutrisi tanaman dari awal hingga panen</p>
                    </div>
                </div>
            </div>

            @php
                $allFasesDB = \App\Models\RuleNutrisi::where('tanaman_id', $tanaman->id)
                    ->pluck('fase')
                    ->toArray();

                $faseIcons = [
                    'semai'          => 'fa-seedling',
                    'vegetatif_awal' => 'fa-leaf',
                    'vegetatif_akhir'=> 'fa-plant-wilt',
                    'panen'          => 'fa-basket-shopping',
                    'vegetatif'      => 'fa-leaf',
                    'pembungaan'     => 'fa-sun',
                    'pembuahan'      => 'fa-apple-whole',
                    'pembesaran'     => 'fa-expand',
                    'transisi'       => 'fa-arrows-turn-right',
                    'pematangan'     => 'fa-hourglass-half',
                ];

                $currentIdx  = array_search($fase, $allFasesDB);
                $totalFases  = count($allFasesDB);
                $progressPct = $totalFases > 1
                    ? round(($currentIdx / ($totalFases - 1)) * 100)
                    : 100;
            @endphp

            <div class="pt-6 pb-16 px-4 md:px-8">
                <div class="relative flex justify-between items-center w-full">
                    {{-- Track background garis --}}
                    <div class="absolute left-6 right-6 top-1/2 -translate-y-1/2 h-1.5 bg-brand-graylt rounded-full z-0 overflow-hidden">
                        {{-- Track progress garis hijau --}}
                        <div class="h-full bg-brand-green rounded-full transition-all duration-700"
                             style="width: {{ $progressPct }}%;"></div>
                    </div>

                    {{-- Node-node fase --}}
                    @foreach($allFasesDB as $stepIdx => $stepFase)
                        @php
                            $isActive  = $stepFase === $fase;
                            $isSelesai = $stepIdx < $currentIdx;
                            $icon      = $faseIcons[$stepFase] ?? 'fa-circle';
                            $label     = ucwords(str_replace('_', ' ', $stepFase));
                        @endphp

                        <div class="relative z-10 flex flex-col items-center">
                            {{-- Lingkaran Node --}}
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-base transition-all duration-300 shadow-sm
                                @if($isActive)
                                    bg-brand-green text-white ring-8 ring-brand-greenpal scale-110 shadow-md
                                @elseif($isSelesai)
                                    bg-brand-green text-white
                                @else
                                    bg-white border-2 border-brand-graylt text-brand-gray
                                @endif
                            ">
                                @if($isSelesai)
                                    <i class="fa-solid fa-check text-sm"></i>
                                @else
                                    <i class="fa-solid {{ $icon }} text-sm"></i>
                                @endif
                            </div>

                            {{-- Label & Badge di bawah lingkaran --}}
                            <div class="absolute top-16 left-1/2 -translate-x-1/2 w-28 md:w-36 flex flex-col items-center gap-1">
                                <span class="text-xs md:text-sm font-bold text-center leading-tight
                                    {{ $isActive ? 'text-brand-green' : ($isSelesai ? 'text-brand-black' : 'text-brand-gray') }}">
                                    {{ $label }}
                                </span>

                                @if($isActive)
                                    <span class="bg-brand-green text-white text-[9px] px-2.5 py-0.5 rounded-full font-extrabold uppercase tracking-widest shadow-sm animate-pulse">
                                        Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 3 Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- pH Metric -->
            <div
                class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-3 block">Kadar pH</span>
                    <span class="block text-4xl font-black text-brand-black leading-none">
                        <span class="metric-value">{{ number_format($rekomendasi['ph_min'], 1) }}</span>
                        @if($rekomendasi['ph_min'] != $rekomendasi['ph_max'])
                            - <span class="metric-value">{{ number_format($rekomendasi['ph_max'], 1) }}</span>
                        @endif
                    </span>
                </div>
                <div
                    class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> Target Ideal
                </div>
            </div>

            <!-- PPM Metric -->
            <div
                class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-3 block">Nilai
                        PPM</span>
                    <span class="block text-4xl font-black text-brand-black leading-none font-sans">
                        <span class="metric-value">{{ $rekomendasi['ppm_min'] }}</span>
                        @if($rekomendasi['ppm_min'] != $rekomendasi['ppm_max'])
                            - <span class="metric-value">{{ $rekomendasi['ppm_max'] }}</span>
                        @endif
                    </span>
                    <span class="text-xs text-brand-gray block mt-1">PPM</span>
                </div>
                <div
                    class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> Target Kepekatan
                </div>
            </div>

            <!-- Suhu Air Metric -->
            <div
                class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
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
                <div
                    class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
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
            <div
                class="bg-white border border-brand-graylt rounded-2xl overflow-hidden flex flex-col justify-between formula-card">
                <div>
                    <div class="bg-brand-black px-6 py-4 flex items-center justify-between">
                        <span class="text-sm font-semibold text-white">Kalkulator Dosis AB Mix</span>
                        <span class="text-xs text-white/60">Anjuran: A = {{ $rekomendasi['dosis_a'] }} ml, B =
                            {{ $rekomendasi['dosis_b'] }} ml per Liter</span>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="bg-brand-offwhite p-4 rounded-xl border border-brand-graylt/50">
                            <label for="volume-air"
                                class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">
                                Volume Air Wadah Penampung:
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="number" id="volume-air" min="1" value="10"
                                    class="w-full bg-white border border-brand-graylt rounded-xl py-2 px-3 focus:border-brand-green ring-2 ring-brand-greenpal outline-none font-bold text-brand-black text-lg transition-all duration-200">
                                <span class="font-bold text-brand-black text-lg">Liter</span>
                            </div>
                        </div>

                        <div class="divide-y divide-brand-graylt">
                            <div
                                class="flex justify-between items-center py-4 hover:bg-brand-offwhite px-4 rounded-xl transition-all duration-150">
                                <span class="text-sm font-medium text-brand-black">Kebutuhan Nutrisi A</span>
                                <span class="text-base font-bold text-brand-green" id="kalkulasi-a">
                                    {{ $rekomendasi['dosis_a'] * 10 }} ml
                                </span>
                            </div>
                            <div
                                class="flex justify-between items-center py-4 hover:bg-brand-offwhite px-4 rounded-xl transition-all duration-150">
                                <span class="text-sm font-medium text-brand-black">Kebutuhan Nutrisi B</span>
                                <span class="text-base font-bold text-brand-green" id="kalkulasi-b">
                                    {{ $rekomendasi['dosis_b'] * 10 }} ml
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Pencampuran AB Mix Card -->
            <div class="bg-white border border-brand-graylt rounded-2xl p-6 flex flex-col justify-between jadwal-card">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-brand-black flex items-center gap-2">
                            <i class="fa-solid fa-flask-vial text-brand-green"></i> Panduan Melarutkan AB Mix
                        </h3>
                    </div>
                    <p class="text-xs text-brand-gray mb-5 leading-relaxed font-light">
                        Ikuti langkah pencampuran nutrisi yang benar agar pekat A dan B tidak menggumpal (kristalisasi) saat dilarutkan ke dalam air.
                    </p>

                    <!-- Steps List -->
                    <div class="space-y-3.5">
                        <!-- Step 1 -->
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-brand-offwhite/60 border border-brand-graylt/70">
                            <div class="w-7 h-7 rounded-lg bg-brand-black text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                1
                            </div>
                            <div>
                                <span class="block font-semibold text-xs text-brand-black">Siapkan Air Bersih</span>
                                <span class="block text-[11px] text-brand-gray font-light mt-0.5">Siapkan wadah berisi air baku/bersih sesuai volume tangki yang ingin dilarutkan.</span>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-brand-offwhite/60 border border-brand-graylt/70">
                            <div class="w-7 h-7 rounded-lg bg-brand-green text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                2
                            </div>
                            <div>
                                <span class="block font-semibold text-xs text-brand-black">Tuang Pekat Nutrisi A</span>
                                <span class="block text-[11px] text-brand-gray font-light mt-0.5">Masukkan takaran ml pekat A sesuai hasil kalkulasi di samping, lalu aduk hingga tercampur rata.</span>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-red-50/60 border border-red-200/80">
                            <div class="w-7 h-7 rounded-lg bg-red-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-xs text-red-900">Jangan Campur A & B Langsung!</span>
                                <span class="block text-[11px] text-red-700 font-light mt-0.5">Dilarang mencampur pekat A dan B tanpa air karena akan menghasilkan endapan kalsium sulfat yang tidak bisa diserap tanaman.</span>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-brand-offwhite/60 border border-brand-graylt/70">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                3
                            </div>
                            <div>
                                <span class="block font-semibold text-xs text-brand-black">Tuang Pekat Nutrisi B</span>
                                <span class="block text-[11px] text-brand-gray font-light mt-0.5">Setelah air dan Nutrisi A merata, masukkan takaran ml pekat B lalu aduk kembali hingga sempurna.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Unified Jadwal & Progress Section -->
    <div id="jadwal" class="mt-12 space-y-8 pt-8 border-t border-brand-graylt/80">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-brand-black flex items-center gap-2.5">
                    <i class="fa-solid fa-calendar-check text-brand-green"></i> Jadwal & Progress Pemeliharaan
                </h2>
                <p class="text-xs text-brand-gray mt-1">Pantau siklus pertumbuhan dan agenda pemeliharaan rutin otomatis tanaman Anda</p>
            </div>
        </div>

        <!-- Active Session Progress Panel -->
        @if(isset($progressPersen))
        <div class="bg-white border border-brand-graylt rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xs progress-card">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <span class="text-4xl bg-brand-offwhite w-16 h-16 rounded-2xl flex items-center justify-center border border-brand-graylt/50">
                        {{ $tanaman->emoji }}
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xl text-brand-black">{{ $tanaman->nama }}</span>
                        </div>
                        <span class="text-xs text-brand-gray block mt-0.5 font-light">
                            Hari Ke-<strong>{{ $usiaHari }}</strong> sejak ditanam
                        </span>
                    </div>
                </div>

                <div class="text-left md:text-right space-y-1">
                    <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider block">Fase Saat Ini</span>
                    <div class="flex items-center gap-2 md:justify-end">
                        <span class="bg-brand-black text-white font-semibold text-xs px-3.5 py-1 rounded-full uppercase tracking-wide">
                            {{ str_replace('_', ' ', $fase) }}
                        </span>
                    </div>
                    @if(!empty($estimasiPindahFase))
                    <span class="text-xs text-brand-amber bg-amber-50 px-2.5 py-0.5 rounded-full border border-brand-amber/20 inline-block font-medium mt-1">
                        <i class="fa-solid fa-circle-notch animate-spin mr-1 text-[10px]"></i> {{ $estimasiPindahFase }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="w-full bg-brand-graylt rounded-full h-2.5 overflow-hidden">
                    <div class="bg-brand-green h-full rounded-full transition-all duration-700 ease-out progress-fill" style="width: {{ $progressPersen }}%"></div>
                </div>
                <div class="flex justify-between items-center text-xs text-brand-gray">
                    <span class="font-medium">Kemajuan Siklus Tanam</span>
                    <span class="font-semibold text-brand-black">{{ number_format($progressPersen, 0) }}% (Estimasi {{ max(0, ($durasiTotal ?? 35) - $usiaHari) }} Hari Sisa)</span>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Interactive Calendar (Col Span 2) -->
            <div class="lg:col-span-2 space-y-6" id="calendar-container">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-brand-black flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-brand-green"></i> Kalender Pemeliharaan
                    </h3>
                    <div class="flex items-center gap-4 text-sm font-semibold">
                        <span class="text-brand-black">{{ \Carbon\Carbon::today()->translatedFormat('F Y') }}</span>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="bg-white rounded-2xl border border-brand-graylt overflow-hidden shadow-2xs">
                    <div class="grid grid-cols-7 border-b border-brand-graylt bg-brand-offwhite/50 text-center text-[10px] sm:text-xs font-bold text-brand-gray py-3 uppercase tracking-wider">
                        <div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div><div>Min</div>
                    </div>
                    <div class="grid grid-cols-7 auto-rows-[60px] sm:auto-rows-[80px] text-sm">
                        @foreach($kalenderBulan as $day)
                            @php
                                $hasTask = count($day['kegiatan']) > 0;
                                $isAllDone = false;
                                if ($hasTask) {
                                    $isAllDone = true;
                                    foreach($day['kegiatan'] as $k) {
                                        $logExists = collect($day['logs'])->where('tipe', $k['tipe'])->first();
                                        if (!$logExists || $logExists->status !== 'selesai') {
                                            $isAllDone = false;
                                            break;
                                        }
                                    }
                                }
                                $cellBg = $day['isToday'] ? 'bg-brand-greenpal/20 ring-inset ring-2 ring-brand-green' : '';
                                if ($hasTask && $day['isPast'] && !$isAllDone && !$day['isToday']) {
                                    $cellBg = 'bg-red-50/30 ring-inset ring-1 ring-red-200';
                                }
                            @endphp
                            <div class="border-b border-r border-brand-graylt/50 p-1.5 sm:p-2 flex flex-col justify-between relative cursor-pointer hover:bg-brand-offwhite transition-colors {{ $day['isCurrentMonth'] ? 'text-brand-black' : 'text-brand-graylt' }} {{ $cellBg }}" 
                                 onclick="openDayDetail('{{ $day['date'] }}')" id="cal-cell-{{ $day['date'] }}">
                                
                                <span class="block text-right font-medium text-xs sm:text-sm {{ $day['isToday'] ? 'text-brand-green font-bold' : '' }}">
                                    {{ $day['day'] }}
                                </span>
                                
                                @if($hasTask)
                                    <div class="flex items-center justify-center gap-1 sm:gap-1.5 mb-1 flex-wrap" id="cal-indicator-{{ $day['date'] }}">
                                        @foreach($day['kegiatan'] as $k)
                                            @php
                                                $logExists = collect($day['logs'])->where('tipe', $k['tipe'])->first();
                                                $isDone = $logExists && $logExists->status === 'selesai';
                                                $isWarning = $logExists && $logExists->status === 'perlu_perhatian';
                                                $iconClass = $k['tipe'] === 'cek' ? 'fa-eye-dropper' : 'fa-flask';
                                                
                                                if ($isDone) {
                                                    $colorClass = 'bg-brand-green text-white border-brand-green';
                                                } elseif ($isWarning) {
                                                    $colorClass = 'bg-amber-500 text-white border-amber-600 ring-2 ring-amber-300';
                                                } elseif ($day['isPast'] && !$day['isToday']) {
                                                    $colorClass = 'bg-red-100 text-red-600 border-red-300';
                                                } elseif ($day['isToday']) {
                                                    $colorClass = 'bg-amber-100 text-amber-700 border-amber-400 ring-1 ring-amber-400 animate-pulse';
                                                } else {
                                                    $colorClass = 'bg-blue-50 text-blue-600 border-blue-200';
                                                }
                                            @endphp
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full border flex items-center justify-center text-[10px] sm:text-xs shadow-2xs transition-transform hover:scale-110 {{ $colorClass }}" title="{{ $k['judul'] }} {{ $isDone ? '(Normal)' : ($isWarning ? '(Perlu Koreksi)' : '') }}">
                                                <i class="fa-solid {{ $iconClass }}"></i>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Day Detail Panel (Hidden by default, shown via JS) -->
                <div id="day-detail-panel" class="hidden bg-white p-6 rounded-2xl border border-brand-graylt shadow-2xs mt-4">
                    <div class="flex items-center justify-between mb-4 border-b border-brand-graylt pb-4">
                        <h4 class="font-bold text-brand-black flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-list text-brand-green"></i> Tugas pada <span id="detail-date-title"></span>
                        </h4>
                        <button onclick="closeDayDetail()" class="text-brand-gray hover:text-brand-black transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div id="detail-tasks-container" class="space-y-4">
                        <!-- Tasks injected via JS -->
                    </div>
                </div>
            </div>

            <!-- Notes & Context Info (Col Span 1) -->
            <div class="space-y-6">
                @if(!empty($faseBerikutnya))
                <!-- Info Fase Berikutnya -->
                <div class="border-l-4 border-brand-green bg-brand-offwhite p-6 rounded-r-2xl rounded-l-md space-y-4 next-phase-card">
                    <h3 class="font-bold text-base flex items-center gap-2 text-brand-green">
                        <i class="fa-solid fa-circle-info text-brand-green"></i> Persiapan Fase Berikutnya
                    </h3>
                    <div class="space-y-1">
                        <span class="text-xs text-brand-gray block">Fase Selanjutnya:</span>
                        <span class="text-lg font-extrabold text-brand-black block">Fase {{ $faseBerikutnya }}</span>
                    </div>
                    <p class="text-xs text-brand-gray leading-relaxed font-light">
                        {{ $catatanFaseBerikutnya }}
                    </p>
                </div>
                @endif

                @if(!empty($sesiAktif))
                <!-- Mark Harvest Card -->
                <div class="bg-red-50/50 p-6 rounded-2xl border border-red-100 space-y-4">
                    <h3 class="font-bold text-xs text-red-800 uppercase tracking-wider">
                        Siklus Penanaman Selesai
                    </h3>
                    <p class="text-xs text-brand-gray leading-relaxed font-light">
                        Jika Anda sudah melakukan pemanenan total tanaman ini, harap tandai penanaman ini sebagai telah dipanen untuk disimpan ke riwayat.
                    </p>
                    
                    <form action="/sesi-tanam/{{ $sesiAktif->id }}/panen" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan sesi tanam ini dan menandainya sebagai telah panen?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold text-xs py-3 rounded-xl block transition-colors duration-200">
                            <i class="fa-solid fa-basket-shopping mr-1 text-[10px]"></i> Tandai Telah Panen
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Vanilla JS calculator integration -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
        
        // Pass PHP data to JS (outside DOMContentLoaded so functions can access them easily)
        const kalenderData = @json($kalenderBulan ?? []);
        const sesiAktifId = {{ $sesiAktif ? $sesiAktif->id : 'null' }};
        const csrfToken = '{{ csrf_token() }}';

        function openDayDetail(dateStr) {
            const dayData = kalenderData.find(d => d.date === dateStr);
            if (!dayData) return;

            const panel = document.getElementById('day-detail-panel');
            const title = document.getElementById('detail-date-title');
            const container = document.getElementById('detail-tasks-container');

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

                    let cardBg = isDone ? 'bg-brand-greenpal/20 border-brand-green/30' : (isWarning ? 'bg-amber-50/70 border-amber-300' : 'bg-brand-offwhite');
                    let iconHtml = isDone ? '<i class="fa-solid fa-circle-check text-lg text-brand-green"></i>' : (isWarning ? '<i class="fa-solid fa-triangle-exclamation text-lg text-amber-600"></i>' : (keg.tipe === 'cek' ? '<i class="fa-solid fa-eye-dropper text-lg text-brand-gray"></i>' : '<i class="fa-solid fa-flask text-lg text-brand-gray"></i>'));

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

                        if (isWarning && logExists.catatan) {
                            html += `<div class="mt-3 bg-amber-100/90 border-l-4 border-amber-500 p-3.5 rounded-r-xl text-xs text-amber-950 space-y-1.5 shadow-2xs">
                                <div class="font-bold flex items-center gap-1.5 text-amber-900">
                                    <i class="fa-solid fa-stethoscope text-amber-600"></i> Panduan Tindakan Koreksi:
                                </div>
                                <div class="whitespace-pre-line text-[11px] leading-relaxed font-medium pl-1">${logExists.catatan}</div>
                            </div>`;
                        }

                        if (isToday && keg.tipe === 'cek') {
                            html += `<a href="/cek-kondisi" class="mt-3.5 inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-brand-greenpal/20 text-brand-green border border-brand-green/30 rounded-xl text-xs font-bold transition-all shadow-2xs group">
                                <i class="fa-solid fa-rotate transition-transform group-hover:rotate-180"></i>
                                <span>Update Pengukuran & Diagnosa Ulang di Halaman Cek Kondisi ➔</span>
                            </a>`;
                        }
                    } else if (isToday && keg.tipe === 'cek') {
                        html += `
                        <a href="/cek-kondisi" class="mt-4 block w-full bg-brand-green hover:bg-brand-green/90 text-white text-xs font-bold py-3 px-4 rounded-xl transition-all text-center shadow-sm hover:shadow group">
                            <span class="flex items-center justify-center gap-2">
                                <i class="fa-solid fa-stethoscope text-sm transition-transform group-hover:scale-110"></i>
                                <span>Lakukan Cek Kondisi Air & Diagnosa Sekarang</span>
                                <i class="fa-solid fa-arrow-right ml-1 transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </a>
                        `;
                    } else if (isToday && keg.tipe === 'isi_ulang') {
                        html += `
                        <form onsubmit="submitLog(event, '${keg.tipe}', '${dateStr}')" class="mt-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-3 px-4 rounded-xl transition-all text-center shadow-sm hover:shadow">
                                Tandai Sudah Diisi Ulang
                            </button>
                        </form>
                        `;
                    } else if (dayData.isPast) {
                        html += `<div class="mt-3 text-[10px] font-bold text-brand-amber bg-amber-50 inline-block px-2 py-1 rounded-md border border-amber-200">Terlewat</div>`;
                    } else {
                        html += `<div class="mt-3 text-[10px] font-bold text-brand-gray bg-brand-offwhite inline-block px-2 py-1 rounded-md border border-brand-graylt">Terjadwal</div>`;
                    }

                    html += `</div>`;
                    container.insertAdjacentHTML('beforeend', html);
                });
            }

            panel.classList.remove('hidden');
            gsap.fromTo(panel, { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.3 });
            
            // Highlight selected cell
            document.querySelectorAll('[id^="cal-cell-"]').forEach(el => el.classList.remove('ring-4', 'ring-brand-green/30'));
            const activeCell = document.getElementById(`cal-cell-${dateStr}`);
            if (activeCell) activeCell.classList.add('ring-4', 'ring-brand-green/30');
        }

        function closeDayDetail() {
            const panel = document.getElementById('day-detail-panel');
            gsap.to(panel, { y: 10, opacity: 0, duration: 0.2, onComplete: () => panel.classList.add('hidden') });
            document.querySelectorAll('[id^="cal-cell-"]').forEach(el => el.classList.remove('ring-4', 'ring-brand-green/30'));
        }

        async function submitLog(e, tipe, dateStr) {
            e.preventDefault();
            
            let payload = {
                sesi_tanam_id: sesiAktifId,
                tanggal: dateStr,
                tipe: tipe,
                _token: csrfToken
            };

            if (tipe === 'cek') {
                payload.ph = document.getElementById(`input-ph-${tipe}`).value;
                payload.ppm = document.getElementById(`input-ppm-${tipe}`).value;
                payload.suhu = document.getElementById(`input-suhu-${tipe}`).value || null;
            }

            try {
                const response = await fetch('/log-perawatan', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const res = await response.json();
                
                if (res.success) {
                    // Update data in JS variable
                    const dayData = kalenderData.find(d => d.date === dateStr);
                    const existIdx = dayData.logs.findIndex(l => l.tipe === res.data.tipe);
                    if (existIdx >= 0) {
                        dayData.logs[existIdx] = res.data;
                    } else {
                        dayData.logs.push(res.data);
                    }
                    
                    // Re-render panel
                    openDayDetail(dateStr);

                    // Update kalender cell UI
                    const indicator = document.getElementById(`cal-indicator-${dateStr}`);
                    if (indicator) {
                        let indicatorHtml = '';
                        dayData.kegiatan.forEach(k => {
                            const logEx = dayData.logs.find(l => l.tipe === k.tipe);
                            const done = logEx && logEx.status === 'selesai';
                            const warn = logEx && logEx.status === 'perlu_perhatian';
                            const iconCls = k.tipe === 'cek' ? 'fa-eye-dropper' : 'fa-flask';
                            const colCls = done ? 'bg-brand-green text-white border-brand-green' : (warn ? 'bg-amber-500 text-white border-amber-600 ring-2 ring-amber-300' : 'bg-amber-100 text-amber-700 border-amber-400 ring-1 ring-amber-400 animate-pulse');
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
        }
    </script>
@endsection