<?php

namespace App\Http\Controllers;

use App\Models\SesiTanam;
use App\Models\RiwayatDiagnosa;
use App\Models\LogPerawatan;
use App\Models\RuleNutrisi;
use App\Models\Tanaman;
use Illuminate\Http\Request;
use App\Services\RuleBasedEngine;

class DiagnosaController extends Controller
{
    protected $engine;

    public function __construct(RuleBasedEngine $engine)
    {
        $this->engine = $engine;
    }

    public function index()
    {
        $aktifSesiId = session('aktif_sesi_id');
        $sesiTanam = null;
        if ($aktifSesiId) {
            $sesiTanam = SesiTanam::where('id', $aktifSesiId)->where('status', 'aktif')->with('tanaman')->first();
        }
        if (!$sesiTanam) {
            $sesiTanam = SesiTanam::where('status', 'aktif')->with('tanaman')->latest()->first();
        }
        
        $tanaman = null;
        $fase = null;
        $rule = null;

        if ($sesiTanam) {
            $tanaman = $sesiTanam->tanaman;
            $fase = $sesiTanam->fase_saat_ini;
            $rule = RuleNutrisi::where('tanaman_id', $sesiTanam->tanaman_id)
                ->where('fase', $fase)
                ->first();
        } else {
            // Fallback ke Laravel Session jika tidak ada sesi tanam aktif di database
            $tanamanId = session('rekomendasi_tanaman_id');
            $fase = session('rekomendasi_fase');
            if ($tanamanId && $fase) {
                $tanaman = Tanaman::find($tanamanId);
                $rule = RuleNutrisi::where('tanaman_id', $tanamanId)
                    ->where('fase', $fase)
                    ->first();
            }
        }

        return view('pages.cek-kondisi', compact('sesiTanam', 'tanaman', 'fase', 'rule'));
    }

    public function diagnosa(Request $request)
    {
        $validated = $request->validate([
            'ph_aktual' => 'required|numeric|min:0|max:14',
            'ec_aktual' => 'required|numeric|min:0|max:10',
            'ppm_aktual' => 'required|integer|min:0|max:5000',
            'suhu_aktual' => 'nullable|numeric|min:0|max:50',
            'sesi_tanam_id' => 'nullable|exists:sesi_tanam,id',
            'tanaman_id' => 'nullable|exists:tanaman,id',
            'fase' => 'nullable|string',
        ]);

        $sesiTanamId = $validated['sesi_tanam_id'] ?? null;
        
        if ($sesiTanamId) {
            $sesi = SesiTanam::findOrFail($sesiTanamId);
            $tanamanId = $sesi->tanaman_id;
            $fase = $sesi->fase_saat_ini;
        } else {
            $tanamanId = $validated['tanaman_id'] ?? session('rekomendasi_tanaman_id');
            $fase = $validated['fase'] ?? session('rekomendasi_fase');
        }

        if (!$tanamanId || !$fase) {
            return redirect('/cek-kondisi')->with('error', 'Konteks tanaman atau fase tidak valid. Silakan lakukan rekomendasi terlebih dahulu.');
        }

        // Jalankan engine diagnosa
        $hasil = $this->engine->diagnosaAbnormal(
            $tanamanId,
            $fase,
            $validated['ph_aktual'],
            $validated['ec_aktual'],
            $validated['ppm_aktual'],
            $validated['suhu_aktual'] ?? null
        );

        // Simpan ke database jika ada sesi tanam aktif
        if ($sesiTanamId) {
            RiwayatDiagnosa::create([
                'sesi_tanam_id' => $sesiTanamId,
                'ph_aktual' => $validated['ph_aktual'],
                'ec_aktual' => $validated['ec_aktual'],
                'ppm_aktual' => $validated['ppm_aktual'],
                'hasil_diagnosa' => $hasil,
            ]);

            $status = 'selesai';
            $catatanPanduan = null;
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
                    'tipe' => 'cek',
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
