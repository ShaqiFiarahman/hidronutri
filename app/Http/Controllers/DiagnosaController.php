<?php

namespace App\Http\Controllers;

use App\Models\SesiTanam;

use App\Models\LogPerawatan;
use App\Models\RuleNutrisi;
use App\Models\Tanaman;
use App\Http\Requests\DiagnosaRequest;
use Illuminate\Http\Request;
use App\Services\RuleBasedEngine;

class DiagnosaController extends Controller
{
    protected $engine;

    public function __construct(RuleBasedEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Menampilkan halaman cek kondisi dan form diagnosa
     */
    public function index()
    {
        $aktifSesiId = session('aktif_sesi_id');
        $sesiTanam = null;
        // cari sesi tanam aktif berdasarkan session id
        if ($aktifSesiId) {
            $sesiTanam = SesiTanam::where('id', $aktifSesiId)->where('status', 'aktif')->with('tanaman')->first();
        }
        
        // cari sesi tanam aktif terbaru sebagai alternatif jika tidak ada id di session
        if (!$sesiTanam) {
            $sesiTanam = SesiTanam::where('status', 'aktif')->with('tanaman')->latest()->first();
        }
        
        $tanaman = null;
        $fase = null;
        $rule = null;

        // tetapkan target nutrisi berdasarkan sesi tanam aktif
        if ($sesiTanam) {
            $tanaman = $sesiTanam->tanaman;
            $fase = $sesiTanam->fase_saat_ini;
            $rule = RuleNutrisi::where('tanaman_id', $sesiTanam->tanaman_id)
                ->where('fase', $fase)
                ->first();
        } else {
            // gunakan data rekomendasi dari session jika belum ada sesi tanam di database
            $tanamanId = session('rekomendasi_tanaman_id');
            $fase = session('rekomendasi_fase');
            
            // tetapkan target nutrisi berdasarkan data sementara
            if ($tanamanId && $fase) {
                $tanaman = Tanaman::find($tanamanId);
                $rule = RuleNutrisi::where('tanaman_id', $tanamanId)
                    ->where('fase', $fase)
                    ->first();
            }
        }

        return view('pages.cek-kondisi', compact('sesiTanam', 'tanaman', 'fase', 'rule'));
    }

    /**
     * Memproses diagnosa kondisi nutrisi air
     */
    public function diagnosa(DiagnosaRequest $request)
    {
        $validated = $request->validated();

        $sesiTanamId = $validated['sesi_tanam_id'] ?? null;
        
        // gunakan data dari sesi tanam spesifik jika tersedia
        if ($sesiTanamId) {
            $sesi = SesiTanam::findOrFail($sesiTanamId);
            $tanamanId = $sesi->tanaman_id;
            $fase = $sesi->fase_saat_ini;
        } else {
            // gunakan input manual atau data dari session sebagai alternatif
            $tanamanId = $validated['tanaman_id'] ?? session('rekomendasi_tanaman_id');
            $fase = $validated['fase'] ?? session('rekomendasi_fase');
        }

        // kembalikan pesan peringatan jika parameter untuk diagnosa tidak lengkap
        if (!$tanamanId || !$fase) {
            return redirect('/cek-kondisi')->with('error', 'Konteks tanaman atau fase tidak valid. Silakan lakukan rekomendasi terlebih dahulu.');
        }

        // evaluasi input kondisi air untuk mencari potensi masalah nutrisi
        $hasil = $this->engine->diagnosaAbnormal(
            $tanamanId,
            $fase,
            $validated['ph_aktual'],
            $validated['ec_aktual'],
            $validated['ppm_aktual'],
            $validated['suhu_aktual'] ?? null
        );

        // rekam hasil diagnosa dan riwayat pengecekan ke database
        if ($sesiTanamId) {


            $status = 'selesai';
            $catatanPanduan = null;
            
            // ubah status menjadi peringatan jika ditemukan masalah pada air
            if (!empty($hasil)) {
                $status = 'perlu_perhatian';
                $panduanList = [];
                foreach ($hasil as $diag) {
                    $panduanList[] = "• [" . $diag['parameter'] . " " . ucfirst($diag['kondisi']) . "]: " . $diag['tindakan'];
                }
                $catatanPanduan = implode("\n", $panduanList);
            }

            LogPerawatan::updateOrCreate(
                [
                    'sesi_tanam_id' => $sesiTanamId,
                    'tanggal' => now()->format('Y-m-d'),
                    'tipe' => now()->format('H') < 12 ? 'cek_pagi' : 'cek_sore',
                ],
                [
                    'ph' => $validated['ph_aktual'],
                    'ppm' => $validated['ppm_aktual'],
                    'suhu' => $validated['suhu_aktual'] ?? null,
                    'catatan' => $catatanPanduan,
                    'status' => $status,
                ]
            );
        }

        return redirect('/cek-kondisi')->with([
            'diagnosa_result' => $hasil,
            'ph_input' => $validated['ph_aktual'],
            'ec_input' => $validated['ec_aktual'],
            'ppm_input' => $validated['ppm_aktual'],
            'suhu_input' => $validated['suhu_aktual'] ?? null,
            'success_diagnosa' => true
        ]);
    }
}
