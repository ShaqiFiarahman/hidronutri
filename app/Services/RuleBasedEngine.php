<?php

namespace App\Services;

use App\Models\RuleNutrisi;

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
                'tindakan' => 'Tambahkan pH Up. Koreksi dilakukan setelah penambahan nutrisi, tunggu larutan tercampur rata.',
            ];
        }
        // Rule pH Tinggi (RKor-02)
        if ($phAktual > $rule->ph_max) {
            $hasil[] = [
                'parameter' => 'pH',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $phAktual,
                'nilai_target' => $rule->ph_min . ' - ' . $rule->ph_max,
                'tindakan' => 'Tambahkan pH Down. Koreksi dilakukan setelah penambahan nutrisi, tunggu larutan tercampur rata.',
            ];
        }

        // ═══════════════════════════════════════════════════
        // Evaluasi Rule EC (dihitung dari PPM / 500)
        // ═══════════════════════════════════════════════════

        // Rule EC Rendah
        if ($ecAktual < $rule->ec_min) {
            $hasil[] = [
                'parameter' => 'EC',
                'kondisi' => 'rendah',
                'nilai_aktual' => $ecAktual,
                'nilai_target' => $rule->ec_min . ' - ' . $rule->ec_max,
                'tindakan' => 'Tambahkan nutrisi A+B secara proporsional hingga EC mencapai target.',
            ];
        }
        // Rule EC Tinggi
        if ($ecAktual > $rule->ec_max) {
            $hasil[] = [
                'parameter' => 'EC',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $ecAktual,
                'nilai_target' => $rule->ec_min . ' - ' . $rule->ec_max,
                'tindakan' => 'Encerkan larutan dengan air bersih hingga EC turun ke rentang target.',
            ];
        }

        // ═══════════════════════════════════════════════════
        // Evaluasi Rule PPM — RULE KOREKSI
        // RKor-03: PPM kurang → tambah 5ml/L per 100 ppm kekurangan
        // RKor-04: PPM berlebih → encerkan larutan
        // Formula: 5ml A + 5ml B per 1000ml air = 1000 PPM
        // ═══════════════════════════════════════════════════

        // Rule PPM Rendah (RKor-03)
        if ($ppmAktual < $rule->ppm_min) {
            $kekurangan = $rule->ppm_min - $ppmAktual;
            $koreksiMlPerLiter = round(($kekurangan / 100) * 5, 1);
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'rendah',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => "Kekurangan {$kekurangan} PPM. Tambahkan nutrisi {$koreksiMlPerLiter} ml/L (masing-masing A dan B). Formula: 5ml A + 5ml B per 1000ml air = 1000 PPM.",
            ];
        }
        // Rule PPM Tinggi (RKor-04)
        if ($ppmAktual > $rule->ppm_max) {
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => 'Encerkan larutan (tambah air bersih) pada pagi/sore. Kuras tandon tiap 1 siklus jika sudah jauh berlebih.',
            ];
        }

        return $hasil;
    }

    /**
     * Map durasi fase (dalam hari) untuk setiap tanaman.
     */
    public function getDurasiFaseMap()
    {
        return [
            'Selada' => [
                'semai' => ['durasi' => 14, 'kumulatif' => 14],           // RFase-01: 14 hari
                'vegetatif_awal' => ['durasi' => 14, 'kumulatif' => 28],  // RFase-02: 14 hari
                'vegetatif_akhir' => ['durasi' => 12, 'kumulatif' => 40], // RFase-03: sisa hari hingga panen
                'panen' => ['durasi' => 0, 'kumulatif' => 40],            // Total 40 hari
            ],
            'Kangkung' => [
                'semai' => ['durasi' => 10, 'kumulatif' => 10],           // RFase-04: 7-10 hari
                'vegetatif_awal' => ['durasi' => 5, 'kumulatif' => 15],
                'vegetatif_akhir' => ['durasi' => 6, 'kumulatif' => 21],  // RFase-05: total 21 hari
                'panen' => ['durasi' => 0, 'kumulatif' => 21],
            ],
            'Pakcoy' => [
                'semai' => ['durasi' => 10, 'kumulatif' => 10],           // RFase-06: 7-10 hari
                'vegetatif_awal' => ['durasi' => 10, 'kumulatif' => 20],
                'vegetatif_akhir' => ['durasi' => 10, 'kumulatif' => 30], // RFase-07: total 28-30 hari
                'panen' => ['durasi' => 0, 'kumulatif' => 30],
            ],
            'Cabai' => [
                'semai' => ['durasi' => 30, 'kumulatif' => 30],           // RFase-08: 25-30 hari
                'vegetatif' => ['durasi' => 15, 'kumulatif' => 45],       // RFase-09: 14-15 hari
                'pembungaan' => ['durasi' => 20, 'kumulatif' => 65],
                'pembuahan' => ['durasi' => 20, 'kumulatif' => 85],
                'pembesaran' => ['durasi' => 15, 'kumulatif' => 100],     // Total ~100 hari (indikatif)
            ],
            'Melon' => [
                'semai' => ['durasi' => 14, 'kumulatif' => 14],           // RFase-10: 14 hari
                'vegetatif' => ['durasi' => 16, 'kumulatif' => 30],       // RFase-11: 25-30 hari
                'pembesaran' => ['durasi' => 30, 'kumulatif' => 60],      // RFase-12: 30 hari
                'pematangan' => ['durasi' => 20, 'kumulatif' => 80],      // Total ~80 hari
            ],
        ];
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
