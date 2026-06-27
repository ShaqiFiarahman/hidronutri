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

        // Evaluasi Rule Forward Chaining
        // Rule pH Rendah
        if ($phAktual < $rule->ph_min) {
            $hasil[] = [
                'parameter' => 'pH',
                'kondisi' => 'rendah',
                'nilai_aktual' => $phAktual,
                'nilai_target' => $rule->ph_min . ' - ' . $rule->ph_max,
                'tindakan' => 'tambah pH Up 0.5ml/liter',
            ];
        }
        // Rule pH Tinggi
        if ($phAktual > $rule->ph_max) {
            $hasil[] = [
                'parameter' => 'pH',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $phAktual,
                'nilai_target' => $rule->ph_min . ' - ' . $rule->ph_max,
                'tindakan' => 'tambah pH Down 0.5ml/liter',
            ];
        }
        // Rule EC Rendah
        if ($ecAktual < $rule->ec_min) {
            $hasil[] = [
                'parameter' => 'EC',
                'kondisi' => 'rendah',
                'nilai_aktual' => $ecAktual,
                'nilai_target' => $rule->ec_min . ' - ' . $rule->ec_max,
                'tindakan' => 'tambah nutrisi A+B 0.5ml/liter',
            ];
        }
        // Rule EC Tinggi
        if ($ecAktual > $rule->ec_max) {
            $hasil[] = [
                'parameter' => 'EC',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $ecAktual,
                'nilai_target' => $rule->ec_min . ' - ' . $rule->ec_max,
                'tindakan' => 'encerkan dengan air bersih',
            ];
        }
        // Rule PPM Rendah
        if ($ppmAktual < $rule->ppm_min) {
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'rendah',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => 'tambah dosis nutrisi',
            ];
        }
        // Rule PPM Tinggi
        if ($ppmAktual > $rule->ppm_max) {
            $hasil[] = [
                'parameter' => 'PPM',
                'kondisi' => 'tinggi',
                'nilai_aktual' => $ppmAktual,
                'nilai_target' => $rule->ppm_min . ' - ' . $rule->ppm_max,
                'tindakan' => 'encerkan larutan',
            ];
        }

        return $hasil;
    }
}
