@extends('layouts.app')

@section('title', 'Autentikasi - HidroNutri')
@section('no-container', true)

@section('content')
<style>
    /* Styling for the sliding container */
    .auth-container {
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 1000px;
        min-height: 600px;
        background-color: #fff;
        border-radius: 2rem;
        box-shadow: 0 14px 28px rgba(0,0,0,0.1), 0 10px 10px rgba(0,0,0,0.05);
    }
    .form-container {
        position: absolute;
        top: 0;
        height: 100%;
        transition: transform 0.6s ease-in-out;
        background-color: #fff;
    }
    .sign-in-container {
        left: 0;
        width: 50%;
        z-index: 2;
        opacity: 1;
        transition: transform 0.6s ease-in-out, opacity 0s 0.3s, z-index 0s 0.3s;
    }
    .sign-up-container {
        left: 0;
        width: 50%;
        opacity: 0;
        z-index: 1;
        transition: transform 0.6s ease-in-out, opacity 0s 0.3s, z-index 0s 0.3s;
    }
    
    .right-panel-active .sign-in-container {
        transform: translateX(100%);
        opacity: 0;
        z-index: 1;
    }
    .right-panel-active .sign-up-container {
        transform: translateX(100%);
        opacity: 1;
        z-index: 5;
    }
    
    .overlay-container {
        position: absolute;
        top: 0;
        left: 50%;
        width: 50%;
        height: 100%;
        overflow: hidden;
        transition: transform 0.6s ease-in-out;
        z-index: 100;
    }
    .right-panel-active .overlay-container {
        transform: translateX(-100%);
    }
    
    .overlay {
        background-color: #111827; /* brand-black */
        background-image: linear-gradient(135deg, rgba(16,185,129,0.7) 0%, rgba(17,24,39,0.95) 100%), url('https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?q=80&w=1969&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: #ffffff;
        position: relative;
        left: -100%;
        height: 100%;
        width: 200%;
        transform: translateX(0);
        transition: transform 0.6s ease-in-out;
    }

    .right-panel-active .overlay {
        transform: translateX(50%);
    }
    
    .overlay-panel {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 0 40px;
        text-align: center;
        top: 0;
        height: 100%;
        width: 50%;
        transform: translateX(0);
        transition: transform 0.6s ease-in-out;
        z-index: 2;
    }
    
    .overlay-left {
        transform: translateX(-20%);
    }
    .right-panel-active .overlay-left {
        transform: translateX(0);
    }
    
    .overlay-right {
        right: 0;
        transform: translateX(0);
    }
    .right-panel-active .overlay-right {
        transform: translateX(20%);
    }
    
    /* Mobile responsive overrides */
    @media (max-width: 1024px) {
        .auth-container {
            min-height: auto;
            border-radius: 1.5rem;
            display: flex;
            flex-direction: column;
            overflow: visible;
            box-shadow: none;
            background-color: transparent;
        }
        .form-container, .overlay-container {
            position: relative;
            width: 100% !important;
            height: auto !important;
            top: auto;
            left: auto;
            transform: none !important;
            transition: none;
        }
        .sign-in-container, .sign-up-container {
            opacity: 1 !important;
            z-index: 1 !important;
            animation: none !important;
            background-color: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        /* Show/hide panels on mobile instead of sliding */
        .right-panel-active .sign-in-container {
            display: none;
        }
        .sign-up-container {
            display: none;
        }
        .right-panel-active .sign-up-container {
            display: block;
        }
        .overlay-container {
            display: none; /* Hide overlay image on mobile to save space */
        }
    }
    
    /* Input styles */
    .input-field {
        background-color: #f3f4f6;
        border: 2px solid transparent;
        padding: 12px 15px 12px 40px;
        border-radius: 1rem;
        width: 100%;
        font-weight: 600;
        transition: all 0.3s;
    }
    .input-field:focus {
        background-color: #fff;
        border-color: #10B981;
        outline: none;
        box-shadow: 0 0 0 4px rgba(16,185,129,0.1);
    }
</style>

<div class="h-[calc(100vh-4rem)] w-full flex items-center justify-center p-4 md:p-8 bg-brand-grayultra overflow-y-auto lg:overflow-hidden">

    <div class="auth-container {{ $mode == 'register' ? 'right-panel-active' : '' }}" id="container">
        
        <!-- =======================
             REGISTER FORM (Panel Kiri tersembunyi)
             ======================= -->
        <div class="form-container sign-up-container px-8 md:px-12 flex flex-col justify-center">
            
            <div class="mb-6 lg:mb-8 text-center">
                <a href="/" class="inline-block mb-4 lg:hidden">
                    <span class="font-black text-2xl tracking-tight text-brand-black">
                        Hidro<span class="text-brand-green">Nutri</span>
                    </span>
                </a>
                <h1 class="text-3xl font-black text-brand-black tracking-tight mb-2">Buat Akun</h1>
                <p class="text-brand-gray text-xs md:text-sm">Mulai petualangan bertani cerdas Anda</p>
            </div>

            @if(session('warning') && $mode == 'register')
                <div class="mb-4 flex items-start p-3 text-brand-amber border border-brand-amber/30 bg-amber-50 rounded-xl" role="alert">
                    <i class="fa-solid fa-circle-exclamation text-brand-amber mt-0.5 mr-2"></i>
                    <div class="text-xs font-semibold">{{ session('warning') }}</div>
                </div>
            @endif
            @if(session('error') && $mode == 'register')
                <div class="mb-4 flex items-start p-3 text-red-700 border border-red-200 bg-red-50 rounded-xl" role="alert">
                    <i class="fa-solid fa-circle-xmark text-red-500 mt-0.5 mr-2"></i>
                    <div class="text-xs font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-3 lg:space-y-4">
                @csrf
                <div class="relative">
                    <i class="fa-solid fa-user absolute top-3.5 left-4 text-brand-gray/50"></i>
                    <input type="text" name="name" placeholder="Nama Lengkap" class="input-field text-sm" value="{{ old('name') }}" required>
                    @error('name')
                        <p class="mt-1 text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute top-3.5 left-4 text-brand-gray/50"></i>
                    <input type="email" name="email" placeholder="Email" class="input-field text-sm" value="{{ old('email') }}" required>
                    @error('email')
                        <p class="mt-1 text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute top-3.5 left-4 text-brand-gray/50"></i>
                    <input type="password" name="password" placeholder="Kata Sandi (Min 6)" class="input-field text-sm" required>
                    @error('password')
                        <p class="mt-1 text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <i class="fa-solid fa-check-double absolute top-3.5 left-4 text-brand-gray/50"></i>
                    <input type="password" name="password_confirmation" placeholder="Ulangi Kata Sandi" class="input-field text-sm" required>
                </div>
                <button type="submit" class="w-full mt-2 py-3.5 px-4 rounded-xl shadow-lg shadow-brand-green/20 text-sm font-extrabold text-white bg-brand-green hover:bg-brand-black transition-all duration-300">
                    Daftar Sekarang
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs text-brand-gray lg:hidden">
                Sudah punya akun? 
                <button onclick="togglePanel()" class="text-brand-green font-bold ml-1">Masuk di sini</button>
            </div>
        </div>
        
        <!-- =======================
             LOGIN FORM (Panel Kiri default)
             ======================= -->
        <div class="form-container sign-in-container px-8 md:px-12 flex flex-col justify-center">
            
            <div class="mb-6 lg:mb-8 text-center">
                <a href="/" class="inline-block mb-4 lg:hidden">
                    <span class="font-black text-2xl tracking-tight text-brand-black">
                        Hidro<span class="text-brand-green">Nutri</span>
                    </span>
                </a>
                <h1 class="text-3xl font-black text-brand-black tracking-tight mb-2">Selamat Datang</h1>
                <p class="text-brand-gray text-xs md:text-sm">Masuk untuk mengelola kebun Anda</p>
            </div>

            @if(session('success'))
                <div class="mb-4 flex items-start p-3 text-brand-green border border-brand-greenlt/30 bg-brand-greenpal/50 rounded-xl" role="alert">
                    <i class="fa-solid fa-circle-check text-brand-green mt-0.5 mr-2"></i>
                    <div class="text-xs font-semibold">{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error') && $mode == 'login')
                <div class="mb-4 flex items-start p-3 text-red-700 border border-red-200 bg-red-50 rounded-xl" role="alert">
                    <i class="fa-solid fa-circle-xmark text-red-500 mt-0.5 mr-2"></i>
                    <div class="text-xs font-semibold">{{ session('error') }}</div>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute top-3.5 left-4 text-brand-gray/50"></i>
                    <input type="email" name="email" placeholder="Email" class="input-field text-sm" value="{{ old('email') }}" required>
                    @error('email')
                        <p class="mt-1 text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute top-3.5 left-4 text-brand-gray/50"></i>
                    <input type="password" name="password" placeholder="Kata Sandi" class="input-field text-sm" required>
                    @error('password')
                        <p class="mt-1 text-[10px] text-red-500 font-bold ml-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="text-right">
                    <a href="#" class="text-[10px] font-bold text-brand-gray hover:text-brand-green transition-colors">Lupa sandi?</a>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl shadow-lg shadow-brand-green/20 text-sm font-extrabold text-white bg-brand-green hover:bg-brand-black transition-all duration-300">
                    Masuk Sekarang
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs text-brand-gray lg:hidden">
                Belum punya akun? 
                <button onclick="togglePanel()" class="text-brand-green font-bold ml-1">Daftar sekarang</button>
            </div>
        </div>

        <!-- =======================
             OVERLAY PANELS (Bagian penutup yg bergeser)
             ======================= -->
        <div class="overlay-container hidden lg:block">
            <div class="overlay">
                <!-- Panel Register (ditampilkan saat posisi form Login terbuka) -->
                <div class="overlay-panel overlay-left">
                    <h2 class="text-3xl font-black mb-4">Sudah Terdaftar?</h2>
                    <p class="text-sm font-medium mb-8 text-white/80 leading-relaxed px-6">
                        Jika Anda sudah memiliki akun HidroNutri, langsung masuk untuk melihat rekomendasi terbaru.
                    </p>
                    <button class="border-2 border-white rounded-full px-10 py-3 text-sm font-bold uppercase tracking-wider hover:bg-white hover:text-brand-black transition-colors" id="signInBtn">
                        Masuk
                    </button>
                </div>
                
                <!-- Panel Login (ditampilkan saat posisi form Register terbuka) -->
                <div class="overlay-panel overlay-right">
                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mb-6 border border-white/30">
                        <i class="fa-solid fa-leaf text-2xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-black mb-4">Halo, Kawan!</h2>
                    <p class="text-sm font-medium mb-8 text-white/80 leading-relaxed px-6">
                        Daftarkan diri Anda sekarang dan jadikan kebun hidroponik Anda lebih subur dari sebelumnya.
                    </p>
                    <button class="border-2 border-white rounded-full px-10 py-3 text-sm font-bold uppercase tracking-wider hover:bg-white hover:text-brand-black transition-colors" id="signUpBtn">
                        Daftar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const signUpButton = document.getElementById('signUpBtn');
    const signInButton = document.getElementById('signInBtn');
    const container = document.getElementById('container');

    if (signUpButton && signInButton) {
        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
            // Mengubah URL tanpa reload halaman agar pas dengan logika backend error handling
            window.history.pushState({}, '', '/register');
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
            window.history.pushState({}, '', '/login');
        });
    }

    // Fungsi untuk mode mobile
    function togglePanel() {
        if (container.classList.contains("right-panel-active")) {
            container.classList.remove("right-panel-active");
            window.history.pushState({}, '', '/login');
        } else {
            container.classList.add("right-panel-active");
            window.history.pushState({}, '', '/register');
        }
    }
    
    // Hide custom mobile back button if it exists
    document.addEventListener('DOMContentLoaded', () => {
        if (window.innerWidth >= 1024) {
            // Allow body to be perfectly unscrollable
            document.body.style.overflow = 'hidden';
            
            // Remove main padding/margin if any
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.style.padding = '0';
                mainContent.style.borderRadius = '0';
                mainContent.style.boxShadow = 'none';
            }
        }
    });
</script>
@endsection
