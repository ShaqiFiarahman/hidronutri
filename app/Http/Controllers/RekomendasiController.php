<?php

namespace App\Http\Controllers;

use App\Models\Tanaman;
use App\Models\SesiTanam;
use App\Models\RuleNutrisi;
use Carbon\Carbon;
use App\Services\RuleBasedEngine;
use App\Http\Requests\RekomendasiProsesRequest;
use Illuminate\Http\Request;

class RekomendasiController extends Controller
{
    protected $engine;

    public function __construct(RuleBasedEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Menampilkan halaman pemilihan tanaman dan sistem untuk rekomendasi
     */
    public function index()
    {
        $tanaman = Tanaman::all();

        // ambil daftar fase pertumbuhan yang tersedia untuk setiap tanaman
        $fasePerTanaman = \App\Models\RuleNutrisi::select('tanaman_id', 'fase')
            ->get()
            ->groupBy('tanaman_id')
            ->map(fn($rules) => $rules->pluck('fase')->values())
            ->toArray();

        // buat peta data ID ke nama tanaman untuk mempermudah operasi JavaScript
        $tanamanMap = $tanaman->pluck('nama', 'id')->toArray();

        // ambil data panduan durasi per fase dari sistem pakar
        $durasiMap = $this->engine->getDurasiFaseMap();

        return view('pages.rekomendasi', compact('tanaman', 'fasePerTanaman', 'durasiMap', 'tanamanMap'));
    }

    /**
     * Memproses input rekomendasi dan membuat sesi tanam aktif
     */
    public function proses(RekomendasiProsesRequest $request)
    {
        $validated = $request->validated();

        $tanaman = Tanaman::find($validated['tanaman_id']);
        // hitung usia tanaman dalam hari dari tanggal mulai sampai hari ini
        $usiaHari = \Carbon\Carbon::parse($validated['tanggal_mulai'])->diffInDays(\Carbon\Carbon::today());
        
        // kalkulasi rentang fase saat ini berdasarkan usia tanaman
        $fase = $this->engine->determineFase($tanaman->nama, $usiaHari);

        // pastikan kombinasi tanaman dan fase memiliki target nutrisi di basis data
        $ruleExists = \App\Models\RuleNutrisi::where('tanaman_id', $validated['tanaman_id'])
            ->where('fase', $fase)
            ->exists();

        // tolak pemrosesan jika fase tidak ditemukan
        if (!$ruleExists) {
            return back()->withErrors(['tanaman_id' => 'Gagal menentukan fase yang valid untuk usia tanaman ini.'])->withInput();
        }

        // buat pencatatan sesi tanam baru dengan status aktif secara otomatis
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

    /**
     * Menampilkan hasil rekomendasi berdasarkan sesi aktif atau session
     */
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

        // ambil data sesi aktif yang tercatat di session
        $sesiAktif = session('aktif_sesi_id') 
            ? SesiTanam::where('id', session('aktif_sesi_id'))->where('status', 'aktif')->with('tanaman')->first()
            : null;

        // periksa jika ada sesi tanam aktif terbaru sebagai cadangan
        if (!$sesiAktif) {
            $sesiAktif = SesiTanam::where('status', 'aktif')->with('tanaman')->latest()->first();
        }

        // redirect dengan peringatan jika informasi tanaman tidak lengkap di session
        if (!$tanamanId || !$fase || !$sistem) {
            // otomatis redirect ke hasil dari sesi yang ada jika data session hilang
            if ($sesiAktif) {
                return redirect('/hasil?sesi_id=' . $sesiAktif->id);
            }
            return redirect('/rekomendasi')->with('warning', 'Silakan pilih tanaman dan fase pertumbuhan terlebih dahulu.');
        }

        $tanaman = Tanaman::find($tanamanId);
        $rekomendasi = $this->engine->getRekomendasiNutrisi($tanamanId, $fase, $sistem);

        // kembalikan error jika panduan untuk fase ini tidak ditemukan di basis data
        if (!$rekomendasi) {
            return redirect('/rekomendasi')->with('error', 'Aturan rekomendasi tidak ditemukan.');
        }

        // --- Perhitungan Data Siklus dan Kemajuan Pertumbuhan ---
        $rule = RuleNutrisi::where('tanaman_id', $tanamanId)
            ->where('fase', $fase)
            ->first();

        $durasiMap = $this->engine->getDurasiFaseMap();
        $namaTanaman = $tanaman ? $tanaman->nama : '';
        $fasesTanaman = $durasiMap[$namaTanaman] ?? null;

        // kalkulasi durasi total dari kumulatif fase terakhir (data dari tabel fase_tanaman)
        $durasiTotal = 0;
        if ($fasesTanaman) {
            $lastFase = end($fasesTanaman);
            $durasiTotal = $lastFase['kumulatif'];
        }

        $usiaTotalTanaman = $usiaHari ?? 0;
        
        // hitung persentase kemajuan siklus tumbuh tanaman
        $progressPersen = min(100, ($usiaTotalTanaman / max(1, $durasiTotal)) * 100);

        // perkirakan jumlah hari menuju tahap perkembangan selanjutnya
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

        // buat susunan jadwal perawatan bulanan untuk antarmuka kalender
        $kalenderBulan = [];
        $logsByDate = [];
        
        // kumpulkan semua riwayat penugasan berdasarkan tanggal jika ada sesi aktif
        if ($sesiAktif) {
            $logs = \App\Models\LogPerawatan::where('sesi_tanam_id', $sesiAktif->id)->get();
            foreach ($logs as $log) {
                $logsByDate[$log->tanggal->format('Y-m-d')][] = $log;
            }
        }

        // buat pola hari untuk tampilan kalender bulan ini
        if ($rule) {
            $startOfMonth = Carbon::today()->startOfMonth();
            $endOfMonth = Carbon::today()->endOfMonth();
            
            // berikan elemen spasi agar tanggal 1 jatuh di hari yang tepat
            $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
            
            $currentDate = $startOfMonth->copy()->subDays($startDayOfWeek - 1);
            
            // siapkan jumlah petak 6 baris kali 7 hari genap menjadi 42 buah
            for ($i = 0; $i < 42; $i++) {
                $dateStr = $currentDate->format('Y-m-d');
                $isCurrentMonth = $currentDate->month === Carbon::today()->month;
                $isToday = $currentDate->isToday();
                
                $usiaTanamanPadaTanggalIni = Carbon::parse($tanggalMulai)->diffInDays($currentDate, false);
                
                $kegiatan = [];
                
                // saring pembuatan jadwal hanya untuk usia aktif sebelum waktu panen tiba
                if ($usiaTanamanPadaTanggalIni >= 0 && $usiaTanamanPadaTanggalIni <= $durasiTotal) {
                    // periksa apakah harus pengecekan nutrisi di hari ini
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

                    // periksa apakah waktunya menambah volume air berdasarkan interval 
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

        // tentukan catatan ringkas tahapan perkembangan yang menunggu selanjutnya
        $faseBerikutnya = '';
        $catatanFaseBerikutnya = '';

        if ($fasesTanaman) {
            $faseKeys = array_keys($fasesTanaman);
            $currentIdx = array_search($fase, $faseKeys);
            $nextIdx = $currentIdx + 1;

            if ($nextIdx < count($faseKeys)) {
                $nextFaseKey = $faseKeys[$nextIdx];
                $faseBerikutnya = ucwords(str_replace('_', ' ', $nextFaseKey));
            } else {
                $faseBerikutnya = 'Selesai';
            }
        }

        return view('pages.hasil', compact(
            'tanaman', 'fase', 'sistem', 'rekomendasi', 'tanggalMulai', 'usiaHari',
            'sesiAktif', 'rule', 'progressPersen', 'durasiTotal', 'estimasiPindahFase',
            'kalenderBulan', 'logsByDate', 'faseBerikutnya'
        ));
    }
}
