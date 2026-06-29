<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogPerawatan;
use App\Models\SesiTanam;
use App\Models\RuleNutrisi;
use App\Services\RuleBasedEngine;
use App\Http\Requests\LogPerawatanRequest;


class LogPerawatanController extends Controller
{
    /**
     * Menyimpan data log perawatan (pengecekan nutrisi atau isi ulang)
     */
    public function store(LogPerawatanRequest $request, RuleBasedEngine $engine)
    {
        $validated = $request->validated();

        $sesi = SesiTanam::findOrFail($validated['sesi_tanam_id']);
        
        // ambil data target nutrisi sesuai tanaman dan fase saat ini
        $rule = RuleNutrisi::where('tanaman_id', $sesi->tanaman_id)
            ->where('fase', $sesi->fase_saat_ini)
            ->first();

        $status = 'selesai';
        $catatanPanduan = $validated['catatan'] ?? null;
        
        // periksa potensi masalah jika jenis kegiatan adalah pengecekan dan aturan target ditemukan
        if ($validated['tipe'] === 'cek' && $rule) {
            $isPhValid = $validated['ph'] === null || ($validated['ph'] >= $rule->ph_min && $validated['ph'] <= $rule->ph_max);
            $isPpmValid = $validated['ppm'] === null || ($validated['ppm'] >= $rule->ppm_min && $validated['ppm'] <= $rule->ppm_max);
            $isSuhuValid = $validated['suhu'] === null || 
                           ($rule->suhu_min === null && $rule->suhu_max === null) ||
                           ($validated['suhu'] >= $rule->suhu_min && $validated['suhu'] <= $rule->suhu_max);

            // tetapkan status perlu perhatian jika salah satu parameter di luar batas ideal
            if (!$isPhValid || !$isPpmValid || !$isSuhuValid) {
                $status = 'perlu_perhatian';
                
                $phAktual = $validated['ph'] ?? $rule->ph_min;
                $ppmAktual = $validated['ppm'] ?? $rule->ppm_min;
                $ecAktual = round($ppmAktual / 500, 2);
                
                $diagnosa = $engine->diagnosaAbnormal($sesi->tanaman_id, $sesi->fase_saat_ini, $phAktual, $ecAktual, $ppmAktual);
                $panduanList = [];
                foreach ($diagnosa as $diag) {
                    $panduanList[] = "• [" . $diag['parameter'] . " " . ucfirst($diag['kondisi']) . "]: " . $diag['tindakan'];
                }
                // gabungkan pesan panduan untuk suhu air jika bermasalah
                if (!$isSuhuValid && $validated['suhu'] !== null) {
                    $panduanList[] = "• [Suhu Air Abnormal]: Suhu saat ini {$validated['suhu']}°C (Target: {$rule->suhu_min}-{$rule->suhu_max}°C). Tambahkan es batu bersih atau letakkan tandon di area teduh.";
                }
                
                // tetapkan catatan perbaikan jika ada rekomendasi langkah dari mesin aturan
                if (!empty($panduanList)) {
                    $catatanPanduan = implode("\n", $panduanList);
                }
            }
        }

        // buat catatan baru atau perbarui riwayat berdasarkan id sesi dan tanggal
        $log = LogPerawatan::updateOrCreate(
            [
                'sesi_tanam_id' => $validated['sesi_tanam_id'],
                'tanggal' => $validated['tanggal'],
                'tipe' => $validated['tipe'],
            ],
            [
                'ph' => $validated['ph'] ?? null,
                'ppm' => $validated['ppm'] ?? null,
                'suhu' => $validated['suhu'] ?? null,
                'catatan' => $catatanPanduan,
                'status' => $status,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Log perawatan berhasil disimpan.',
            'data' => $log
        ]);
    }
}
