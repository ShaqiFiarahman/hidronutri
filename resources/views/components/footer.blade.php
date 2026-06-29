{{-- footer halaman, terungkap di akhir scroll --}}
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
          <span class="bg-white/10 text-white/60 text-xs px-3 py-1 rounded-full">Laravel 11</span>
          <span class="bg-white/10 text-white/60 text-xs px-3 py-1 rounded-full">Tailwind CSS</span>
          <span class="bg-white/10 text-white/60 text-xs px-3 py-1 rounded-full">PostgreSQL</span>
          <span class="bg-white/10 text-white/60 text-xs px-3 py-1 rounded-full">Rule-Based Reasoning</span>
        </div>
      </div>
      
      <!-- Kolom 2: Navigasi -->
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-5">Navigasi</h4>
        <ul class="space-y-3">
          <li><a href="{{ route('dashboard') }}" class="text-sm text-white/70 hover:text-white transition-colors duration-150 hover:translate-x-1 inline-block">Dashboard</a></li>
          <li><a href="{{ route('rekomendasi') }}" class="text-sm text-white/70 hover:text-white transition-colors duration-150 hover:translate-x-1 inline-block">Rekomendasi</a></li>
          <li><a href="{{ route('cek-kondisi') }}" class="text-sm text-white/70 hover:text-white transition-colors duration-150 hover:translate-x-1 inline-block">Cek Kondisi</a></li>
          <li><a href="/hasil#jadwal" class="text-sm text-white/70 hover:text-white transition-colors duration-150 hover:translate-x-1 inline-block">Jadwal</a></li>
          <li><a href="{{ route('riwayat') }}" class="text-sm text-white/70 hover:text-white transition-colors duration-150 hover:translate-x-1 inline-block">Riwayat</a></li>
        </ul>
      </div>
      
      <!-- Kolom 3: Tentang -->
      <div>
        <h4 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-5">Tentang</h4>
        <ul class="space-y-3">
          <li><span class="text-sm text-white/70">Sistem Pakar</span></li>
          <li><span class="text-sm text-white/70">Rule-Based Reasoning</span></li>
          <li><span class="text-sm text-white/70">Basis Pengetahuan</span></li>
          <li><span class="text-sm text-white/70">Literatur Ilmiah</span></li>
        </ul>
      </div>
      
    </div>
  </div>
  
  <!-- Bottom bar -->
  <div class="border-t border-white/10">
    <div class="max-w-7xl mx-auto px-8 py-5 flex flex-col md:flex-row justify-between items-center gap-3">
      <span class="text-xs text-white/40">© HidroNutri 2026. Tugas Akhir Kecerdasan Buatan.</span>
      <span class="text-xs text-white/40">Berbasis Rule-Based Reasoning dari literatur ilmiah</span>
    </div>
  </div>
  
</footer>
