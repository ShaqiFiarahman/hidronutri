@extends('layouts.app')

@section('title', 'Dashboard - HidroNutri')

@section('no-container', true)

@section('content')
<div class="page-wrapper">
    <!-- SECTION 1: HERO FULLWIDTH -->
    <section class="hero-section relative h-[85vh] min-h-[600px] overflow-hidden">
      
      <!-- Foto background -->
      <img 
        src="https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?w=1600&q=85&auto=format&fit=crop"
        alt="Kebun hidroponik modern"
        class="absolute inset-0 w-full h-full object-cover hero-img"
        loading="lazy"
      >
      
      <!-- Overlay gradient -->
      <div class="absolute inset-0 bg-gradient-to-r 
                  from-black/70 via-black/40 to-transparent"></div>
      
      <!-- Konten teks kiri -->
      <div class="relative z-10 h-full flex items-center">
        <div class="max-w-7xl mx-auto px-8 w-full">
          <div class="max-w-xl">
            
            <!-- Label pill -->
            <span class="inline-block bg-brand-green text-white 
                         text-xs font-semibold px-4 py-1.5 
                         rounded-full mb-6 uppercase tracking-widest gsap-up">
              Sistem Pakar Hidroponik
            </span>
            
            <!-- Heading besar -->
            <h1 class="text-5xl md:text-6xl font-black text-white 
                       leading-none tracking-tighter mb-6">
              <span class="inline-block gsap-left">Tanam Lebih</span><br>
              <span class="inline-block overflow-hidden align-bottom"><span class="text-brand-moss inline-block gsap-cerdas" style="transform: translateY(100%);">Cerdas</span></span> <span class="inline-block gsap-left">dengan</span><br>
              <span class="inline-block gsap-left">Hidroponik</span>
            </h1>
            
            <!-- Subjudul -->
            <p class="text-lg text-white/80 leading-relaxed mb-8 max-w-md gsap-up">
              Panduan lengkap nutrisi, pH, dan EC untuk hasil panen 
              optimal. Didukung sistem pakar berbasis literatur ilmiah.
            </p>
            
            <!-- 2 tombol CTA -->
            <div class="flex gap-4 flex-wrap gsap-stagger">
              <a href="{{ route('rekomendasi') }}" 
                 class="bg-white text-brand-black font-semibold 
                        px-8 py-4 rounded-xl text-sm
                        hover:bg-brand-green hover:text-white 
                        transition-colors duration-200 
                        flex items-center gap-2">
                Mulai Rekomendasi →
              </a>
              <a href="#panduan" 
                 class="bg-white/10 backdrop-blur-sm text-white 
                        font-semibold px-8 py-4 rounded-xl text-sm
                        border border-white/30
                        hover:bg-white/20 transition-colors duration-200">
                Pelajari Dasar
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Scroll indicator bawah -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 
                  text-white/60 text-xs flex flex-col items-center gap-2">
        <span>Scroll untuk pelajari lebih</span>
        <div class="w-px h-8 bg-white/40"></div>
      </div>
      
    </section>

    <!-- SECTION 2: STATISTIK FAKTA MENARIK -->
    <section class="bg-brand-black py-16">
      <div class="max-w-7xl mx-auto px-8">
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          
          <div class="text-center">
            <div class="text-5xl font-black text-white stat-number" data-target="90" data-suffix="%">0%</div>
            <div class="text-sm text-white/60 leading-relaxed mt-2">
              Lebih hemat air dibanding pertanian konvensional
            </div>
          </div>
          
          <div class="text-center">
            <div class="text-5xl font-black text-brand-moss stat-number" data-target="3" data-suffix="x">0x</div>
            <div class="text-sm text-white/60 leading-relaxed mt-2">
              Lebih cepat pertumbuhan dibanding tanam di tanah
            </div>
          </div>
          
          <div class="text-center">
            <div class="text-5xl font-black text-white stat-number" data-target="22" data-suffix="">0</div>
            <div class="text-sm text-white/60 leading-relaxed mt-2">
              Rule pengetahuan dalam sistem pakar HidroNutri
            </div>
          </div>
          
          <div class="text-center">
            <div class="text-5xl font-black text-brand-moss stat-number" data-target="5" data-suffix="">0</div>
            <div class="text-sm text-white/60 leading-relaxed mt-2">
              Jenis tanaman didukung sistem rekomendasi ini
            </div>
          </div>
          
        </div>
      </div>
    </section>

    <!-- SECTION 3: PANDUAN DASAR PARAMETER -->
    <section id="panduan" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-8">
        
        <!-- Label + heading -->
        <div class="mb-12">
          <span class="text-xs font-semibold uppercase tracking-widest 
                       text-brand-green">
            Panduan Dasar
          </span>
          <h2 class="text-4xl font-black text-brand-black 
                     tracking-tight mt-2 gsap-heading">
            3 Parameter Wajib<br>Dipantau Setiap Hari
          </h2>
        </div>
        
        <!-- 3 card parameter -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          
          <!-- Card pH -->
          <div class="bg-brand-offwhite rounded-3xl overflow-hidden gsap-scale">
            <div class="relative h-48">
              <img 
                src="{{ asset('images/ph_measurement.png') }}"
                alt="pH meter hidroponik"
                class="w-full h-full object-cover"
                loading="lazy"
              >
              <div class="absolute inset-0 bg-gradient-to-t 
                          from-black/50 to-transparent"></div>
              <span class="absolute bottom-4 left-4 text-white 
                           font-black text-2xl">pH</span>
            </div>
            <div class="p-6">
              <h3 class="font-bold text-brand-black text-lg mb-2">
                Keasaman Larutan
              </h3>
              <p class="text-sm text-brand-gray leading-relaxed mb-4">
                pH mengukur tingkat keasaman larutan nutrisi. 
                Nilai ideal untuk hidroponik berada di rentang 
                5.5 – 6.5. Di luar rentang ini, tanaman tidak 
                bisa menyerap nutrisi meski dosisnya sudah tepat.
              </p>
              <!-- Skala visual -->
              <div class="flex gap-1 items-center">
                <span class="text-xs text-brand-gray">Asam</span>
                <div class="flex-1 h-2 rounded-full overflow-hidden 
                            bg-gradient-to-r from-red-400 via-brand-green 
                            to-blue-400 mx-2"></div>
                <span class="text-xs text-brand-gray">Basa</span>
              </div>
              <div class="mt-2 flex justify-between text-xs 
                          text-brand-gray">
                <span>0</span>
                <span class="font-semibold text-brand-green">5.5–6.5</span>
                <span>14</span>
              </div>
            </div>
          </div>
          
          <!-- Card EC -->
          <div class="bg-brand-offwhite rounded-3xl overflow-hidden gsap-scale">
            <div class="relative h-48">
              <img 
                src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80&auto=format&fit=crop"
                alt="EC meter hidroponik"
                class="w-full h-full object-cover"
                loading="lazy"
              >
              <div class="absolute inset-0 bg-gradient-to-t 
                          from-black/50 to-transparent"></div>
              <span class="absolute bottom-4 left-4 text-white 
                           font-black text-2xl">EC</span>
            </div>
            <div class="p-6">
              <h3 class="font-bold text-brand-black text-lg mb-2">
                Kepekatan Nutrisi
              </h3>
              <p class="text-sm text-brand-gray leading-relaxed mb-4">
                EC (Electrical Conductivity) mengukur seberapa pekat 
                larutan nutrisi. Satuan mS/cm. Makin tinggi EC, makin 
                banyak nutrisi. Terlalu tinggi merusak akar, 
                terlalu rendah membuat tanaman kelaparan.
              </p>
              <!-- Tabel range singkat -->
              <div class="space-y-1.5">
                <div class="flex justify-between text-xs">
                  <span class="text-brand-gray">Sayuran daun</span>
                  <span class="font-semibold text-brand-black">
                    1.0 – 2.4 mS/cm
                  </span>
                </div>
                <div class="flex justify-between text-xs">
                  <span class="text-brand-gray">Sayuran buah</span>
                  <span class="font-semibold text-brand-black">
                    1.6 – 3.0 mS/cm
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Card PPM -->
          <div class="bg-brand-offwhite rounded-3xl overflow-hidden gsap-scale">
            <div class="relative h-48">
              <img 
                src="https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?w=600&q=80&auto=format&fit=crop"
                alt="TDS meter PPM hidroponik"
                class="w-full h-full object-cover"
                loading="lazy"
              >
              <div class="absolute inset-0 bg-gradient-to-t 
                          from-black/50 to-transparent"></div>
              <span class="absolute bottom-4 left-4 text-white 
                           font-black text-2xl">PPM</span>
            </div>
            <div class="p-6">
              <h3 class="font-bold text-brand-black text-lg mb-2">
                Parts Per Million
              </h3>
              <p class="text-sm text-brand-gray leading-relaxed mb-4">
                PPM mengukur hal yang sama dengan EC, hanya berbeda 
                satuan. Konversi sederhana: PPM = EC × 500. 
                Diukur menggunakan TDS meter yang mudah didapat 
                dengan harga terjangkau.
              </p>
              <!-- Konversi visual -->
              <div class="bg-white rounded-xl p-3 text-center">
                <div class="text-xs text-brand-gray mb-1">Konversi</div>
                <div class="font-black text-brand-black text-sm">
                  EC 1.0 mS/cm = PPM 500
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </section>

    <!-- SECTION 4: JENIS SISTEM HIDROPONIK -->
    <section class="py-20 bg-brand-offwhite">
      <div class="max-w-7xl mx-auto px-8">
        
        <div class="mb-12">
          <span class="text-xs font-semibold uppercase tracking-widest 
                       text-brand-green">
            Jenis Sistem
          </span>
          <h2 class="text-4xl font-black text-brand-black 
                     tracking-tight mt-2 gsap-heading">
            4 Sistem Hidroponik<br>yang Didukung
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 gsap-stagger">
          
          @php
          $sistem = [
            ['nama' => 'NFT', 'full' => 'Nutrient Film Technique',
             'desc' => 'Larutan mengalir tipis di dasar pipa. Hemat air, cocok untuk sayuran daun seperti selada dan pakcoy.',
             'level' => 'Menengah', 'foto' => 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?w=400&q=80&auto=format&fit=crop'],
            ['nama' => 'DFT', 'full' => 'Deep Flow Technique',
             'desc' => 'Akar terendam larutan lebih dalam. Lebih stabil dan toleran jika pompa mati beberapa jam.',
             'level' => 'Menengah', 'foto' => 'https://images.unsplash.com/photo-1598123586091-ff6a30ed031c?w=400&q=80&auto=format&fit=crop'],
            ['nama' => 'Rakit Apung', 'full' => 'Floating Raft System',
             'desc' => 'Tanaman di atas styrofoam yang mengapung di bak larutan. Paling mudah dan murah untuk pemula.',
             'level' => 'Pemula', 'foto' => 'images/rakit_apung.png'],
            ['nama' => 'Wick', 'full' => 'Wick System',
             'desc' => 'Larutan naik ke akar lewat sumbu kain. Tanpa pompa, sangat hemat energi untuk tanaman kecil.',
             'level' => 'Pemula', 'foto' => 'https://images.unsplash.com/photo-1592841200221-a6898f307baa?w=400&q=80&auto=format&fit=crop'],
          ];
          @endphp
          
          @foreach($sistem as $s)
          <div class="bg-white rounded-2xl overflow-hidden border 
                      border-brand-graylt hover:border-brand-green 
                      hover:shadow-sm transition-all duration-200">
            <div class="relative h-44">
              <img 
                src="{{ str_starts_with($s['foto'], 'http') ? $s['foto'] : asset($s['foto']) }}"
                alt="{{ $s['nama'] }}"
                loading="lazy"
                class="w-full h-full object-cover"
              >
              <div class="absolute inset-0 bg-gradient-to-t 
                          from-black/50 to-transparent"></div>
              <span class="absolute bottom-3 left-3 text-white 
                           font-black text-lg">
                {{ $s['nama'] }}
              </span>
              <span class="absolute top-3 right-3 
                           {{ $s['level'] === 'Pemula' 
                              ? 'bg-brand-green' : 'bg-brand-amber' }} 
                           text-white text-xs font-semibold 
                           px-2 py-1 rounded-full">
                {{ $s['level'] }}
              </span>
            </div>
            <div class="p-5">
              <div class="text-xs text-brand-gray mb-2">
                {{ $s['full'] }}
              </div>
              <p class="text-sm text-brand-gray leading-relaxed">
                {{ $s['desc'] }}
              </p>
            </div>
          </div>
          @endforeach
          
        </div>
      </div>
    </section>

    <!-- SECTION 5: TIPS & ARTIKEL SINGKAT -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-8">
        
        <div class="flex justify-between items-end mb-12">
          <div>
            <span class="text-xs font-semibold uppercase tracking-widest 
                         text-brand-green">
              Tips & Panduan
            </span>
            <h2 class="text-4xl font-black text-brand-black 
                       tracking-tight mt-2 gsap-heading">
              Baca Sebelum Mulai
            </h2>
          </div>
        </div>
        
        <!-- Layout: 1 artikel besar kiri + 2 kecil kanan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          
          <!-- Artikel besar -->
          <a href="https://hidroponikpedia.com/kepadatan-tanam-salah-produktivitas-hidroponik/" target="_blank" class="block bg-brand-offwhite rounded-3xl overflow-hidden hover:shadow-xl hover:shadow-brand-green/5 hover:-translate-y-2 transition-all duration-500 ease-out gsap-left group">
            <div class="relative h-64 overflow-hidden">
              <img 
                src="https://hidroponikpedia.com/wp-content/uploads/2026/02/kepadatan-tanam-hidroponik-yang-salah.jpg"
                alt="Jarak tanam hidroponik"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-108"
                loading="lazy"
              >
              <div class="absolute inset-0 bg-gradient-to-t 
                           from-black/60 to-transparent"></div>
              <span class="absolute top-4 left-4 bg-brand-green 
                           text-white text-xs font-semibold 
                           px-3 py-1 rounded-full shadow-sm">
                Produktivitas
              </span>
            </div>
            <div class="p-6">
              <h3 class="font-black text-brand-black text-xl 
                         leading-tight mb-3 group-hover:text-brand-green transition-colors duration-300">
                Kepadatan Tanam Salah? Ini Pengaruhnya Pada Produktivitas Hidroponik
              </h3>
              <p class="text-sm text-brand-gray leading-relaxed">
                Menanam terlalu rapat dengan harapan panen berlimpah justru sering menjadi bumerang. Jarak tanam yang ideal menjamin sirkulasi udara dan distribusi nutrisi cahaya yang maksimal bagi setiap tanaman.
              </p>
              <div class="mt-4 flex gap-3 flex-wrap">
                <span class="bg-brand-greenpal text-brand-green 
                             text-xs px-3 py-1 rounded-full font-medium">
                  Jarak Tanam
                </span>
                <span class="bg-brand-greenpal text-brand-green 
                             text-xs px-3 py-1 rounded-full font-medium">
                  Sirkulasi
                </span>
                <span class="bg-brand-greenpal text-brand-green 
                             text-xs px-3 py-1 rounded-full font-medium">
                  Pemula
                </span>
              </div>
            </div>
          </a>
          
          <!-- 2 artikel kecil kanan (stack vertikal) -->
          <div class="flex flex-col gap-6 gsap-right">
            
            <a href="https://farmee.id/tips-trik-penggunaan-nutrisi-ab-mix/" target="_blank" class="bg-brand-offwhite rounded-3xl overflow-hidden 
                        flex hover:shadow-xl hover:shadow-brand-green/5 hover:-translate-y-2 transition-all duration-500 ease-out group">
              <div class="relative w-36 flex-shrink-0 overflow-hidden">
                <img 
                  src="https://farmee.id/wp-content/uploads/2020/09/IMG_20200923_194353-1600x900.jpg"
                  alt="Perbedaan AB Mix"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-112"
                  loading="lazy"
                >
              </div>
              <div class="p-5">
                <span class="text-xs font-semibold text-brand-green 
                             uppercase tracking-wider">
                  Nutrisi
                </span>
                <h3 class="font-bold text-brand-black text-sm 
                           leading-tight mt-1 mb-2 group-hover:text-brand-green transition-colors duration-300">
                  Tips & Trik Manajemen Nutrisi AB Mix pada Tanaman Hidroponik
                </h3>
                <p class="text-xs text-brand-gray leading-relaxed">
                  Bagi kalangan hidroponik pasti tidak asing dengan nutrisi AB mix. Pelajari cara manajemen mulai dari dosis penggunaan hingga penyimpanannya agar awet.
                </p>
              </div>
            </a>
            
            <a href="https://npkmutiara.com/post/pentingnya-ph-dalam-budidaya-sistem-hidroponik" target="_blank" class="bg-brand-offwhite rounded-3xl overflow-hidden 
                        flex hover:shadow-xl hover:shadow-brand-green/5 hover:-translate-y-2 transition-all duration-500 ease-out group">
              <div class="relative w-36 flex-shrink-0 overflow-hidden">
                <img 
                  src="https://npkmutiara-prod.s3.ap-southeast-1.amazonaws.com/2025/09/13-large.jpg"
                  alt="Pentingnya pH Hidroponik"
                  class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-112"
                  loading="lazy"
                >
              </div>
              <div class="p-5">
                <span class="text-xs font-semibold text-brand-green 
                             uppercase tracking-wider">
                  Kualitas Air
                </span>
                <h3 class="font-bold text-brand-black text-sm 
                           leading-tight mt-1 mb-2 group-hover:text-brand-green transition-colors duration-300">
                  Pentingnya pH dalam Budidaya Sistem Hidroponik
                </h3>
                <p class="text-xs text-brand-gray leading-relaxed">
                  Pengaturan pH larutan nutrisi sangat menentukan keberhasilan tanaman dalam menyerap nutrisi dengan maksimal. Pahami kisaran idealnya.
                </p>
              </div>
            </a>
            
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 6: CTA BAWAH -->
    <section class="py-24 relative overflow-hidden cta-section"
             style="background: linear-gradient(135deg, 
                    #1a4a06 0%, 
                    #2D6A0F 40%, 
                    #4A9020 70%, 
                    #3B6D11 100%); background-size: 200% 200%;">

      <!-- Dekorasi lingkaran blur di background -->
      <div class="absolute top-0 left-0 w-96 h-96 rounded-full 
                  opacity-20 -translate-x-1/2 -translate-y-1/2 cta-circle-1"
           style="background: radial-gradient(circle, #97C459, transparent)">
      </div>
      <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full 
                  opacity-15 translate-x-1/3 translate-y-1/3 cta-circle-2"
           style="background: radial-gradient(circle, #C0DD97, transparent)">
      </div>
      
      <!-- Noise texture overlay tipis -->
      <div class="absolute inset-0 opacity-[0.03]"
           style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;200&quot; height=&quot;200&quot;><filter id=&quot;noise&quot;><feTurbulence type=&quot;fractalNoise&quot; baseFrequency=&quot;0.9&quot; numOctaves=&quot;4&quot; stitchTiles=&quot;stitch&quot;/></filter><rect width=&quot;200&quot; height=&quot;200&quot; filter=&quot;url(%23noise)&quot; opacity=&quot;1&quot;/></svg>')">
      </div>

      <!-- Konten CTA (tetap sama, hanya ubah warna teks) -->
      <div class="relative z-10 max-w-7xl mx-auto px-8 text-center">
        
        <h2 class="text-4xl md:text-5xl font-black text-white 
                   tracking-tight mb-4">
          Siap mulai tanam<br>
          <span class="text-brand-moss">lebih cerdas?</span>
        </h2>
        <p class="text-white/70 text-lg mb-8 max-w-md mx-auto">
          Dapatkan rekomendasi nutrisi yang tepat untuk 
          tanaman hidroponikmu dalam hitungan detik.
        </p>
        <a href="{{ route('rekomendasi') }}" 
           class="inline-flex items-center gap-2 
                  bg-white text-brand-black font-semibold 
                  px-10 py-4 rounded-xl
                  hover:bg-brand-moss hover:text-brand-black
                  transition-colors duration-200 text-sm
                  shadow-lg shadow-black/20 cta-btn">
          Mulai Rekomendasi →
        </a>
        
      </div>
    </section>

    <!-- Pemisah gelombang CTA → Footer -->
    <div class="relative z-10 -mb-1"
         style="background: linear-gradient(135deg, 
                #1a4a06 0%, #2D6A0F 40%, 
                #4A9020 70%, #3B6D11 100%);">
      <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg"
           preserveAspectRatio="none" class="w-full h-12 md:h-16">
        <path d="M0,0 C360,60 1080,60 1440,0 L1440,60 L0,60 Z" 
              fill="#0D0D0D"/>
      </svg>
    </div>
</div>
@endsection
