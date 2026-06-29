import { initNavbar } from './components/navbar';
import { initFooter } from './components/footer';
import { initLayout } from './pages/layout';
import { initDashboard } from './pages/dashboard';
import { initCekKondisi } from './pages/cek-kondisi';
import { initHasil } from './pages/hasil';
import { initJadwal } from './pages/jadwal';
import { initRekomendasi } from './pages/rekomendasi';
import { initRiwayat } from './pages/riwayat';

// Inisialisasi komponen global
document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initFooter();
    initLayout();

    // Halaman spesifik
    if (document.querySelector('.hero-section')) {
        initDashboard();
    }
    if (document.querySelector('.cek-header')) {
        initCekKondisi();
    }
    if (document.querySelector('#calendar-container')) {
        initHasil();
    }
    if (document.querySelector('.jadwal-header')) {
        initJadwal();
    }
    if (document.querySelector('.tanaman-card')) {
        initRekomendasi();
    }
    if (document.querySelector('.riwayat-header')) {
        initRiwayat();
    }
});
