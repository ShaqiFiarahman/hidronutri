<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HidroNutri - Sistem Pakar Hidroponik')</title>
    
    <!-- Tailwind CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- GSAP Core -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <!-- ScrollTrigger plugin (animasi saat scroll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <!-- ScrollTo plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>
    <!-- TextPlugin (animasi teks) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/TextPlugin.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F7FAF2; /* offwhite */
        }
        /* Sembunyikan elemen sebelum animasi */
        .gsap-hidden    { opacity: 0; }
        .gsap-up        { opacity: 0; transform: translateY(40px); }
        .gsap-down      { opacity: 0; transform: translateY(-40px); }
        .gsap-left      { opacity: 0; transform: translateX(-40px); }
        .gsap-right     { opacity: 0; transform: translateX(40px); }
        .gsap-scale     { opacity: 0; transform: scale(0.85); }
        .gsap-stagger > * { opacity: 0; }

        /* Smooth page transition */
        .page-wrapper   { opacity: 0; }

        /* Cursor follower (opsional, desktop only) */
        .cursor-dot {
          width: 8px; height: 8px;
          background: #2D6A0F;
          border-radius: 50%;
          position: fixed;
          pointer-events: none;
          z-index: 9999;
          mix-blend-mode: multiply;
        }
        .cursor-ring {
          width: 32px; height: 32px;
          border: 1.5px solid #2D6A0F;
          border-radius: 50%;
          position: fixed;
          pointer-events: none;
          z-index: 9998;
          opacity: 0.5;
        }

        /* Loading bar atas halaman */
        .page-progress {
          position: fixed;
          top: 0; left: 0;
          height: 2px;
          background: #2D6A0F;
          z-index: 9999;
          width: 0%;
        }

        /* Navbar scroll effect */
        .navbar-scrolled {
          box-shadow: 0 1px 20px rgba(0,0,0,0.08);
        }

        /* ─── MARQUEE TICKER ──────────────────────────────── */
        .marquee-left  { animation: marqueeLeft  35s linear infinite; }
        .marquee-right { animation: marqueeRight 35s linear infinite; }
        @keyframes marqueeLeft {
          from { transform: translateX(0); }
          to   { transform: translateX(-33.333%); }
        }
        @keyframes marqueeRight {
          from { transform: translateX(-33.333%); }
          to   { transform: translateX(0); }
        }
        /* Pause saat hover */
        .marquee-wrapper:hover .marquee-left,
        .marquee-wrapper:hover .marquee-right {
          animation-play-state: paused;
        }

        /* Main content menutupi footer dengan shadow */
        .main-content {
          position: relative;
          z-index: 10;
          background: white;
          border-bottom-left-radius: 2.5rem;
          border-bottom-right-radius: 2.5rem;
          box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
          /* Penting: ini yang membuat footer "terungkap" */
          margin-bottom: 0;
        }

        /* Footer diam di belakang, terungkap saat main content habis */
        .footer-reveal {
          position: sticky;
          bottom: 0;
          z-index: 0;
        }

        /* Transisi smooth border radius saat scroll */
        .main-content {
          transition: border-radius 0.3s ease;
        }
    </style>
</head>
<body class="bg-white text-brand-black antialiased selection:bg-brand-green selection:text-white">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/90 border-b border-transparent transition-all duration-300" id="navbar-header">
        <div class="max-w-7xl mx-auto px-6 md:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="flex items-center space-x-2 group">
                        <span class="font-black text-xl tracking-tight text-brand-black">
                            Hidro<span class="text-brand-green">Nutri</span>
                        </span>
                    </a>
                </div>
                
                <!-- Nav Links -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'text-brand-black font-semibold aktif' : 'text-brand-gray hover:text-brand-black' }}">
                        Dashboard
                    </a>
                    <a href="/rekomendasi" class="flex items-center gap-2 text-sm font-medium transition-colors duration-150 {{ request()->is('rekomendasi*') ? 'text-brand-black font-semibold' : 'text-brand-gray hover:text-brand-black' }}">
                        Rekomendasi
                    </a>
                    <a href="/hasil" class="flex items-center gap-2 text-sm font-medium transition-colors duration-150 {{ request()->is('hasil*') || request()->is('jadwal*') ? 'text-brand-black font-semibold' : 'text-brand-gray hover:text-brand-black' }}">
                        Hasil & Jadwal
                    </a>
                    <a href="/cek-kondisi" class="flex items-center gap-2 text-sm font-medium transition-colors duration-150 {{ request()->is('cek-kondisi*') ? 'text-brand-black font-semibold' : 'text-brand-gray hover:text-brand-black' }}">
                        Cek Kondisi
                    </a>
                    <a href="/riwayat" class="flex items-center gap-2 text-sm font-medium transition-colors duration-150 {{ request()->is('riwayat*') ? 'text-brand-black font-semibold' : 'text-brand-gray hover:text-brand-black' }}">
                        Riwayat
                    </a>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <button type="button" onclick="toggleMobileMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-brand-gray hover:text-brand-black hover:bg-brand-grayultra focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Buka menu utama</span>
                        <i class="fa-solid fa-bars text-xl" id="menu-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu, show/hide based on menu state. -->
        <div class="hidden md:hidden bg-white border-t border-brand-graylt transition-all duration-300" id="mobile-menu">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-brand-greenpal text-brand-green font-bold aktif' : 'text-brand-gray hover:bg-brand-grayultra hover:text-brand-black' }}">
                    Dashboard
                </a>
                <a href="/rekomendasi" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->is('rekomendasi*') ? 'bg-brand-greenpal text-brand-green font-bold' : 'text-brand-gray hover:bg-brand-grayultra hover:text-brand-black' }}">
                    Rekomendasi
                </a>
                <a href="/hasil" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->is('hasil*') || request()->is('jadwal*') ? 'bg-brand-greenpal text-brand-green font-bold' : 'text-brand-gray hover:bg-brand-grayultra hover:text-brand-black' }}">
                    Hasil & Jadwal
                </a>
                <a href="/cek-kondisi" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->is('cek-kondisi*') ? 'bg-brand-greenpal text-brand-green font-bold' : 'text-brand-gray hover:bg-brand-grayultra hover:text-brand-black' }}">
                    Cek Kondisi
                </a>
                <a href="/riwayat" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->is('riwayat*') ? 'bg-brand-greenpal text-brand-green font-bold' : 'text-brand-gray hover:bg-brand-grayultra hover:text-brand-black' }}">
                    Riwayat
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content relative bg-white z-10 rounded-b-[2.5rem] min-h-screen shadow-[0_20px_60px_rgba(0,0,0,0.15)]">
        @hasSection('no-container')
            @yield('content')
        @else
            <div class="max-w-7xl w-full mx-auto px-6 md:px-8 py-12 md:py-16">
                <!-- Toast Notification (Laravel Session Success/Error) -->
                @if(session('success'))
                    <div class="mb-6 flex items-center p-4 text-brand-green border border-brand-greenlt/20 bg-brand-greenpal rounded-xl animate-fade-in" role="alert">
                        <i class="fa-solid fa-circle-check text-brand-green text-lg mr-3"></i>
                        <div class="text-sm font-semibold">{{ session('success') }}</div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 flex items-center p-4 text-red-700 border border-red-200 bg-red-50 rounded-xl animate-fade-in" role="alert">
                        <i class="fa-solid fa-circle-xmark text-red-500 text-lg mr-3"></i>
                        <div class="text-sm font-semibold">{{ session('error') }}</div>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="mb-6 flex items-center p-4 text-brand-amber border border-brand-amber/20 bg-amber-50 rounded-xl animate-fade-in" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-brand-amber text-lg mr-3"></i>
                        <div class="text-sm font-semibold">{{ session('warning') }}</div>
                    </div>
                @endif

                @yield('content')
            </div>
        @endif
    </main>

    <!-- Footer tersembunyi di belakang main content -->
    <!-- Position sticky di bawah, terungkap saat scroll habis -->
    <footer class="footer-reveal bg-brand-black sticky bottom-0 z-0 border-t-0">
      
      <div class="max-w-7xl mx-auto px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
          
          <!-- Kolom 1: Brand -->
          <div class="md:col-span-2">
            <div class="flex items-center gap-2 mb-4">
              <span class="text-2xl">🌿</span>
              <span class="text-xl font-black text-white">
                Hidro<span class="text-brand-moss">Nutri</span>
              </span>
            </div>
            <p class="text-sm text-white/50 leading-relaxed max-w-xs">
              Sistem pakar rekomendasi dan monitoring nutrisi 
              tanaman hidroponik berbasis Rule-Based Reasoning 
              dari literatur ilmiah.
            </p>
            <div class="flex gap-2 flex-wrap mt-6">
              <span class="bg-white/10 text-white/60 text-xs 
                           px-3 py-1 rounded-full">Laravel 11</span>
              <span class="bg-white/10 text-white/60 text-xs 
                           px-3 py-1 rounded-full">Tailwind CSS</span>
              <span class="bg-white/10 text-white/60 text-xs 
                           px-3 py-1 rounded-full">PostgreSQL</span>
              <span class="bg-white/10 text-white/60 text-xs 
                           px-3 py-1 rounded-full">Rule-Based Reasoning</span>
            </div>
          </div>
          
          <!-- Kolom 2: Navigasi -->
          <div>
            <h4 class="text-xs font-semibold uppercase tracking-widest 
                       text-white/40 mb-5">Navigasi</h4>
            <ul class="space-y-3">
              <li><a href="{{ route('dashboard') }}" 
                     class="text-sm text-white/70 hover:text-white 
                            transition-colors duration-150 
                            hover:translate-x-1 inline-block">
                Dashboard</a></li>
              <li><a href="{{ route('rekomendasi') }}" 
                     class="text-sm text-white/70 hover:text-white 
                            transition-colors duration-150
                            hover:translate-x-1 inline-block">
                Rekomendasi</a></li>
              <li><a href="{{ route('cek-kondisi') }}" 
                     class="text-sm text-white/70 hover:text-white 
                            transition-colors duration-150
                            hover:translate-x-1 inline-block">
                Cek Kondisi</a></li>
              <li><a href="/hasil#jadwal" 
                     class="text-sm text-white/70 hover:text-white 
                            transition-colors duration-150
                            hover:translate-x-1 inline-block">
                Jadwal</a></li>
              <li><a href="{{ route('riwayat') }}" 
                     class="text-sm text-white/70 hover:text-white 
                            transition-colors duration-150
                            hover:translate-x-1 inline-block">
                Riwayat</a></li>
            </ul>
          </div>
          
          <!-- Kolom 3: Tentang -->
          <div>
            <h4 class="text-xs font-semibold uppercase tracking-widest 
                       text-white/40 mb-5">Tentang</h4>
            <ul class="space-y-3">
              <li><span class="text-sm text-white/70">
                Sistem Pakar</span></li>
              <li><span class="text-sm text-white/70">
                Rule-Based Reasoning</span></li>
              <li><span class="text-sm text-white/70">
                Basis Pengetahuan</span></li>
              <li><span class="text-sm text-white/70">
                Literatur Ilmiah</span></li>
            </ul>
          </div>
          
        </div>
      </div>
      
      <!-- Bottom bar -->
      <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-8 py-5 
                    flex flex-col md:flex-row 
                    justify-between items-center gap-3">
          <span class="text-xs text-white/40">
            © HidroNutri 2026. Tugas Akhir Kecerdasan Buatan.
          </span>
          <span class="text-xs text-white/40">
            Berbasis Rule-Based Reasoning dari literatur ilmiah
          </span>
        </div>
      </div>
      
    </footer>

    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>
    <div class="page-progress" id="pageProgress"></div>

    <!-- Vanilla JS for layout logic -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('navbar-header');
            if (window.scrollY > 10) {
                header.classList.remove('border-transparent');
                header.classList.add('border-brand-graylt');
            } else {
                header.classList.remove('border-brand-graylt');
                header.classList.add('border-transparent');
            }
        });

        // ─── GSAP GLOBAL ANIMATIONS ────────────────────────────────
        gsap.registerPlugin(ScrollTrigger, ScrollToPlugin, TextPlugin);

        // ─── 1. PAGE ENTER ANIMATION ────────────────────────────────
        gsap.to('.page-wrapper', {
          opacity: 1,
          duration: 0.5,
          ease: 'power2.out'
        });

        // ─── 2. NAVBAR SCROLL EFFECT ────────────────────────────────
        ScrollTrigger.create({
          start: 'top -60',
          onEnter: () => document.getElementById('navbar-header')
                                  .classList.add('navbar-scrolled'),
          onLeaveBack: () => document.getElementById('navbar-header')
                                      .classList.remove('navbar-scrolled'),
        });

        // ─── 3. SCROLL PROGRESS BAR ─────────────────────────────────
        gsap.to('#pageProgress', {
          width: '100%',
          ease: 'none',
          scrollTrigger: {
            trigger: 'body',
            start: 'top top',
            end: 'bottom bottom',
            scrub: 0.3,
          }
        });

        // ─── 4. CURSOR FOLLOWER (desktop only) ──────────────────────
        if (window.innerWidth > 768) {
          const dot  = document.getElementById('cursorDot');
          const ring = document.getElementById('cursorRing');
          
          window.addEventListener('mousemove', e => {
            gsap.to(dot,  { x: e.clientX - 4,  y: e.clientY - 4,  duration: 0.1 });
            gsap.to(ring, { x: e.clientX - 16, y: e.clientY - 16, duration: 0.4, ease: 'power2.out' });
          });
          
          // Ring membesar saat hover link/button
          document.querySelectorAll('a, button, .cursor-grow').forEach(el => {
            el.addEventListener('mouseenter', () => {
              gsap.to(ring, { scale: 2, opacity: 0.3, duration: 0.3 });
              gsap.to(dot,  { scale: 0, duration: 0.3 });
            });
            el.addEventListener('mouseleave', () => {
              gsap.to(ring, { scale: 1, opacity: 0.5, duration: 0.3 });
              gsap.to(dot,  { scale: 1, duration: 0.3 });
            });
          });
        }

        // ─── 5. PAGE EXIT ANIMATION ─────────────────────────────────
        document.querySelectorAll('a[href]').forEach(link => {
          const href = link.getAttribute('href');
          if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
          
          // Skip link eksternal
          if (link.hostname !== window.location.hostname) return;
          
          link.addEventListener('click', function(e) {
            e.preventDefault();
            gsap.to('.page-wrapper', {
              opacity: 0,
              y: -20,
              duration: 0.3,
              ease: 'power2.in',
              onComplete: () => window.location.href = href
            });
          });
        });

        // ─── 6. UNIVERSAL SCROLL ANIMATIONS ─────────────────────────
        // Semua elemen dengan class gsap-up
        gsap.utils.toArray('.gsap-up').forEach((el, i) => {
          gsap.to(el, {
            opacity: 1, y: 0,
            duration: 0.7,
            ease: 'power3.out',
            delay: i * 0.05,
            scrollTrigger: {
              trigger: el,
              start: 'top 88%',
              toggleActions: 'play none none none'
            }
          });
        });

        // Semua elemen dengan class gsap-left
        gsap.utils.toArray('.gsap-left').forEach(el => {
          gsap.to(el, {
            opacity: 1, x: 0,
            duration: 0.7,
            ease: 'power3.out',
            scrollTrigger: { trigger: el, start: 'top 88%' }
          });
        });

        // Semua elemen dengan class gsap-right
        gsap.utils.toArray('.gsap-right').forEach(el => {
          gsap.to(el, {
            opacity: 1, x: 0,
            duration: 0.7,
            ease: 'power3.out',
            scrollTrigger: { trigger: el, start: 'top 88%' }
          });
        });

        // Semua elemen dengan class gsap-scale
        gsap.utils.toArray('.gsap-scale').forEach(el => {
          gsap.to(el, {
            opacity: 1, scale: 1,
            duration: 0.6,
            ease: 'back.out(1.4)',
            scrollTrigger: { trigger: el, start: 'top 88%' }
          });
        });

        // Staggered children (parent punya class gsap-stagger)
        gsap.utils.toArray('.gsap-stagger').forEach(parent => {
          const children = parent.children;
          gsap.fromTo(children, 
            { opacity: 0, y: 30 },
            {
              opacity: 1,
              y: 0,
              duration: 0.6,
              ease: 'power3.out',
              stagger: 0.1,
              scrollTrigger: { trigger: parent, start: 'top 85%' }
            }
          );
        });

        // Smooth scroll untuk semua anchor link internal
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
          anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
              gsap.to(window, {
                scrollTo: { y: target, offsetY: 80 },
                duration: 1,
                ease: 'power3.inOut'
              });
            }
          });
        });

        // ─── 7. MARQUEE SECTION & FOOTER ANIMATIONS ─────────────────
        // Marquee section fade in
        if (document.querySelector('.marquee-section')) {
          gsap.from('.marquee-section', {
            opacity: 0, y: 40,
            duration: 0.8,
            ease: 'power3.out',
            scrollTrigger: {
              trigger: '.marquee-section',
              start: 'top 90%'
            }
          });
        }

        // Deteksi saat footer mulai terlihat
        ScrollTrigger.create({
          trigger: '.footer-reveal',
          start: 'top bottom',
          end: 'top 50%',
          onEnter: () => {
            // Perkecil border radius main content saat footer mulai muncul
            gsap.to('.main-content', {
              borderBottomLeftRadius: '0px',
              borderBottomRightRadius: '0px',
              duration: 0.4,
              ease: 'power2.out'
            });
          },
          onLeaveBack: () => {
            // Kembalikan border radius saat scroll ke atas
            gsap.to('.main-content', {
              borderBottomLeftRadius: '2.5rem',
              borderBottomRightRadius: '2.5rem',
              duration: 0.4,
              ease: 'power2.out'
            });
          }
        });

        // Animasi konten footer saat muncul
        gsap.from('.footer-reveal .grid > div', {
          y: 30,
          opacity: 0,
          stagger: 0.1,
          duration: 0.6,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '.footer-reveal',
            start: 'top 80%',
          }
        });

        // Link footer hover dengan GSAP
        document.querySelectorAll('footer a').forEach(link => {
          link.addEventListener('mouseenter', () => {
            gsap.to(link, { x: 4, duration: 0.2, ease: 'power2.out' });
          });
          link.addEventListener('mouseleave', () => {
            gsap.to(link, { x: 0, duration: 0.2 });
          });
        });
    </script>
</body>
</html>

