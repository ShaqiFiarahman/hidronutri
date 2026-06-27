<?php

namespace App\Http\Controllers;

use App\Models\SesiTanam;
use App\Models\RuleNutrisi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SesiTanamController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanaman_id' => 'required|exists:tanaman,id',
            'sistem_hidroponik' => 'required|in:nft,dft,rakit_apung,wick',
            'fase_saat_ini' => 'required|in:semai,vegetatif_awal,vegetatif_akhir,panen',
            'tanggal_mulai' => 'required|date|before_or_equal:today',
        ]);

        // Cek jika ada sesi tanam aktif sebelumnya, selesaikan secara otomatis (atau boleh ada banyak, tapi untuk kemudahan monitoring, kita selesaikan sesi aktif sebelumnya)
        SesiTanam::where('status', 'aktif')->update(['status' => 'panen']);

        $sesi = SesiTanam::create([
            'tanaman_id' => $validated['tanaman_id'],
            'sistem_hidroponik' => $validated['sistem_hidroponik'],
            'fase_saat_ini' => $validated['fase_saat_ini'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'status' => 'aktif',
        ]);

        // Simpan juga ke session rekomendasi untuk sinkronisasi halaman cek-kondisi
        session([
            'rekomendasi_tanaman_id' => $sesi->tanaman_id,
            'rekomendasi_fase' => $sesi->fase_saat_ini,
            'rekomendasi_sistem' => $sesi->sistem_hidroponik,
        ]);

        return redirect('/jadwal')->with('success', 'Sesi tanam baru berhasil dimulai!');
    }

    public function panen($id)
    {
        $sesi = SesiTanam::findOrFail($id);
        $sesi->update(['status' => 'panen']);

        return redirect('/riwayat')->with('success', 'Selamat! Sesi tanam berhasil dipanen.');
    }

    public function jadwal()
    {
        $sesi = SesiTanam::where('status', 'aktif')->with('tanaman')->latest()->first();

        if (!$sesi) {
            return view('pages.jadwal', ['sesi' => null]);
        }

        $rule = RuleNutrisi::where('tanaman_id', $sesi->tanaman_id)
            ->where('fase', $sesi->fase_saat_ini)
            ->first();

        $tanggalMulai = Carbon::parse($sesi->tanggal_mulai);
        $usiaHari = max(0, $tanggalMulai->diffInDays(Carbon::today()));

        // Estimasi durasi per fase (asumsi standar hortikultura)
        $durasiSemai = 7;
        $durasiVegAwal = 7;
        $durasiVegAkhir = 14;
        $durasiTotal = 35; // Semai (7) + Veg Awal (7) + Veg Akhir (14) + Panen (7)

        // Hitung progress berdasarkan fase saat ini
        $progressPersen = 0;
        $estimasiPindahFase = '';

        switch ($sesi->fase_saat_ini) {
            case 'semai':
                $progressPersen = min(100, ($usiaHari / $durasiSemai) * 100);
                $sisaHari = max(0, $durasiSemai - $usiaHari);
                $estimasiPindahFase = $sisaHari > 0 
                    ? "$sisaHari hari lagi ke Fase Vegetatif Awal" 
                    : "Siap pindah ke Fase Vegetatif Awal";
                break;
            case 'vegetatif_awal':
                $progressPersen = min(100, (($usiaHari) / ($durasiSemai + $durasiVegAwal)) * 100);
                // Asumsi vegetatif awal selesai pada hari ke-14 sejak tanam
                $sisaHari = max(0, 14 - $usiaHari);
                $estimasiPindahFase = $sisaHari > 0 
                    ? "$sisaHari hari lagi ke Fase Vegetatif Akhir" 
                    : "Siap pindah ke Fase Vegetatif Akhir";
                break;
            case 'vegetatif_akhir':
                $progressPersen = min(100, (($usiaHari) / ($durasiSemai + $durasiVegAwal + $durasiVegAkhir)) * 100);
                // Asumsi vegetatif akhir selesai pada hari ke-28 sejak tanam
                $sisaHari = max(0, 28 - $usiaHari);
                $estimasiPindahFase = $sisaHari > 0 
                    ? "$sisaHari hari lagi ke Fase Panen" 
                    : "Siap pindah ke Fase Panen";
                break;
            case 'panen':
                $progressPersen = 100;
                $estimasiPindahFase = "Tanaman siap dipanen kapan saja!";
                break;
        }

        // Generate Jadwal Perawatan Mingguan (7 hari ke depan)
        $jadwalSeminggu = [];
        $hariNama = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        for ($i = 0; $i < 7; $i++) {
            $hariTarget = Carbon::today()->addDays($i);
            $totalHariSejakMulai = $usiaHari + $i;
            $namaHari = $hariNama[$hariTarget->dayOfWeek];
            $tanggalFormat = $hariTarget->translatedFormat('d M Y');

            $kegiatan = [];

            // Evaluasi rule cek pH/EC
            $intervalCek = $rule->cek_ph_ec ?? 1;
            if ($totalHariSejakMulai % $intervalCek === 0) {
                $kegiatan[] = [
                    'tipe' => 'cek',
                    'judul' => 'Cek & Kalibrasi pH/EC/PPM',
                    'deskripsi' => "Target: pH {$rule->ph_min}-{$rule->ph_max}, EC {$rule->ec_min}-{$rule->ec_max} mS/cm, PPM {$rule->ppm_min}-{$rule->ppm_max}."
                ];
            }

            // Evaluasi rule isi ulang nutrisi
            $intervalIsiUlang = $rule->isi_ulang ?? 2;
            if ($totalHariSejakMulai % $intervalIsiUlang === 0) {
                $kegiatan[] = [
                    'tipe' => 'isi_ulang',
                    'judul' => 'Isi Ulang Air & Nutrisi',
                    'deskripsi' => "Tambahkan air bersih jika volume menyusut. Dosis pupuk: A = {$rule->dosis_a} ml/L, B = {$rule->dosis_b} ml/L."
                ];
            }

            // Evaluasi rule ganti larutan total
            $intervalGanti = $rule->ganti_larutan ?? 7;
            if ($totalHariSejakMulai % $intervalGanti === 0) {
                $kegiatan[] = [
                    'tipe' => 'ganti',
                    'judul' => 'Kuras & Ganti Total Larutan',
                    'deskripsi' => 'Kuras wadah penampungan, bersihkan kerak lumut, ganti dengan larutan nutrisi baru yang segar.'
                ];
            }

            if (!empty($kegiatan)) {
                $jadwalSeminggu[] = [
                    'hari' => $namaHari,
                    'tanggal' => $tanggalFormat,
                    'kegiatan' => $kegiatan
                ];
            }
        }

        // Tentukan fase berikutnya untuk card info
        $faseBerikutnya = '';
        $catatanFaseBerikutnya = '';
        switch ($sesi->fase_saat_ini) {
            case 'semai':
                $faseBerikutnya = 'Vegetatif Awal';
                $catatanFaseBerikutnya = 'Fase di mana daun sejati mulai tumbuh lebar. Kebutuhan EC dan PPM akan meningkat hampir dua kali lipat.';
                break;
            case 'vegetatif_awal':
                $faseBerikutnya = 'Vegetatif Akhir';
                $catatanFaseBerikutnya = 'Fase pembesaran batang dan rimbun daun. Tanaman rakus nutrisi, pastikan pasokan air lancar.';
                break;
            case 'vegetatif_akhir':
                $faseBerikutnya = 'Panen';
                $catatanFaseBerikutnya = 'Persiapan panen. Untuk sayur daun seperti selada, turunkan konsentrasi EC 3 hari sebelum panen agar rasa sayur manis dan tidak pahit.';
                break;
            case 'panen':
                $faseBerikutnya = 'Selesai';
                $catatanFaseBerikutnya = 'Bersihkan modul hidroponik secara menyeluruh sebelum memulai siklus tanam baru agar steril dari spora jamur.';
                break;
        }

        return view('pages.jadwal', compact(
            'sesi', 'rule', 'usiaHari', 'progressPersen', 
            'estimasiPindahFase', 'jadwalSeminggu', 'faseBerikutnya', 'catatanFaseBerikutnya'
        ));
    }
}
