<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HidroNutri - Sistem Pakar Hidroponik')</title>
    
    <!-- Pemuatan Tailwind CSS menggunakan Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Pemuatan FontAwesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Pemuatan pustaka inti animasi GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <!-- Plugin GSAP untuk memicu animasi saat menggulir halaman -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <!-- Plugin GSAP untuk animasi gulir halaman ke elemen tertentu -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>
    <!-- Plugin GSAP untuk efek animasi teks -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/TextPlugin.min.js"></script>

    <!-- Gaya tata letak kustom yang dipindahkan ke css/pages/layout.css -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F7FAF2; /* offwhite */
        }
    </style>
</head>
<body class="bg-white text-brand-black antialiased selection:bg-brand-green selection:text-white">
    {{-- Bilah navigasi utama halaman --}}
    <x-navbar />

    <!-- Konten utama halaman -->
    <main class="main-content relative bg-white z-10 rounded-b-[2.5rem] min-h-screen shadow-[0_20px_60px_rgba(0,0,0,0.15)]">
        @hasSection('no-container')
            @yield('content')
        @else
            <div class="max-w-7xl w-full mx-auto px-6 md:px-8 py-12 md:py-16">
                <!-- Notifikasi pesan aksi berhasil atau gagal -->
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

    {{-- Bagian bawah halaman --}}
    <x-footer />

    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>
    <div class="page-progress" id="pageProgress"></div>
    <!-- File skrip dipindahkan ke resources/js/pages/layout.js dan app.js -->
</body>
</html>

