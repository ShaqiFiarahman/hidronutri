{{-- navigasi utama, muncul di semua halaman --}}
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
