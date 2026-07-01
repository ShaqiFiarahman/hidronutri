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
        <!-- Tautan navigasi hierarki halaman -->
        <div class="flex items-center space-x-2 text-xs text-brand-gray font-medium mb-2 breadcrumb">
            <a href="/rekomendasi" class="hover:text-brand-black transition-colors">Rekomendasi</a>
            <span>/</span>
            <span>{{ $tanaman->nama }}</span>
            <span>/</span>
            <span class="text-brand-black font-semibold">Fase {{ ucwords(str_replace('_', ' ', $fase)) }}</span>
        </div>

        <!-- Ringkasan informasi utama di bagian atas halaman -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-brand-graylt pb-8 mt-6">
            <div class="space-y-2">
                @php
                    $activeSessions = \App\Models\SesiTanam::where('status', 'aktif')->with('tanaman')->get();
                @endphp
                @if($activeSessions->count() > 0)
                    <div class="relative inline-block text-left" id="tanamanDropdownContainer">
                        <!-- Tombol pemantik untuk membuka menu turun -->
                        <button type="button" onclick="toggleTanamanDropdown()" class="group inline-flex items-center gap-3.5 text-left focus:outline-none">
                            <h1 class="text-4xl font-extrabold tracking-tight text-brand-black group-hover:text-brand-green transition-colors duration-200 flex items-center gap-3">
                                <span>{{ $tanaman->emoji }} {{ $tanaman->nama }}</span>
                            </h1>
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-brand-graylt shadow-2xs group-hover:border-brand-green group-hover:text-brand-green transition-all duration-200">
                                <span class="text-xs font-bold text-brand-black group-hover:text-brand-green">{{ $activeSessions->count() }} Sesi</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-brand-gray group-hover:text-brand-green transition-transform duration-200" id="dropdownArrow"></i>
                            </div>
                        </button>

                        <!-- Menu pilihan turun untuk daftar sesi -->
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
                
                @if(in_array(strtolower($sistem), ['rakit apung', 'wick', 'rakit_apung']))
                    <div class="mt-4 border-l-4 border-amber-400 bg-amber-50 p-4 rounded-r-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-xs font-medium text-amber-800">Catatan Sistem: {{ strtoupper($sistem) }}</h3>
                                <p class="mt-1 text-xs text-amber-700">Pada instalasi yang airnya tidak bergerak seperti {{ $sistem }}, Anda perlu lebih teliti memantau kondisi air. Ketersediaan oksigen sangat krusial.</p>
                            </div>
                        </div>
                    </div>
                @elseif(strtolower($sistem) == 'nft' || strtolower($sistem) == 'dft')
                    <div class="mt-4 border-l-4 border-brand-green bg-brand-offwhite p-4 rounded-r-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-circle-info text-brand-green"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-xs font-medium text-brand-green">Catatan Sistem: {{ strtoupper($sistem) }}</h3>
                                <p class="mt-1 text-xs text-brand-gray">Untuk sistem bersirkulasi (khususnya NFT), harus ekstra hati-hati menjaga sirkulasi pompa air. Jika tidak tersirkulasi, tanaman bisa langsung mati.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="flex flex-wrap gap-3">

                <a href="/cek-kondisi"
                    class="bg-brand-black text-white rounded-xl px-6 py-3 text-sm font-semibold hover:bg-brand-green transition-colors duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-vial-virus text-xs"></i> Cek Kondisi Aktual
                </a>
            </div>
        </div>

        <!-- Kartu linimasa fase pertumbuhan secara horizontal -->
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
                    {{-- Garis latar belakang lintasan --}}
                    <div class="absolute left-6 right-6 top-1/2 -translate-y-1/2 h-1.5 bg-brand-graylt rounded-full z-0 overflow-hidden">
                        {{-- Garis hijau penanda progres bergeser secara dinamis --}}
                        <div class="h-full bg-brand-green rounded-full transition-all duration-700"
                             style="width: {{ $progressPct }}%;"></div>
                    </div>

                    {{-- Titik-titik penanda setiap fase --}}
                    @foreach($allFasesDB as $stepIdx => $stepFase)
                        @php
                            $isActive  = $stepFase === $fase;
                            $isSelesai = $stepIdx < $currentIdx;
                            $icon      = $faseIcons[$stepFase] ?? 'fa-circle';
                            $label     = ucwords(str_replace('_', ' ', $stepFase));
                        @endphp

                        <div class="relative z-10 flex flex-col items-center">
                            {{-- Lingkaran penanda titik fase --}}
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

                            {{-- Teks label dan lencana di bawah lingkaran --}}
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

        <!-- Tiga kartu metrik utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kartu nilai target pH -->
            <div
                class="bg-white border border-brand-graylt rounded-2xl p-6 hover:border-brand-green transition-colors duration-200 flex flex-col justify-between metric-card">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-3 block">Kadar pH</span>
                    <span class="block text-4xl font-black text-brand-black leading-none">
                        @if(isset($rekomendasi['ph_optimal_min']) && isset($rekomendasi['ph_optimal_max']))
                            <span class="metric-value">{{ number_format($rekomendasi['ph_optimal_min'], 1) }}</span>
                            - <span class="metric-value">{{ number_format($rekomendasi['ph_optimal_max'], 1) }}</span>
                        @else
                            <span class="metric-value">{{ number_format($rekomendasi['ph_min'], 1) }}</span>
                            @if($rekomendasi['ph_min'] != $rekomendasi['ph_max'])
                                - <span class="metric-value">{{ number_format($rekomendasi['ph_max'], 1) }}</span>
                            @endif
                        @endif
                    </span>
                </div>
                <div
                    class="mt-4 pt-4 border-t border-brand-graylt text-xs text-brand-green font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> 
                    @if(isset($rekomendasi['ph_optimal_min']))
                        Target Optimal (Batas Aman: {{ number_format($rekomendasi['ph_min'], 1) }} - {{ number_format($rekomendasi['ph_max'], 1) }})
                    @else
                        Target Ideal
                    @endif
                </div>
            </div>

            <!-- Kartu nilai target PPM -->
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


        </div>



        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Kartu perhitungan kalkulator dosis nutrisi -->
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

                        <div class="mt-4 bg-brand-offwhite p-3 rounded-lg border border-brand-graylt">
                            <p class="text-[10px] text-brand-gray font-medium leading-relaxed">
                                <i class="fa-solid fa-circle-info text-brand-green mr-1"></i> Catatan Pakar:
                                Rasio takaran Nutrisi A & B <strong>wajib selalu sama (1:1)</strong>. Gunakan alat ukur TDS/EC meter untuk hasil yang lebih presisi. Jika ada anomali pertumbuhan (seperti tanaman <em>stuck</em> atau warna daun tidak normal), Anda dapat menaikkan target PPM mendekati batas maksimal.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu panduan langkah demi langkah pelarutan nutrisi -->
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

                    <!-- Daftar urutan tahapan pelaksanaan -->
                    <div class="space-y-3.5">
                        <!-- Langkah pertama -->
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-brand-offwhite/60 border border-brand-graylt/70">
                            <div class="w-7 h-7 rounded-lg bg-brand-black text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                1
                            </div>
                            <div>
                                <span class="block font-semibold text-xs text-brand-black">Siapkan Air Bersih</span>
                                <span class="block text-[11px] text-brand-gray font-light mt-0.5">Siapkan wadah berisi air baku/bersih sesuai volume tangki yang ingin dilarutkan.</span>
                            </div>
                        </div>

                        <!-- Langkah kedua -->
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-brand-offwhite/60 border border-brand-graylt/70">
                            <div class="w-7 h-7 rounded-lg bg-brand-green text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                2
                            </div>
                            <div>
                                <span class="block font-semibold text-xs text-brand-black">Tuang Pekat Nutrisi A</span>
                                <span class="block text-[11px] text-brand-gray font-light mt-0.5">Masukkan takaran ml pekat A sesuai hasil kalkulasi di samping, lalu aduk hingga tercampur rata.</span>
                            </div>
                        </div>

                        <!-- Langkah ketiga -->
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-red-50/60 border border-red-200/80">
                            <div class="w-7 h-7 rounded-lg bg-red-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-xs text-red-900">Jangan Campur A & B Langsung!</span>
                                <span class="block text-[11px] text-red-700 font-light mt-0.5">Dilarang mencampur pekat A dan B tanpa air karena akan menghasilkan endapan kalsium sulfat yang tidak bisa diserap tanaman.</span>
                            </div>
                        </div>

                        <!-- Langkah keempat -->
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

    <x-kalender-bulanan 
        :progressPersen="$progressPersen ?? null" 
        :tanaman="$tanaman" 
        :usiaHari="$usiaHari" 
        :fase="$fase" 
        :estimasiPindahFase="$estimasiPindahFase ?? null" 
        :durasiTotal="$durasiTotal ?? null" 
        :kalenderBulan="$kalenderBulan" 
        :faseBerikutnya="$faseBerikutnya ?? null" 
        :sesiAktif="$sesiAktif ?? null" 
    />
    <script>
    window.hasilData = {
        kalenderData: @json($kalenderBulan ?? []),
        sesiAktifId: {{ $sesiAktif ? $sesiAktif->id : 'null' }},
        csrfToken: '{{ csrf_token() }}',
        dosisA: parseFloat("{{ $rekomendasi['dosis_a'] ?? 0 }}"),
        dosisB: parseFloat("{{ $rekomendasi['dosis_b'] ?? 0 }}")
    };
    </script>
@endsection