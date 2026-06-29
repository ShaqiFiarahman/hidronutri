<?php

namespace App\Http\Controllers;

use App\Models\Tanaman;
use App\Models\SesiTanam;
use App\Models\RuleNutrisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\RuleBasedEngine;

class RekomendasiController extends Controller
{
    protected $engine;

    public function __construct(RuleBasedEngine $engine)
    {
        $this->engine = $engine;
    }

    public function index()
    {
        $tanaman = Tanaman::all();

        // Ambil daftar fase per tanaman dari rule_nutrisi
        $fasePerTanaman = \App\Models\RuleNutrisi::select('tanaman_id', 'fase')
            ->get()
            ->groupBy('tanaman_id')
            ->map(fn($rules) => $rules->pluck('fase')->values())
            ->toArray();

        // Map ID tanaman ke Nama untuk memudahkan JS
        $tanamanMap = $tanaman->pluck('nama', 'id')->toArray();

        // Ambil durasi map dari engine
        $durasiMap = $this->engine->getDurasiFaseMap();

        return view('pages.rekomendasi', compact('tanaman', 'fasePerTanaman', 'durasiMap', 'tanamanMap'));
    }

    public function proses(Request $request)
    {
        $validated = $request->validate([
            'tanaman_id' => 'required|exists:tanaman,id',
            'tanggal_mulai' => 'required|date|before_or_equal:today',
            'sistem_hidroponik' => 'required|in:nft,dft,rakit_apung,wick',
        ]);

        $tanaman = Tanaman::find($validated['tanaman_id']);
        $usiaHari = \Carbon\Carbon::parse($validated['tanggal_mulai'])->diffInDays(\Carbon\Carbon::today());
        
        // Tentukan fase secara dinamis
        $fase = $this->engine->determineFase($tanaman->nama, $usiaHari);

        // Validasi bahwa kombinasi tanaman + fase ada di rule_nutrisi
        $ruleExists = \App\Models\RuleNutrisi::where('tanaman_id', $validated['tanaman_id'])
            ->where('fase', $fase)
            ->exists();

        if (!$ruleExists) {
            return back()->withErrors(['tanaman_id' => 'Gagal menentukan fase yang valid untuk usia tanaman ini.'])->withInput();
        }

        // Otomatis simpan sebagai Sesi Tanam Aktif
        $sesi = SesiTanam::create([
            'tanaman_id' => $validated['tanaman_id'],
            'sistem_hidroponik' => $validated['sistem_hidroponik'],
            'fase_saat_ini' => $fase,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'status' => 'aktif',
        ]);

        session([
            'aktif_sesi_id' => $sesi->id,
            'rekomendasi_tanaman_id' => $validated['tanaman_id'],
            'rekomendasi_fase' => $fase,
            'rekomendasi_sistem' => $validated['sistem_hidroponik'],
            'rekomendasi_tanggal_mulai' => $validated['tanggal_mulai'],
            'rekomendasi_usia_hari' => $usiaHari,
        ]);

        return redirect('/hasil?sesi_id=' . $sesi->id);
    }

    public function hasil(Request $request)
    {
        if ($request->has('sesi_id')) {
            $sesi = SesiTanam::findOrFail($request->sesi_id);
            session([
                'aktif_sesi_id' => $sesi->id,
                'rekomendasi_tanaman_id' => $sesi->tanaman_id,
                'rekomendasi_fase' => $sesi->fase_saat_ini,
                'rekomendasi_sistem' => $sesi->sistem_hidroponik,
                'rekomendasi_tanggal_mulai' => $sesi->tanggal_mulai,
                'rekomendasi_usia_hari' => Carbon::parse($sesi->tanggal_mulai)->diffInDays(Carbon::today()),
            ]);
            return redirect('/hasil');
        }

        $tanamanId = session('rekomendasi_tanaman_id');
        $fase = session('rekomendasi_fase');
        $sistem = session('rekomendasi_sistem');
        $tanggalMulai = session('rekomendasi_tanggal_mulai');
        $usiaHari = session('rekomendasi_usia_hari');

        $sesiAktif = session('aktif_sesi_id') 
            ? SesiTanam::where('id', session('aktif_sesi_id'))->where('status', 'aktif')->with('tanaman')->first()
            : null;

        if (!$sesiAktif) {
            $sesiAktif = SesiTanam::where('status', 'aktif')->with('tanaman')->latest()->first();
        }

        if (!$tanamanId || !$fase || !$sistem) {
            if ($sesiAktif) {
                return redirect('/hasil?sesi_id=' . $sesiAktif->id);
            }
            return redirect('/rekomendasi')->with('warning', 'Silakan pilih tanaman dan fase pertumbuhan terlebih dahulu.');
        }

        $tanaman = Tanaman::find($tanamanId);
        $rekomendasi = $this->engine->getRekomendasiNutrisi($tanamanId, $fase, $sistem);

        if (!$rekomendasi) {
            return redirect('/rekomendasi')->with('error', 'Aturan rekomendasi tidak ditemukan.');
        }

        // --- Kalkulasi Data Jadwal & Siklus Tanam ---
        $rule = RuleNutrisi::where('tanaman_id', $tanamanId)
            ->where('fase', $fase)
            ->first();

        $durasiMap = $this->engine->getDurasiFaseMap();
        $namaTanaman = $tanaman ? $tanaman->nama : '';
        $fasesTanaman = $durasiMap[$namaTanaman] ?? null;
        $durasiTotal = 35; // Default fallback

        if ($fasesTanaman) {
            $lastFase = end($fasesTanaman);
            $durasiTotal = $lastFase['kumulatif'];
        }

        $usiaTotalTanaman = $usiaHari ?? 0;
        $progressPersen = min(100, ($usiaTotalTanaman / max(1, $durasiTotal)) * 100);

        // Estimasi pindah fase
        $estimasiPindahFase = '';
        if ($fasesTanaman && isset($fasesTanaman[$fase])) {
            $faseKeys = array_keys($fasesTanaman);
            $currentIdx = array_search($fase, $faseKeys);
            $nextIdx = $currentIdx + 1;

            if ($nextIdx < count($faseKeys)) {
                $nextFaseKey = $faseKeys[$nextIdx];
                $nextFaseLabel = ucwords(str_replace('_', ' ', $nextFaseKey));
                $kumulatifFaseIni = $fasesTanaman[$fase]['kumulatif'];
                
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

        // Generate Jadwal Perawatan Bulanan (Satu bulan kalender)
        $kalenderBulan = [];
        $logsByDate = [];
        
        if ($sesiAktif) {
            $logs = \App\Models\LogPerawatan::where('sesi_tanam_id', $sesiAktif->id)->get();
            foreach ($logs as $log) {
                $logsByDate[$log->tanggal->format('Y-m-d')][] = $log;
            }
        }

        if ($rule) {
            // Kita akan buat kalender bulan ini
            $startOfMonth = Carbon::today()->startOfMonth();
            $endOfMonth = Carbon::today()->endOfMonth();
            
            // Padding untuk grid kalender (hari sebelum tanggal 1)
            $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
            
            $currentDate = $startOfMonth->copy()->subDays($startDayOfWeek - 1);
            
            // Buat grid 6 minggu x 7 hari = 42 sel (agar rapi)
            for ($i = 0; $i < 42; $i++) {
                $dateStr = $currentDate->format('Y-m-d');
                $isCurrentMonth = $currentDate->month === Carbon::today()->month;
                $isToday = $currentDate->isToday();
                
                $usiaTanamanPadaTanggalIni = Carbon::parse($tanggalMulai)->diffInDays($currentDate, false);
                
                $kegiatan = [];
                
                // Hanya jadwalkan tugas jika usianya positif (setelah tanggal mulai) 
                // dan belum melebihi durasi total
                if ($usiaTanamanPadaTanggalIni >= 0 && $usiaTanamanPadaTanggalIni <= $durasiTotal) {
                    // Evaluasi rule cek pH/EC
                    $intervalCek = $rule->cek_ph_ec ?? 1;
                    if ($usiaTanamanPadaTanggalIni % $intervalCek === 0) {
                        $kegiatan[] = [
                            'tipe' => 'cek',
                            'judul' => 'Cek Kondisi Air',
                            'deskripsi' => "Target: pH {$rule->ph_min}-{$rule->ph_max}, PPM {$rule->ppm_min}-{$rule->ppm_max}, Suhu Air {$rule->suhu_min}-{$rule->suhu_max}°C.",
                            'ph_min' => $rule->ph_min,
                            'ph_max' => $rule->ph_max,
                            'ppm_min' => $rule->ppm_min,
                            'ppm_max' => $rule->ppm_max,
                            'suhu_min' => $rule->suhu_min,
                            'suhu_max' => $rule->suhu_max,
                        ];
                    }

                    // Evaluasi rule isi ulang nutrisi
                    $intervalIsiUlang = $rule->isi_ulang ?? 2;
                    if ($usiaTanamanPadaTanggalIni > 0 && $usiaTanamanPadaTanggalIni % $intervalIsiUlang === 0) {
                        $kegiatan[] = [
                            'tipe' => 'isi_ulang',
                            'judul' => 'Isi Ulang Air & Nutrisi',
                            'deskripsi' => "Tambahkan air bersih dan nutrisi susulan jika perlu."
                        ];
                    }
                }

                $kalenderBulan[] = [
                    'date' => $dateStr,
                    'day' => $currentDate->day,
                    'isCurrentMonth' => $isCurrentMonth,
                    'isToday' => $isToday,
                    'isPast' => $currentDate->isPast() && !$isToday,
                    'kegiatan' => $kegiatan,
                    'logs' => $logsByDate[$dateStr] ?? []
                ];
                
                $currentDate->addDay();
            }
        }

        // Tentukan fase berikutnya untuk card info
        $faseBerikutnya = '';
        $catatanFaseBerikutnya = '';

        if ($fasesTanaman) {
            $faseKeys = array_keys($fasesTanaman);
            $currentIdx = array_search($fase, $faseKeys);
            $nextIdx = $currentIdx + 1;

            if ($nextIdx < count($faseKeys)) {
                $nextFaseKey = $faseKeys[$nextIdx];
                $faseBerikutnya = ucwords(str_replace('_', ' ', $nextFaseKey));

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

        return view('pages.hasil', compact(
            'tanaman', 'fase', 'sistem', 'rekomendasi', 'tanggalMulai', 'usiaHari',
            'sesiAktif', 'rule', 'progressPersen', 'durasiTotal', 'estimasiPindahFase',
            'kalenderBulan', 'logsByDate', 'faseBerikutnya', 'catatanFaseBerikutnya'
        ));
    }
}
