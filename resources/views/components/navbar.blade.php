{{-- Bilah navigasi utama, ditampilkan di setiap halaman --}}
<header class="sticky top-0 z-50 backdrop-blur-md bg-white/90 border-b border-transparent transition-all duration-300" id="navbar-header">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Tanda pengenal atau logo merek aplikasi -->
            <div class="flex-shrink-0 flex items-center">
                <a href="/" class="flex items-center space-x-2 group">
                    <span class="font-black text-xl tracking-tight text-brand-black">
                        Hidro<span class="text-brand-green">Nutri</span>
                    </span>
                </a>
            </div>
            
            <!-- Tautan navigasi untuk layar lebar -->
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

            <!-- Authentication Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                @if(Session::has('supabase_user'))
                    <span class="text-sm font-semibold text-brand-black mr-2">Halo, {{ Session::get('supabase_user')['user']['user_metadata']['name'] ?? 'User' }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Logout" class="flex items-center justify-center w-8 h-8 rounded-full text-brand-gray hover:text-red-500 hover:bg-red-50 transition-colors border border-transparent hover:border-red-100">
                            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-brand-gray hover:text-brand-black transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-bold text-white bg-brand-green hover:bg-brand-green/90 rounded-xl shadow-md shadow-brand-green/20 transition-all transform hover:-translate-y-0.5">
                        Daftar
                    </a>
                @endif
            </div>

            <!-- Tombol menu hamburger untuk tampilan layar kecil -->
            <div class="flex md:hidden">
                <button type="button" onclick="toggleMobileMenu()" class="inline-flex items-center justify-center p-2 rounded-md text-brand-gray hover:text-brand-black hover:bg-brand-grayultra focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Buka menu utama</span>
                    <i class="fa-solid fa-bars text-xl" id="menu-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Daftar menu layar kecil, tersembunyi secara default -->
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
            
            <div class="border-t border-brand-graylt my-2 pt-2">
                @if(Session::has('supabase_user'))
                    <div class="px-3 py-2 text-sm font-semibold text-brand-black">Halo, {{ Session::get('supabase_user')['user']['user_metadata']['name'] ?? 'User' }}</div>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 rounded-lg text-sm font-medium text-brand-gray hover:text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-brand-gray hover:bg-brand-grayultra hover:text-brand-black">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 mt-1 rounded-lg text-base font-bold text-center text-white bg-brand-green hover:bg-brand-green/90">
                        Daftar
                    </a>
                @endif
            </div>
        </div>
    </div>
</header>
