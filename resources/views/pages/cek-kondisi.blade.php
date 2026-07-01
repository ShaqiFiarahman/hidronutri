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
        <!-- peringatan jika pengguna belum memilih sesi tanam aktif -->
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
        <!-- kartu informasi konteks tanaman yang sedang diperiksa -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 konteks-card">
            <div class="flex items-center space-x-4">
                <span class="text-4xl bg-brand-offwhite w-16 h-16 rounded-2xl flex items-center justify-center border border-brand-graylt/50 flex-shrink-0">
                    {{ $tanaman->emoji }}
                </span>
                <div class="flex flex-col justify-center">
                    <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider block">Konteks Monitoring Aktual</span>
                    <span class="font-extrabold text-lg text-brand-black block">
                        {{ $tanaman->nama }} (Fase {{ ucwords(str_replace('_', ' ', $fase)) }})
                    </span>

                </div>
            </div>
            
            <!-- tetapkan variabel target nutrisi dari backend untuk validasi slider -->
            @php
                $ec_min = round($rule->ppm_min / 500, 2);
                $ec_max = round($rule->ppm_max / 500, 2);

            @endphp
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- formulir penggeser input untuk memasukkan kondisi air aktual -->
            <form action="/cek-kondisi/diagnosa" method="POST" class="lg:col-span-3 bg-white p-6 sm:p-8 rounded-2xl border border-brand-graylt space-y-8">
                @csrf
                @if($sesiTanam)
                    <input type="hidden" name="sesi_tanam_id" value="{{ $sesiTanam->id }}">
                @else
                    <input type="hidden" name="tanaman_id" value="{{ $tanaman->id }}">
                    <input type="hidden" name="fase" value="{{ $fase }}">
                @endif

                <!-- 1. pengatur nilai tingkat keasaman (pH) -->
                <div class="space-y-3 slider-card">
                    <div class="flex items-center justify-between">
                        <label for="ph_aktual" class="font-bold text-brand-black flex items-center gap-1.5">
                            <i class="fa-solid fa-droplet text-brand-green"></i> Parameter pH Aktual
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-black text-brand-black" id="ph-val-display">{{ old('ph_aktual', session('ph_input', round(($rule->ph_min + $rule->ph_max) / 2, 1))) }}</span>
                            <span id="ph-status-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Normal
                            </span>
                        </div>
                    </div>
                    <div class="relative py-2">
                        <input type="range" name="ph_aktual" id="ph_aktual" min="0" max="14" step="0.1" 
                               value="{{ old('ph_aktual', session('ph_input', round(($rule->ph_min + $rule->ph_max) / 2, 1))) }}"
                               class="custom-range-slider">
                    </div>
                    <div class="flex justify-between text-[10px] text-brand-gray font-medium px-1">
                        <span>0.0 (Asam Kuat)</span>
                        @if(isset($rule->ph_optimal_min))
                            <span class="text-brand-green font-semibold">Optimal: {{ number_format($rule->ph_optimal_min, 1) }} - {{ number_format($rule->ph_optimal_max, 1) }} (Aman: {{ number_format($rule->ph_min, 1) }} - {{ number_format($rule->ph_max, 1) }})</span>
                        @else
                            <span class="text-brand-green font-semibold">Ideal: {{ number_format($rule->ph_min, 1) }}{{ $rule->ph_min != $rule->ph_max ? ' - ' . number_format($rule->ph_max, 1) : '' }}</span>
                        @endif
                        <span>14.0 (Basa Kuat)</span>
                    </div>
                </div>

                <!-- 2. pengatur nilai konduktivitas listrik (EC) -->
                <div class="space-y-3 slider-card">
                    <div class="flex items-center justify-between">
                        <label for="ec_aktual" class="font-bold text-brand-black flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-brand-green"></i> Parameter EC Aktual
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-black text-brand-black" id="ec-val-display">{{ old('ec_aktual', session('ec_input', round((($rule->ppm_min + $rule->ppm_max) / 2) / 500, 1))) }}</span>
                            <span id="ec-status-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Normal
                            </span>
                        </div>
                    </div>
                    <div class="relative py-2">
                        <input type="range" name="ec_aktual" id="ec_aktual" min="0" max="5.0" step="0.1" 
                               value="{{ old('ec_aktual', session('ec_input', round((($rule->ppm_min + $rule->ppm_max) / 2) / 500, 1))) }}"
                               class="custom-range-slider">
                    </div>
                    <div class="flex justify-between text-[10px] text-brand-gray font-medium px-1">
                        <span>0.0 mS/cm</span>
                        <span class="text-brand-green font-semibold">Ideal: {{ number_format($ec_min, 1) }}{{ $ec_min != $ec_max ? ' - ' . number_format($ec_max, 1) : '' }}</span>
                        <span>5.0 mS/cm</span>
                    </div>
                </div>

                <!-- 3. pengatur nilai tingkat kelarutan padatan (PPM) -->
                <div class="space-y-3 slider-card">
                    <div class="flex items-center justify-between">
                        <label for="ppm_aktual" class="font-bold text-brand-black flex items-center gap-1.5">
                            <i class="fa-solid fa-gauge-high text-brand-green"></i> Parameter PPM Aktual
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-black text-brand-black" id="ppm-val-display">{{ old('ppm_aktual', session('ppm_input', intval(($rule->ppm_min + $rule->ppm_max) / 2))) }}</span>
                            <span id="ppm-status-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                Normal
                            </span>
                        </div>
                    </div>
                    <div class="relative py-2">
                        <input type="range" name="ppm_aktual" id="ppm_aktual" min="0" max="2500" step="10" 
                               value="{{ old('ppm_aktual', session('ppm_input', intval(($rule->ppm_min + $rule->ppm_max) / 2))) }}"
                               class="custom-range-slider">
                    </div>
                    <div class="flex justify-between text-[10px] text-brand-gray font-medium px-1">
                        <span>0 PPM</span>
                        <span class="text-brand-green font-semibold">Ideal: {{ $rule->ppm_min }}{{ $rule->ppm_min != $rule->ppm_max ? ' - ' . $rule->ppm_max : '' }} PPM</span>
                        <span>2500 PPM</span>
                    </div>
                </div>


                <div class="pt-4 mt-8">
                    <button type="submit" id="btn-submit-diagnosa"
                            class="w-full bg-brand-black hover:bg-brand-green text-white font-semibold px-6 py-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                        <i class="fa-solid fa-stethoscope text-xs"></i> <span>Mulai Diagnosa Kondisi Aktual</span>
                    </button>
                </div>
            </form>

            <!-- kartu hasil diagnosis dari sistem pakar -->
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
                                <!-- tampilan kartu jika seluruh parameter berada dalam rentang normal -->
                                <div class="border border-brand-green rounded-xl overflow-hidden animate-fade-in diagnosa-result-card">
                                    <div class="bg-brand-greenpal px-4 py-3 text-sm font-bold text-brand-green flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-brand-green"></span> Semua Parameter Normal
                                    </div>
                                    <div class="bg-white p-4 text-xs text-brand-gray leading-relaxed font-light">
                                        Selamat! Larutan nutrisi Anda berada pada kisaran ideal untuk mendukung tumbuh kembang tanaman secara optimal.
                                    </div>
                                </div>
                            @else
                                <!-- tampilan peringatan jika ditemukan kondisi yang tidak sesuai target -->
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
                                            <!-- bagian atas kartu peringatan -->
                                            <div class="px-4 py-3 text-xs font-semibold {{ $isWarning ? 'bg-amber-50 text-brand-amber' : 'bg-red-50 text-red-600' }} flex justify-between items-center">
                                                <span class="uppercase font-bold tracking-wider">Parameter: {{ $err['parameter'] }}</span>
                                                <span class="uppercase font-black text-[10px]">{{ $err['kondisi'] }}</span>
                                            </div>
                                            <!-- isi detail panduan penanganan -->
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
<!-- sediakan data variabel PHP untuk diproses oleh file JavaScript eksternal -->
<script>
    // Data untuk diolah oleh file JS eksternal (cek-kondisi.js)
    window.cekKondisiData = {
        phMin: parseFloat("{{ $rule->ph_min ?? 6.0 }}"),
        phMax: parseFloat("{{ $rule->ph_max ?? 6.5 }}"),
        ecMin: parseFloat("{{ $ec_min ?? 1.2 }}"),
        ecMax: parseFloat("{{ $ec_max ?? 1.6 }}"),
        ppmMin: parseInt("{{ $rule->ppm_min ?? 600 }}"),
        ppmMax: parseInt("{{ $rule->ppm_max ?? 800 }}")
    };
</script>
@endif
@endsection
