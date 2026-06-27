@extends('layouts.app')

@section('title', 'Rekomendasi Nutrisi - HidroNutri')

@section('content')
<div class="space-y-16 animate-fade-in page-wrapper">
    <!-- Hero 2 Kolom Asimetris -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Kolom Kiri (60%) -->
        <div class="lg:col-span-7 space-y-6">
            <span class="bg-brand-greenpal text-brand-green rounded-full px-4 py-1 text-xs font-semibold inline-block">
                SISTEM PAKAR HIDROPONIK
            </span>
            <h1 class="text-5xl md:text-6xl font-black tracking-tighter leading-none text-brand-black hero-heading gsap-hidden">
                Rekomendasi <span class="text-brand-green">Nutrisi</span> Hidroponik
            </h1>
            <p class="text-lg text-brand-gray max-w-md leading-relaxed">
                Dapatkan formulasi pH, EC, PPM, dan dosis nutrisi AB Mix yang ideal untuk memaksimalkan hasil panen tanaman Anda.
            </p>
            <div class="pt-4 space-y-3">
                <div class="flex items-center gap-2 text-sm text-brand-gray">
                    <svg class="w-4 h-4 text-brand-green" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <span>Akurasi formula berbasis data ilmiah</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-brand-gray">
                    <svg class="w-4 h-4 text-brand-green" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <span>Disesuaikan dengan fase pertumbuhan tanaman</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-brand-gray">
                    <svg class="w-4 h-4 text-brand-green" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    <span>Dilengkapi kalkulator dosis tandon praktis</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (40%) -->
        <div class="lg:col-span-5">
            <div class="relative rounded-3xl overflow-hidden h-[420px]">
              <!-- Foto utama hero -->
              <img 
                src="{{ asset('images/rekomendasi_hero.png') }}" 
                alt="Tanaman hidroponik indoor segar"
                class="w-full h-full object-cover"
                loading="lazy"
              >
              
              <!-- Overlay gradient bawah -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
              
              <!-- Floating card di atas foto (tetap tampilkan metric) -->
              <div class="absolute bottom-4 left-4 right-4">
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-4 flex gap-4">
                  <div class="flex-1">
                    <div class="text-xs font-semibold uppercase tracking-widest text-brand-gray">pH Air</div>
                    <div class="text-2xl font-black text-brand-black">6.0</div>
                  </div>
                  <div class="w-px bg-brand-graylt"></div>
                  <div class="flex-1">
                    <div class="text-xs font-semibold uppercase tracking-widest text-brand-gray">EC Target</div>
                    <div class="text-2xl font-black text-brand-black">1.4 <span class="text-sm font-normal">mS/cm</span></div>
                  </div>
                  <div class="w-px bg-brand-graylt"></div>
                  <div class="flex-1">
                    <div class="text-xs font-semibold uppercase tracking-widest text-brand-gray">PPM</div>
                    <div class="text-2xl font-black text-brand-black">700</div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-brand-graylt my-8"></div>

    <!-- Form -->
    <form action="/rekomendasi" method="POST" id="rekomendasi-form" class="space-y-12">
        @csrf
        
        <!-- Hidden Inputs for JS Integration -->
        <input type="hidden" name="tanaman_id" id="selected-tanaman-id" value="{{ old('tanaman_id') }}">

        <!-- 1. Pilih Tanaman (Grid 4 Kolom) -->
        <div class="space-y-6">
            <div class="flex items-center">
                <span class="w-7 h-7 rounded-lg bg-brand-black text-white text-xs font-bold flex items-center justify-center mr-3 flex-shrink-0">1</span>
                <h2 class="text-2xl font-bold tracking-tight text-brand-black">Pilih Jenis Tanaman</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 tanaman-grid" id="tanaman-grid">
                <!-- Active Plants from DB -->
                @foreach($tanaman as $t)
                    @php
                        $isSelected = old('tanaman_id') == $t->id;
                    @endphp
                    <div data-id="{{ $t->id }}" 
                         class="tanaman-card group relative bg-white border {{ $isSelected ? 'border-brand-green bg-brand-greenpal ring-2 ring-brand-greenpal/20' : 'border-brand-graylt' }} rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:border-brand-green hover:shadow-sm active:scale-95">
                        
                        <!-- Foto tanaman (atas card) -->
                        <div class="relative h-28 md:h-36 overflow-hidden">
                            <img 
                                src="{{ str_starts_with($t->foto_url, 'http') ? $t->foto_url : asset($t->foto_url) }}" 
                                alt="{{ $t->nama }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy"
                            >
                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                            
                            <!-- Badge kategori pojok kiri atas -->
                            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-brand-green text-xs font-semibold px-2 py-1 rounded-full">
                                {{ $t->kategori }}
                            </span>
                            
                            <!-- Checkmark saat selected (pojok kanan atas) -->
                            <span class="checkmark-indicator absolute top-3 right-3 w-6 h-6 rounded-full bg-brand-green text-white flex items-center justify-center text-xs transition-all duration-200 {{ $isSelected ? 'opacity-100 scale-100' : 'opacity-0 scale-75' }}">✓</span>
                        </div>
                        
                        <!-- Info bawah card -->
                        <div class="p-4">
                            <div class="font-semibold text-brand-black text-sm">
                                {{ $t->nama }}
                            </div>
                            <div class="text-xs text-brand-gray mt-0.5">
                                Klik untuk pilih
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Dummy Plants (Segera Hadir) -->
                @php
                    $dummies = [
                        ['nama' => 'Bayam', 'emoji' => '🥬', 'kategori' => 'daun', 'foto_url' => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=400&q=80&auto=format&fit=crop'],
                        ['nama' => 'Stroberi', 'emoji' => '🍓', 'kategori' => 'buah', 'foto_url' => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=400&q=80&auto=format&fit=crop'],
                        ['nama' => 'Tomat', 'emoji' => '🍅', 'kategori' => 'buah', 'foto_url' => 'https://images.unsplash.com/photo-1592841200221-a6898f307baa?w=400&q=80&auto=format&fit=crop'],
                    ];
                @endphp
                @foreach($dummies as $d)
                    <div class="bg-white border border-brand-graylt/40 rounded-2xl overflow-hidden cursor-not-allowed relative opacity-50">
                        <span class="absolute top-3 left-3 bg-brand-grayultra text-brand-gray rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider z-10">
                            Segera Hadir
                        </span>
                        
                        <!-- Foto dummy tanaman (atas card) -->
                        <div class="relative h-28 md:h-36 overflow-hidden grayscale">
                            <img 
                                src="{{ str_starts_with($d['foto_url'], 'http') ? $d['foto_url'] : asset($d['foto_url']) }}" 
                                alt="{{ $d['nama'] }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                        </div>

                        <!-- Info bawah card -->
                        <div class="p-4">
                            <div class="font-semibold text-brand-gray text-sm">
                                {{ $d['nama'] }}
                            </div>
                            <div class="text-xs text-brand-gray mt-0.5">
                                Belum tersedia
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('tanaman_id')
                <p class="mt-2 text-sm text-red-500 font-semibold flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        <!-- 2. Pilih Tanggal Mulai Tanam -->
        <div class="space-y-6">
            <div class="flex items-center">
                <span class="w-7 h-7 rounded-lg bg-brand-black text-white text-xs font-bold flex items-center justify-center mr-3 flex-shrink-0">2</span>
                <h2 class="text-2xl font-bold tracking-tight text-brand-black">Tanggal Semai / Tanam Biji</h2>
            </div>
            
            <div class="bg-white border border-brand-graylt rounded-2xl p-6">
                <p class="text-sm text-brand-gray mb-4">
                    Pilih tanggal kapan benih mulai disemai. Sistem akan secara otomatis menghitung usia tanaman dan menentukan fase pertumbuhannya saat ini.
                </p>
                <div class="flex flex-col gap-4">
                    <div class="w-full md:w-1/2">
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 rounded-xl border border-brand-graylt bg-white text-brand-black font-medium focus:border-brand-green ring-2 ring-brand-greenpal outline-none transition-all">
                        
                        @error('tanggal_mulai')
                            <p class="mt-2 text-sm text-red-500 font-semibold flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Live Preview Box -->
                    <div id="phase-preview-box" class="w-full md:w-1/2 bg-brand-offwhite border border-brand-graylt rounded-xl p-4 hidden transition-all duration-300">
                        <div class="text-xs font-semibold uppercase tracking-widest text-brand-gray mb-1">Prediksi Fase Saat Ini</div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white text-brand-green flex items-center justify-center shadow-sm border border-brand-graylt/50">
                                <i id="preview-icon" class="fa-solid fa-seedling text-sm"></i>
                            </div>
                            <div>
                                <div id="preview-usia" class="text-xs text-brand-gray font-medium">Usia: 0 Hari</div>
                                <div id="preview-fase" class="text-brand-black font-bold text-lg leading-tight">Semai</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Pilih Sistem Hidroponik -->
        <div class="space-y-6">
            <div class="flex items-center">
                <span class="w-7 h-7 rounded-lg bg-brand-black text-white text-xs font-bold flex items-center justify-center mr-3 flex-shrink-0">3</span>
                <h2 class="text-2xl font-bold tracking-tight text-brand-black">Pilih Sistem Hidroponik</h2>
            </div>
            
            <!-- Hidden input untuk menyimpan nilai (ganti name select lama) -->
            <input type="hidden" name="sistem_hidroponik" id="sistem_hidroponik" value="{{ old('sistem_hidroponik') }}">

            <!-- Grid 4 card -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sistem-grid">

              <!-- Card NFT -->
              <div class="sistem-card bg-white border border-brand-graylt rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:border-brand-green hover:shadow-sm group"
                   data-value="nft"
                   onclick="selectSistem(this, 'nft')">
                
                <!-- Foto sistem -->
                <div class="relative h-40 overflow-hidden">
                  <img 
                    src="https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?w=400&q=80&auto=format&fit=crop"
                    alt="Sistem NFT"
                    loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                  
                  <!-- Label di atas foto -->
                  <span class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm text-brand-black text-xs font-bold px-2 py-1 rounded-full">
                    NFT
                  </span>
                  
                  <!-- Checkmark saat selected -->
                  <span class="selected-check absolute top-3 right-3 w-6 h-6 rounded-full bg-brand-green text-white flex items-center justify-center text-xs font-bold hidden">
                    ✓
                  </span>
                </div>
                
                <!-- Deskripsi -->
                <div class="p-4">
                  <div class="font-semibold text-brand-black text-sm mb-1">
                    Nutrient Film Technique
                  </div>
                  <div class="text-xs text-brand-gray leading-relaxed">
                    Larutan mengalir tipis di dasar pipa. Cocok untuk sayuran daun, efisien air.
                  </div>
                  <!-- Tag cocok untuk -->
                  <div class="mt-3 flex flex-wrap gap-1">
                    <span class="bg-brand-greenpal text-brand-green text-xs px-2 py-0.5 rounded-full font-medium">
                      Selada
                    </span>
                    <span class="bg-brand-greenpal text-brand-green text-xs px-2 py-0.5 rounded-full font-medium">
                      Pakcoy
                    </span>
                  </div>
                </div>
              </div>

              <!-- Card DFT -->
              <div class="sistem-card bg-white border border-brand-graylt rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:border-brand-green hover:shadow-sm group"
                   data-value="dft"
                   onclick="selectSistem(this, 'dft')">
                
                <div class="relative h-40 overflow-hidden">
                  <img 
                    src="https://images.unsplash.com/photo-1598123586091-ff6a30ed031c?w=400&q=80&auto=format&fit=crop"
                    alt="Sistem DFT"
                    loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                  <span class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm text-brand-black text-xs font-bold px-2 py-1 rounded-full">
                    DFT
                  </span>
                  <span class="selected-check absolute top-3 right-3 w-6 h-6 rounded-full bg-brand-green text-white flex items-center justify-center text-xs font-bold hidden">
                    ✓
                  </span>
                </div>
                
                <div class="p-4">
                  <div class="font-semibold text-brand-black text-sm mb-1">
                    Deep Flow Technique
                  </div>
                  <div class="text-xs text-brand-gray leading-relaxed">
                    Akar terendam larutan lebih dalam. Stabil dan toleran gangguan pompa.
                  </div>
                  <div class="mt-3 flex flex-wrap gap-1">
                    <span class="bg-brand-greenpal text-brand-green text-xs px-2 py-0.5 rounded-full font-medium">
                      Kangkung
                    </span>
                    <span class="bg-brand-greenpal text-brand-green text-xs px-2 py-0.5 rounded-full font-medium">
                      Bayam
                    </span>
                  </div>
                </div>
              </div>

              <!-- Card Rakit Apung -->
              <div class="sistem-card bg-white border border-brand-graylt rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:border-brand-green hover:shadow-sm group"
                   data-value="rakit_apung"
                   onclick="selectSistem(this, 'rakit_apung')">
                
                <div class="relative h-40 overflow-hidden">
                  <img 
                    src="{{ asset('images/rakit_apung.png') }}"
                    alt="Sistem Rakit Apung"
                    loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                  <span class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm text-brand-black text-xs font-bold px-2 py-1 rounded-full">
                    Rakit Apung
                  </span>
                  <span class="selected-check absolute top-3 right-3 w-6 h-6 rounded-full bg-brand-green text-white flex items-center justify-center text-xs font-bold hidden">
                    ✓
                  </span>
                </div>
                
                <div class="p-4">
                  <div class="font-semibold text-brand-black text-sm mb-1">
                    Rakit Apung
                  </div>
                  <div class="text-xs text-brand-gray leading-relaxed">
                    Tanaman mengapung di atas bak larutan. Paling mudah untuk pemula.
                  </div>
                  <div class="mt-3 flex flex-wrap gap-1">
                    <span class="bg-brand-greenpal text-brand-green text-xs px-2 py-0.5 rounded-full font-medium">
                      Semua tanaman
                    </span>
                  </div>
                </div>
              </div>

              <!-- Card Wick System -->
              <div class="sistem-card bg-white border border-brand-graylt rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 hover:border-brand-green hover:shadow-sm group"
                   data-value="wick"
                   onclick="selectSistem(this, 'wick')">
                
                <div class="relative h-40 overflow-hidden">
                  <img 
                    src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&q=80&auto=format&fit=crop"
                    alt="Sistem Wick"
                    loading="lazy"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                  <span class="absolute bottom-3 left-3 bg-white/90 backdrop-blur-sm text-brand-black text-xs font-bold px-2 py-1 rounded-full">
                    Wick
                  </span>
                  <span class="selected-check absolute top-3 right-3 w-6 h-6 rounded-full bg-brand-green text-white flex items-center justify-center text-xs font-bold hidden">
                    ✓
                  </span>
                </div>
                
                <div class="p-4">
                  <div class="font-semibold text-brand-black text-sm mb-1">
                    Wick System
                  </div>
                  <div class="text-xs text-brand-gray leading-relaxed">
                    Larutan naik lewat sumbu/kain ke akar. Tanpa pompa, sangat hemat energi.
                  </div>
                  <div class="mt-3 flex flex-wrap gap-1">
                    <span class="bg-brand-greenpal text-brand-green text-xs px-2 py-0.5 rounded-full font-medium">
                      Tanaman kecil
                    </span>
                  </div>
                </div>
              </div>

            </div>

            <!-- Validasi: tampilkan pesan jika belum pilih -->
            @error('sistem_hidroponik')
                <p id="sistem-error" class="text-red-500 text-sm font-semibold flex items-center gap-1 mt-2">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </p>
            @else
                <p id="sistem-error" class="text-red-500 text-sm font-semibold flex items-center gap-1 mt-2 hidden">
                    <i class="fa-solid fa-circle-exclamation"></i> Pilih sistem hidroponik terlebih dahulu.
                </p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-6">
            <button type="submit" 
                    class="btn-submit w-full md:w-auto bg-brand-black text-white rounded-xl px-8 py-4 text-sm font-semibold hover:bg-brand-green transition-colors duration-200 flex items-center justify-center gap-2">
                <span>Dapatkan Rekomendasi</span>
                <span>→</span>
            </button>
        </div>
    </form>
</div>

<script>
    const durasiMap = @json($durasiMap);
    const tanamanMap = @json($tanamanMap);

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
        gsap.fromTo(previewBox, { scale: 0.95, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.3, ease: 'back.out(1.5)' });
    }


    // Global function for system selection
    function selectSistem(el, nilai) {
        // Reset semua card
        document.querySelectorAll('.sistem-card').forEach(card => {
            card.classList.remove('border-brand-green', 'bg-brand-offwhite');
            card.classList.add('border-brand-graylt');
            const check = card.querySelector('.selected-check');
            if (check) {
                check.classList.add('hidden');
                check.classList.remove('flex');
            }
        });

        // Aktifkan card yang diklik
        el.classList.add('border-brand-green', 'bg-brand-offwhite');
        el.classList.remove('border-brand-graylt');
        const check = el.querySelector('.selected-check');
        if (check) {
            check.classList.remove('hidden');
            check.classList.add('flex');
        }

        // Set nilai ke hidden input
        document.getElementById('sistem_hidroponik').value = nilai;
        
        // Sembunyikan error
        const errorEl = document.getElementById('sistem-error');
        if (errorEl) {
            errorEl.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize on page load (support old/existing value)
        const initialSistem = document.getElementById('sistem_hidroponik').value;
        if (initialSistem) {
            const card = document.querySelector(`.sistem-card[data-value="${initialSistem}"]`);
            if (card) {
                selectSistem(card, initialSistem);
            }
        }

        // Validasi sebelum submit
        const form = document.getElementById('rekomendasi-form') || document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!document.getElementById('sistem_hidroponik').value) {
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
        
        document.getElementById('tanggal_mulai').addEventListener('change', updatePhasePreview);

        // Handle plant cards click
        tanamanCards.forEach(card => {
            card.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                // Toggle active classes on card
                tanamanCards.forEach(c => {
                    c.classList.remove('border-brand-green', 'bg-brand-greenpal', 'ring-2', 'ring-brand-greenpal/20');
                    c.classList.add('border-brand-graylt');
                    
                    const emojiCont = c.querySelector('.w-14');
                    if (emojiCont) {
                        emojiCont.classList.remove('bg-white');
                        emojiCont.classList.add('bg-brand-offwhite');
                    }
                    
                    c.querySelector('.checkmark-indicator').classList.add('opacity-0', 'scale-75');
                    c.querySelector('.checkmark-indicator').classList.remove('opacity-100', 'scale-100');
                });
                
                this.classList.remove('border-brand-graylt');
                this.classList.add('border-brand-green', 'bg-brand-greenpal', 'ring-2', 'ring-brand-greenpal/20');
                
                const emojiCont = this.querySelector('.w-14');
                if (emojiCont) {
                    emojiCont.classList.remove('bg-brand-offwhite');
                    emojiCont.classList.add('bg-white');
                }
                
                this.querySelector('.checkmark-indicator').classList.remove('opacity-0', 'scale-75');
                this.querySelector('.checkmark-indicator').classList.add('opacity-100', 'scale-100');
                
                hiddenTanamanInput.value = id;
                animateCardSelect(this);
                updatePhasePreview();
            });
        });

        // Jika ada old value tanaman, render preview
        if (hiddenTanamanInput.value) {
            updatePhasePreview();
        }

        // ─── GSAP PAGE-SPECIFIC ANIMATIONS ──────────────────────────
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

        // Card tanaman muncul stagger (dijalankan langsung tanpa ScrollTrigger agar tidak bug/tersembunyi)
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

        // Card sistem hidroponik (dijalankan langsung)
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

        // Animasi saat tanaman dipilih
        function animateCardSelect(el) {
          gsap.timeline()
            .to(el, { scale: 0.96, duration: 0.1, ease: 'power2.in' })
            .to(el, { scale: 1.02, duration: 0.15, ease: 'power2.out' })
            .to(el, { scale: 1, duration: 0.1 });
        }
    });
</script>
@endsection
