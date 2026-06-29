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
            $kekurangan = $rule->ppm_min - $ppmAktual;
            $koreksiMlPerLiter = round(($kekurangan / 100) * 5, 1);
            $baseTindakan = $tindakan->has('PPM') ? $tindakan['PPM']->where('kondisi', 'rendah')->first()->tindakan ?? '' : '';
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'rendah',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => "Kekurangan {$kekurangan} PPM. Maka Anda perlu menambahkan racikan {$koreksiMlPerLiter} ml nutrisi A dan {$koreksiMlPerLiter} ml nutrisi B untuk setiap liternya. " . $baseTindakan,
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
