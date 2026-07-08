<?php

namespace App\Services;

use App\Models\RuleNutrisi;
use App\Models\FaseTanaman;

class RuleBasedEngine
{
    /**
     * Mendapatkan rekomendasi nutrisi berdasarkan tanaman dan fase.
     *
     * @param int $tanamanId
     * @param string $fase
     * @param string $sistemHidroponik
     * @return array|null
     */
    public function getRekomendasiNutrisi($tanamanId, $fase, $sistemHidroponik)
    {
        $rule = RuleNutrisi::where('tanaman_id', $tanamanId)
            ->where('fase', $fase)
            ->first();

        if (!$rule) {
            return null;
        }

        $result = $rule->toArray();
        $result['sistem_hidroponik'] = $sistemHidroponik;
        return $result;
    }

    /**
     * Mendiagnosis kondisi larutan abnormal berdasarkan nilai pH, EC, dan PPM.
     * 
     * Rules Koreksi (dari data referensi narasumber):
     * - RKor-01: pH < 5.5 → Trigger koreksi pH Up
     * - RKor-02: pH > 6.5 → Trigger koreksi pH Down
     * - RKor-03: PPM kurang dari target → Tambahkan nutrisi 5 ml/L per 100 ppm kekurangan
     * - RKor-04: PPM melebihi target → Encerkan larutan
     *
     * @param int $tanamanId
     * @param string $fase
     * @param float $phAktual
     * @param float $ecAktual
     * @param int $ppmAktual
     * @return array
     */
    public function diagnosaAbnormal($tanamanId, $fase, $phAktual, $ecAktual, $ppmAktual)
    {
        $rule = RuleNutrisi::where('tanaman_id', $tanamanId)
            ->where('fase', $fase)
            ->first();

        if (!$rule) {
            return [];
        }

        $hasil = [];

        // ═══════════════════════════════════════════════════
        // Ambil Tindakan Korektif dari Database
        // ═══════════════════════════════════════════════════
        $tindakan = \App\Models\TindakanKorektif::all()->groupBy('parameter');

        // ═══════════════════════════════════════════════════
        // Evaluasi Rule Forward Chaining — RULE pH (Universal)
        // RpH-01: pH < 5.5 → terlalu rendah
        // RpH-04: pH > 6.5 → terlalu tinggi
        // ═══════════════════════════════════════════════════

        $suffix = " Setelah setiap tindakan korektif, aduk larutan hingga tercampur merata, tunggu sirkulasi stabil, ukur kembali pH, EC, dan PPM, baru lakukan tindakan berikutnya apabila masih diperlukan.";

        // Rule pH Rendah (RKor-01)
        if ($phAktual < $rule->ph_min) {
            if ($phAktual < 4.0) {
                $tindakanText = "Disarankan mengganti sumber air baku dengan air yang memiliki pH lebih netral sebelum melakukan penyesuaian pH.";
            } else {
                $tindakanText = "Pastikan terlebih dahulu nutrisi (A dan B) telah disesuaikan hingga target PPM tercapai. Aduk larutan hingga tercampur merata. Ukur kembali pH. Apabila pH masih berada di luar rentang ideal, barulah gunakan pH Up sedikit demi sedikit.";
            }
            $hasil[] = [
                'parameter' => 'pH',
                'kondisi' => 'rendah',
                'nilai_aktual' => $phAktual,
                'nilai_target' => $rule->ph_min . ' - ' . $rule->ph_max,
                'tindakan' => $tindakanText . $suffix,
            ];
        }

        // Rule pH Tinggi (RKor-02)
        if ($phAktual > $rule->ph_max) {
            if ($phAktual > 8.0) {
                $tindakanText = "Disarankan mengganti sumber air baku dengan air yang memiliki pH lebih netral sebelum melakukan penyesuaian pH.";
            } else {
                $tindakanText = "Pastikan terlebih dahulu nutrisi (A dan B) telah disesuaikan hingga target PPM tercapai. Aduk larutan hingga tercampur merata. Ukur kembali pH. Apabila pH masih berada di luar rentang ideal, barulah gunakan pH Down sedikit demi sedikit.";
            }
            $hasil[] = [
                'parameter' => 'pH',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $phAktual,
                'nilai_target' => $rule->ph_min . ' - ' . $rule->ph_max,
                'tindakan' => $tindakanText . $suffix,
            ];
        }


        // ═══════════════════════════════════════════════════
        // Evaluasi Rule PPM — RULE KOREKSI
        // RKor-03: PPM kurang → tambah nutrisi secara bertahap
        // RKor-04: PPM berlebih → encerkan larutan (prioritas air baku)
        // ═══════════════════════════════════════════════════

        if ($ppmAktual < $rule->ppm_min) {
            $tindakanText = "Tambahkan larutan nutrisi AB Mix secara bertahap sedikit demi sedikit, kemudian ukur kembali hingga target PPM tercapai.";
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'rendah',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => $tindakanText . $suffix,
            ];
        }
        // Rule PPM Tinggi (RKor-04)
        if ($ppmAktual > $rule->ppm_max) {
            $tindakanText = "Prioritaskan penambahan air baku hingga PPM mendekati target. Penggantian larutan hanya direkomendasikan apabila kondisi benar-benar ekstrem.";
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => $tindakanText . $suffix,
            ];
        }


        return $hasil;
    }

    /**
     * Map durasi fase (dalam hari) untuk setiap tanaman.
     * Data dibaca dari tabel fase_tanaman di database (sebelumnya hardcoded).
     * Sumber: Tabel RULE FASE – Durasi Transisi Antar Fase Pertumbuhan (narasumber).
     *
     * @return array<string, array<string, array{durasi: int, kumulatif: int}>>
     */
    public function getDurasiFaseMap(): array
    {
        $rows = FaseTanaman::with('tanaman')
            ->orderBy('tanaman_id')
            ->orderBy('urutan')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $namaTanaman = $row->tanaman->nama ?? null;
            if (!$namaTanaman) continue;

            $map[$namaTanaman][$row->fase] = [
                'durasi'    => $row->durasi_hari,
                'kumulatif' => $row->kumulatif_hari,
            ];
        }

        return $map;
    }

    /**
     * Menentukan fase tanaman berdasarkan usia (dalam hari) sejak disemai/ditanam biji.
     */
    public function determineFase($tanamanNama, $usiaHari)
    {
        $map = $this->getDurasiFaseMap();
        
        if (!isset($map[$tanamanNama])) {
            return 'semai'; // Fallback aman
        }

        $fases = $map[$tanamanNama];
        
        foreach ($fases as $faseKey => $data) {
            if ($usiaHari <= $data['kumulatif']) {
                return $faseKey;
            }
        }
        
        // Jika melebihi kumulatif terbesar (sudah lewat masa panen dsb), kembalikan fase terakhir
        $faseKeys = array_keys($fases);
        return end($faseKeys);
    }
}
