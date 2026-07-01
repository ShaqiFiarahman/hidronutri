@props([
    'progressPersen' => null,
    'tanaman',
    'usiaHari',
    'fase',
    'estimasiPindahFase' => null,
    'durasiTotal' => 35,
    'kalenderBulan',
    'faseBerikutnya' => null,
    'sesiAktif' => null
])

<!-- Bagian gabungan jadwal dan kemajuan pemeliharaan -->
<div id="jadwal" class="mt-12 space-y-8 pt-8 border-t border-brand-graylt/80">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-brand-black flex items-center gap-2.5">
                <i class="fa-solid fa-calendar-check text-brand-green"></i> Jadwal & Progress Pemeliharaan
            </h2>
            <p class="text-xs text-brand-gray mt-1">Pantau siklus pertumbuhan dan agenda pemeliharaan rutin otomatis tanaman Anda</p>
        </div>
    </div>

    <!-- Panel indikator progres sesi aktif -->
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

        <!-- Bilah persentase pencapaian siklus -->
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
        <!-- Area kalender pemeliharaan interaktif -->
        <div class="lg:col-span-2 space-y-6" id="calendar-container">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-brand-black flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-brand-green"></i> Kalender Pemeliharaan
                </h3>
                <div class="flex items-center gap-4 text-sm font-semibold">
                    <span class="text-brand-black">{{ \Carbon\Carbon::today()->translatedFormat('F Y') }}</span>
                </div>
            </div>

            <!-- Tata letak kotak penanggalan -->
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
                                            $iconClass = str_starts_with($k['tipe'], 'cek') ? 'fa-eye-dropper' : 'fa-flask';
                                            
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
            
            <!-- Panel rincian harian yang awalnya tersembunyi -->
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
                    <!-- Tugas-tugas akan dimuat melalui JavaScript -->
                </div>
            </div>
        </div>

        <!-- Area catatan dan informasi konteks -->
        <div class="space-y-6">
            @if(!empty($faseBerikutnya))
            <!-- Informasi persiapan untuk tahapan selanjutnya -->
            <div class="border-l-4 border-brand-green bg-brand-offwhite p-6 rounded-r-2xl rounded-l-md space-y-4 next-phase-card">
                <h3 class="font-bold text-base flex items-center gap-2 text-brand-green">
                    <i class="fa-solid fa-circle-info text-brand-green"></i> Persiapan Fase Berikutnya
                </h3>
                <div class="space-y-1">
                    <span class="text-xs text-brand-gray block">Fase Selanjutnya:</span>
                    <span class="text-lg font-extrabold text-brand-black block">Fase {{ $faseBerikutnya }}</span>
                </div>
            </div>
            @endif

            @if(!empty($sesiAktif))
            <!-- Kartu penanda penyelesaian panen -->
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
