@extends('layouts.app')

@section('title', 'Riwayat Sesi Tanam - HidroNutri')

@section('content')
<div class="max-w-5xl mx-auto space-y-12 animate-fade-in page-wrapper">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-brand-graylt pb-8 riwayat-header">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-brand-black">
                Riwayat & Monitoring Sesi Tanam
            </h1>
            <p class="mt-2 text-sm text-brand-gray">
                Pantau sesi tanam aktif dan rekapitulasi penanaman hidroponik yang telah berhasil diselesaikan.
            </p>
        </div>
        <div class="flex justify-start md:justify-end">
            <a href="/rekomendasi" class="bg-brand-black text-white rounded-xl px-6 py-3.5 text-sm font-semibold hover:bg-brand-green transition-colors duration-200 flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i> <span>Sesi Tanam Baru</span>
            </a>
        </div>
    </div>

    <!-- Section 1: Aktif Sekarang -->
    <div class="space-y-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-brand-green flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brand-green animate-pulse"></span>
            Sedang Aktif Dipantau ({{ $sesiAktif->count() }})
        </h2>
        
        @if($sesiAktif->isEmpty())
            <div class="bg-brand-offwhite border border-brand-graylt rounded-3xl p-8 text-center max-w-xl mx-auto space-y-6">
                <span class="text-4xl">🪴</span>
                <p class="text-sm font-medium text-brand-black">Tidak ada sesi tanam yang aktif saat ini.</p>
                <p class="text-xs text-brand-gray font-light">Silakan buat sesi tanam baru dari halaman hasil rekomendasi untuk memulai pencatatan.</p>
                <a href="/rekomendasi" class="inline-block bg-white text-brand-black border border-brand-graylt rounded-xl px-4 py-2 text-xs font-semibold hover:border-brand-green transition-colors duration-200">
                    Mulai Tanam Sekarang
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 riwayat-list">
                @foreach($sesiAktif as $sesi)
                    <div class="bg-white border border-brand-graylt rounded-2xl p-6 flex flex-col justify-between space-y-6 hover:border-brand-green transition-all duration-200 riwayat-card">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="text-4xl bg-brand-offwhite w-14 h-14 rounded-2xl flex items-center justify-center border border-brand-graylt/50">
                                    {{ $sesi->tanaman->emoji }}
                                </span>
                                <div>
                                    <h3 class="font-bold text-base text-brand-black leading-tight">{{ $sesi->tanaman->nama }}</h3>
                                    <span class="text-[10px] text-brand-gray block mt-1 uppercase font-bold tracking-wider">{{ $sesi->sistem_hidroponik }}</span>
                                </div>
                            </div>
                            <span class="bg-brand-black text-white text-[10px] font-semibold px-2.5 py-0.5 rounded-full uppercase tracking-wide">
                                Fase {{ str_replace('_', ' ', $sesi->fase_saat_ini) }}
                            </span>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-4 text-xs bg-brand-offwhite p-4 rounded-xl border border-brand-graylt/75">
                            <div>
                                <span class="text-brand-gray font-light block">Tanggal Mulai:</span>
                                <span class="font-bold text-brand-black">{{ \Carbon\Carbon::parse($sesi->tanggal_mulai)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-brand-gray font-light block">Usia Tanaman:</span>
                                <span class="font-bold text-brand-black">{{ $sesi->usia_hari }} Hari</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-2">
                            <div class="w-full bg-brand-graylt rounded-full h-1">
                                <div class="bg-brand-green h-full rounded-full transition-all duration-300 riwayat-progress" style="width: {{ $sesi->progress_persen }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-brand-gray font-bold">
                                <span>Kemajuan Fase</span>
                                <span class="text-brand-black">{{ number_format($sesi->progress_persen, 0) }}%</span>
                            </div>
                        </div>

                        <!-- Actions Buttons -->
                        <div class="pt-2 flex justify-between gap-3 items-center">
                            <a href="/hasil?sesi_id={{ $sesi->id }}#jadwal" class="bg-white text-brand-black border border-brand-graylt rounded-xl px-4 py-2 text-xs font-medium hover:border-brand-green hover:text-brand-green transition-colors duration-200 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-check text-xs"></i> Agenda Perawatan
                            </a>
                            
                            <form action="/sesi-tanam/{{ $sesi->id }}/panen" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menandai sesi tanam ini sebagai panen?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white rounded-xl px-4 py-2 text-[10px] font-semibold transition-all duration-200 flex items-center gap-1">
                                    <i class="fa-solid fa-basket-shopping text-[9px]"></i> Panen
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section 2: Selesai / Panen -->
    <div class="space-y-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-brand-gray flex items-center gap-2">
            <i class="fa-solid fa-circle-check opacity-60"></i>
            Telah Berhasil Dipanen ({{ $sesiPanen->count() }})
        </h2>

        @if($sesiPanen->isEmpty())
            <div class="bg-white p-8 rounded-2xl text-center border border-brand-graylt text-brand-gray">
                Belum ada riwayat panen terdahulu.
            </div>
        @else
            <div class="bg-white rounded-2xl border border-brand-graylt overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs leading-normal">
                        <thead>
                            <tr class="bg-brand-offwhite border-b border-brand-graylt text-brand-gray font-semibold text-[10px] tracking-wider uppercase">
                                <th class="px-6 py-4">Tanaman</th>
                                <th class="px-6 py-4">Sistem</th>
                                <th class="px-6 py-4">Tanggal Mulai</th>
                                <th class="px-6 py-4">Tanggal Panen</th>
                                <th class="px-6 py-4">Total Durasi</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-graylt/50 text-brand-black">
                            @foreach($sesiPanen as $sesi)
                                <tr class="hover:bg-brand-offwhite transition-colors">
                                    <td class="px-6 py-4 font-bold flex items-center gap-2">
                                        <span class="text-2xl">{{ $sesi->tanaman->emoji }}</span>
                                        <span>{{ $sesi->tanaman->nama }}</span>
                                    </td>
                                    <td class="px-6 py-4 uppercase font-medium text-brand-gray">{{ $sesi->sistem_hidroponik }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($sesi->tanggal_mulai)->translatedFormat('d M Y') }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($sesi->updated_at)->translatedFormat('d M Y') }}</td>
                                    <td class="px-6 py-4 font-bold text-brand-green">{{ number_format($sesi->durasi_hari, 0) }} Hari Tanam</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 bg-brand-greenpal text-brand-green border border-brand-greenlt/20 px-2.5 py-1 rounded-full font-bold text-[10px]">
                                            Panen <i class="fa-solid fa-check text-[10px]"></i>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>


@endsection
