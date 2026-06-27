<?php

namespace App\Http\Controllers;

use App\Models\SesiTanam;
use App\Models\RuleNutrisi;
use App\Models\Tanaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SesiTanamController extends Controller
{
    // Mapping durasi fase sekarang ada di App\Services\RuleBasedEngine

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanaman_id' => 'required|exists:tanaman,id',
            'sistem_hidroponik' => 'required|in:nft,dft,rakit_apung,wick',
            'fase_saat_ini' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date|before_or_equal:today',
        ]);

        // Validasi bahwa fase valid untuk tanaman ini
        $ruleExists = RuleNutrisi::where('tanaman_id', $validated['tanaman_id'])
            ->where('fase', $validated['fase_saat_ini'])
            ->exists();

        if (!$ruleExists) {
            return back()->withErrors(['fase_saat_ini' => 'Fase yang dipilih tidak tersedia untuk tanaman ini.'])->withInput();
        }

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

        $tanggalMulai = Carbon::parse($sesi->tanggal_mulai);
        $usiaHari = max(0, $tanggalMulai->diffInDays(Carbon::today()));
        
        // Auto-update fase berdasarkan usia aktual tanaman
        $engine = app(\App\Services\RuleBasedEngine::class);
        $faseDinamis = $engine->determineFase($sesi->tanaman->nama, $usiaHari);
        
        if ($sesi->fase_saat_ini !== $faseDinamis) {
            $sesi->update(['fase_saat_ini' => $faseDinamis]);
        }

        $rule = RuleNutrisi::where('tanaman_id', $sesi->tanaman_id)
            ->where('fase', $sesi->fase_saat_ini)
            ->first();

        // Ambil data durasi fase dari mapping referensi
        $durasiMap = $engine->getDurasiFaseMap();
        $namaTanaman = $sesi->tanaman->nama;
        $faseSaatIni = $sesi->fase_saat_ini;

        $fasesTanaman = $durasiMap[$namaTanaman] ?? null;
        $durasiTotal = 35; // Default fallback

        if ($fasesTanaman) {
            $lastFase = end($fasesTanaman);
            $durasiTotal = $lastFase['kumulatif'];
        }
        
        $usiaTotalTanaman = $usiaHari;

        // Hitung progress berdasarkan total usia tanaman secara keseluruhan
        $progressPersen = min(100, ($usiaTotalTanaman / max(1, $durasiTotal)) * 100);

        // Estimasi pindah fase
        $estimasiPindahFase = '';
        if ($fasesTanaman && isset($fasesTanaman[$faseSaatIni])) {
            $faseKeys = array_keys($fasesTanaman);
            $currentIdx = array_search($faseSaatIni, $faseKeys);
            $nextIdx = $currentIdx + 1;

            if ($nextIdx < count($faseKeys)) {
                $nextFaseKey = $faseKeys[$nextIdx];
                $nextFaseLabel = ucwords(str_replace('_', ' ', $nextFaseKey));
                $kumulatifFaseIni = $fasesTanaman[$faseSaatIni]['kumulatif'];
                
                $sisaHari = max(0, $kumulatifFaseIni - $usiaTotalTanaman);
                
                $estimasiPindahFase = $sisaHari > 0
                    ? "{$sisaHari} hari lagi ke Fase {$nextFaseLabel}"
                    : "Siap pindah ke Fase {$nextFaseLabel}";
            } else {
                $estimasiPindahFase = "Tanaman siap dipanen kapan saja!";
            }
        } else {
            $estimasiPindahFase = "Data durasi fase belum tersedia.";
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
                    'judul' => 'Cek Kondisi Air (pH, EC, PPM, Suhu)',
                    'deskripsi' => "Target: pH {$rule->ph_min}-{$rule->ph_max}, EC {$rule->ec_min}-{$rule->ec_max} mS/cm, PPM {$rule->ppm_min}-{$rule->ppm_max}, Suhu Air {$rule->suhu_min}-{$rule->suhu_max}°C."
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

            // Sesuai dengan rule sistem DFT/Hidroponik: 
            // Penggantian larutan nutrisi hanya 1x per siklus panen. 
            // Selama siklus aktif (belum panen/selesai), cukup tambah air & nutrisi saat berkurang (Isi Ulang).
            // Oleh karena itu, kegiatan rutin 'Ganti Total Larutan' dihapus dari jadwal mingguan.
            
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

        if ($fasesTanaman) {
            $faseKeys = array_keys($fasesTanaman);
            $currentIdx = array_search($faseSaatIni, $faseKeys);
            $nextIdx = $currentIdx + 1;

            if ($nextIdx < count($faseKeys)) {
                $nextFaseKey = $faseKeys[$nextIdx];
                $faseBerikutnya = ucwords(str_replace('_', ' ', $nextFaseKey));

                // Catatan kontekstual per transisi fase
                $catatanMap = [
                    'vegetatif_awal' => 'Fase di mana daun sejati mulai tumbuh lebar. Kebutuhan EC dan PPM akan meningkat.',
                    'vegetatif_akhir' => 'Fase pembesaran batang dan rimbun daun. Tanaman rakus nutrisi, pastikan pasokan air lancar.',
                    'panen' => 'Persiapan panen. Untuk sayur daun, turunkan konsentrasi EC 3 hari sebelum panen agar rasa manis.',
                    'vegetatif' => 'Tanaman akan mulai tumbuh vegetatif. Naikkan konsentrasi nutrisi secara bertahap.',
                    'pembungaan' => 'Fase pembungaan dimulai. Tanaman butuh nutrisi lebih tinggi untuk pembentukan bunga.',
                    'pembuahan' => 'Fase pembuahan. Nutrisi pada titik tertinggi untuk pengisian buah.',
                    'pembesaran' => 'Fase pembesaran buah. Pastikan nutrisi mencukupi dan suhu air terjaga.',
                    'transisi' => 'Masa transisi menuju fase generatif. Naikkan konsentrasi PPM secara bertahap.',
                    'pematangan' => 'Fase pematangan buah. Kurangi nutrisi agar gula buah meningkat.',
                ];

                $catatanFaseBerikutnya = $catatanMap[$nextFaseKey] ?? 'Bersihkan modul hidroponik sebelum memulai siklus tanam baru.';
            } else {
                $faseBerikutnya = 'Selesai';
                $catatanFaseBerikutnya = 'Bersihkan modul hidroponik secara menyeluruh sebelum memulai siklus tanam baru agar steril dari spora jamur.';
            }
        }

        return view('pages.jadwal', compact(
            'sesi', 'rule', 'usiaHari', 'progressPersen', 
            'estimasiPindahFase', 'jadwalSeminggu', 'faseBerikutnya', 'catatanFaseBerikutnya'
        ));
    }
}
