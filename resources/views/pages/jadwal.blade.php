@extends('layouts.app')

@section('title', 'Jadwal Perawatan - HidroNutri')

@section('content')
<div class="max-w-5xl mx-auto space-y-12 animate-fade-in page-wrapper">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-brand-graylt pb-8 jadwal-header">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-brand-black">
                Jadwal Pemeliharaan & Monitoring
            </h1>
            <p class="mt-2 text-sm text-brand-gray">
                Jadwal tugas harian otomatis berdasarkan aturan (rules) sistem pakar fase pertumbuhan tanaman.
            </p>
        </div>
    </div>

    @if(!$sesi)
        <!-- No Active Session Placeholder -->
        <div class="bg-brand-offwhite border border-brand-graylt rounded-3xl p-8 sm:p-12 text-center max-w-xl mx-auto space-y-6">
            <span class="text-6xl block">📅</span>
            <div class="space-y-2">
                <h3 class="text-lg font-bold text-brand-black">Tidak Ada Sesi Tanam Aktif</h3>
                <p class="text-sm text-brand-gray leading-relaxed max-w-sm mx-auto font-light">
                    Sistem tidak dapat menghasilkan jadwal pemeliharaan harian dinamis karena Anda belum memulai sesi penanaman.
                </p>
            </div>
            <div class="flex justify-center pt-2">
                <a href="/rekomendasi" class="bg-brand-black hover:bg-brand-green text-white font-semibold px-8 py-3.5 rounded-xl transition-all duration-200 flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-xs"></i> <span>Dapatkan Rekomendasi & Mulai Tanam</span>
                </a>
            </div>
        </div>
    @else
        <!-- Active Session Status Panel -->
        <div class="bg-white border border-brand-graylt rounded-2xl p-6 sm:p-8 space-y-6 progress-card">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Sesi Context -->
                <div class="flex items-center space-x-4">
                    <span class="text-4xl bg-brand-offwhite w-16 h-16 rounded-2xl flex items-center justify-center border border-brand-graylt/50">
                        {{ $sesi->tanaman->emoji }}
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xl text-brand-black">{{ $sesi->tanaman->nama }}</span>
                            <span class="bg-brand-greenpal text-brand-green text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-brand-green/20 uppercase">
                                {{ $sesi->sistem_hidroponik }}
                            </span>
                        </div>
                        <span class="text-xs text-brand-gray block mt-0.5 font-light">
                            Hari Ke-<strong>{{ $usiaHari }}</strong> sejak ditanam • Mulai: {{ \Carbon\Carbon::parse($sesi->tanggal_mulai)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>

                <!-- Phase Badge -->
                <div class="text-left md:text-right space-y-1">
                    <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider block">Fase Saat Ini</span>
                    <div class="flex items-center gap-2 md:justify-end">
                        <span class="bg-brand-black text-white font-semibold text-xs px-3.5 py-1 rounded-full uppercase tracking-wide">
                            {{ str_replace('_', ' ', $sesi->fase_saat_ini) }}
                        </span>
                    </div>
                    <span class="text-xs text-brand-amber bg-amber-50 px-2.5 py-0.5 rounded-full border border-brand-amber/20 inline-block font-medium mt-1">
                        <i class="fa-solid fa-circle-notch animate-spin mr-1 text-[10px]"></i> {{ $estimasiPindahFase }}
                    </span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="w-full bg-brand-graylt rounded-full h-2">
                    <div class="bg-brand-green h-full rounded-full transition-all duration-700 ease-out progress-fill" style="width: {{ $progressPersen }}%"></div>
                </div>
                <div class="flex justify-between items-center text-xs text-brand-gray">
                    <span class="font-medium">Kemajuan Siklus Tanam</span>
                    <span class="font-semibold text-brand-black">{{ number_format($progressPersen, 0) }}% (Estimasi {{ max(0, $durasiTotal - $usiaHari) }} Hari Sisa)</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Weekly Schedule (Col Span 2) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-brand-black flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-brand-green"></i> Agenda Tugas Minggu Ini
                    </h2>
                    <span class="text-xs text-brand-gray font-light">Dimulai Hari Ini</span>
                </div>

                <div class="space-y-4">
                    @forelse($jadwalSeminggu as $index => $item)
                        @php
                            $isToday = $index === 0;
                        @endphp
                        <div class="bg-white p-5 rounded-2xl border {{ $isToday ? 'border-brand-green ring-2 ring-brand-greenpal/30' : 'border-brand-graylt/70' }} relative overflow-hidden flex gap-4 transition-all duration-150 hover:bg-brand-offwhite sched-row">
                            @if($isToday)
                                <div class="absolute top-0 right-0 bg-brand-green text-white font-bold text-[9px] px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                                    Hari Ini
                                </div>
                            @endif

                            <!-- Date Badge -->
                            <div class="flex-shrink-0 flex flex-col justify-center items-center {{ $isToday ? 'bg-brand-black text-white border-brand-black' : 'bg-brand-offwhite text-brand-gray border-brand-graylt' }} border w-16 h-16 rounded-xl text-center self-start justify-items-center">
                                <span class="text-[10px] font-extrabold uppercase leading-none block pt-1">{{ $item['hari'] }}</span>
                                <span class="text-xs font-bold block mt-1 leading-none">{{ str_replace(date(' Y'), '', $item['tanggal']) }}</span>
                            </div>

                            <!-- Tasks list -->
                            <div class="flex-grow space-y-3 pt-1">
                                @foreach($item['kegiatan'] as $keg)
                                    <div class="flex items-start space-x-3 text-xs leading-normal">
                                        @if($keg['tipe'] === 'cek')
                                            <span class="w-6 h-6 rounded-full bg-amber-50 text-brand-amber border border-brand-amber/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fa-solid fa-eye-dropper"></i>
                                            </span>
                                        @elseif($keg['tipe'] === 'isi_ulang')
                                            <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fa-solid fa-flask"></i>
                                            </span>
                                        @else
                                            <span class="w-6 h-6 rounded-full bg-red-50 text-red-600 border border-red-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </span>
                                        @endif
                                        <div>
                                            <h4 class="font-bold text-brand-black">{{ $keg['judul'] }}</h4>
                                            <p class="text-brand-gray font-light mt-0.5">{{ $keg['deskripsi'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-2xl text-center border border-brand-graylt text-brand-gray">
                            Tidak ada jadwal kegiatan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Notes & Context Info (Col Span 1) -->
            <div class="space-y-6">
                <!-- Info Fase Berikutnya (DIAGNOSA CARD style: Normal/Info style) -->
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

                <!-- Quick Action Diagnose Card -->
                <div class="bg-white p-6 rounded-2xl border border-brand-graylt space-y-4">
                    <h3 class="font-bold text-xs text-brand-black uppercase tracking-wider">
                        Diagnosis Cepat
                    </h3>
                    <p class="text-xs text-brand-gray leading-relaxed font-light">
                        Sudah mengukur pH, EC, atau PPM air tandon hidroponik Anda hari ini? Masukkan angkanya ke simulator untuk didiagnosa langsung.
                    </p>
                    <a href="/cek-kondisi" class="w-full text-center bg-white text-brand-black rounded-xl border border-brand-graylt px-4 py-3 text-xs font-semibold hover:border-brand-green hover:text-brand-green transition-colors duration-200 block">
                        Input Hasil Ukur Aktual
                    </a>
                </div>

                <!-- Mark Harvest Card -->
                <div class="bg-red-50/50 p-6 rounded-2xl border border-red-100 space-y-4">
                    <h3 class="font-bold text-xs text-red-800 uppercase tracking-wider">
                        Siklus Penanaman Selesai
                    </h3>
                    <p class="text-xs text-brand-gray leading-relaxed font-light">
                        Jika Anda sudah melakukan pemanenan total tanaman ini, harap tandai penanaman ini sebagai telah dipanen untuk disimpan ke riwayat.
                    </p>
                    
                    <form action="/sesi-tanam/{{ $sesi->id }}/panen" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan sesi tanam ini dan menandainya sebagai telah panen?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold text-xs py-3 rounded-xl block transition-colors duration-200">
                            <i class="fa-solid fa-basket-shopping mr-1 text-[10px]"></i> Tandai Telah Panen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tlJadwal = gsap.timeline({ delay: 0.15 });

        tlJadwal
          .from('.jadwal-header', { y: -20, opacity: 0, duration: 0.4 })
          .from('.progress-card', { 
            scale: 0.95, opacity: 0, duration: 0.5, 
            ease: 'back.out(1.2)' }, '-=0.1');

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
</script>
@endsection
