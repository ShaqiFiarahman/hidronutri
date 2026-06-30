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
     * @param float|null $suhuAktual
     * @return array
     */
    public function diagnosaAbnormal($tanamanId, $fase, $phAktual, $ecAktual, $ppmAktual, $suhuAktual = null)
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

        // Rule pH Rendah (RKor-01)
        if ($phAktual < $rule->ph_min) {
            $hasil[] = [
                'parameter' => 'pH',
                'kondisi' => 'rendah',
                'nilai_aktual' => $phAktual,
                'nilai_target' => $rule->ph_min . ' - ' . $rule->ph_max,
                'tindakan' => $tindakan->has('pH') ? $tindakan['pH']->where('kondisi', 'rendah')->first()->tindakan ?? 'Tambahkan pH Up.' : 'Tambahkan pH Up.',
            ];
        }
        // Rule pH Tinggi (RKor-02)
        if ($phAktual > $rule->ph_max) {
            $hasil[] = [
                'parameter' => 'pH',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $phAktual,
                'nilai_target' => $rule->ph_min . ' - ' . $rule->ph_max,
                'tindakan' => $tindakan->has('pH') ? $tindakan['pH']->where('kondisi', 'tinggi')->first()->tindakan ?? 'Tambahkan pH Down.' : 'Tambahkan pH Down.',
            ];
        }


        // ═══════════════════════════════════════════════════
        // Evaluasi Rule PPM — RULE KOREKSI
        // RKor-03: PPM kurang → tambah 5ml/L per 100 ppm kekurangan
        // RKor-04: PPM berlebih → encerkan larutan
        // Formula: 5ml A + 5ml B per 1000ml air = 1000 PPM
        // ═══════════════════════════════════════════════════

        if ($ppmAktual < $rule->ppm_min) {
            $baseTindakan = $tindakan->has('PPM') ? $tindakan['PPM']->where('kondisi', 'rendah')->first()->tindakan ?? 'Tambahkan nutrisi.' : 'Tambahkan nutrisi.';
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'rendah',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => $baseTindakan,
            ];
        }
        // Rule PPM Tinggi (RKor-04)
        if ($ppmAktual > $rule->ppm_max) {
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => $tindakan->has('PPM') ? $tindakan['PPM']->where('kondisi', 'tinggi')->first()->tindakan ?? 'Encerkan larutan dengan menambahkan air murni.' : 'Encerkan larutan dengan menambahkan air murni.',
            ];
        }

        // Rule Suhu (Suhu tidak ada di DB tindakan_korektif awalnya, kita set manual)
        if ($suhuAktual !== null) {
            $suhuMin = $rule->suhu_min ?? 22;
            $suhuMax = $rule->suhu_max ?? 28;
            
            if ($suhuAktual < $suhuMin) {
                $hasil[] = [
                    'parameter' => 'Suhu',
                    'kondisi' => 'rendah',
                    'nilai_aktual' => $suhuAktual,
                    'nilai_target' => $suhuMin . ' - ' . $suhuMax,
                    'tindakan' => 'Suhu air terlalu dingin. Gunakan pemanas air akuarium (heater) jika suhu terus drop, atau kurangi intensitas pendingin jika menggunakan water chiller.',
                ];
            } elseif ($suhuAktual > $suhuMax) {
                $hasil[] = [
                    'parameter' => 'Suhu',
                    'kondisi' => 'tinggi',
                    'nilai_aktual' => $suhuAktual,
                    'nilai_target' => $suhuMin . ' - ' . $suhuMax,
                    'tindakan' => 'Suhu air terlalu panas. Tambahkan bongkahan es batu bersih (atau di dalam botol tertutup) ke dalam tandon, pindahkan tandon ke area yang lebih teduh, atau lapisi tandon dengan styrofoam/aluminium foil.',
                ];
            }
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
