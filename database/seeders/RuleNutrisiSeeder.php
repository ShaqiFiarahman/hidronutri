<?php

namespace Database\Seeders;

use App\Models\Tanaman;
use App\Models\RuleNutrisi;
use Illuminate\Database\Seeder;

class RuleNutrisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data berdasarkan tabel referensi narasumber:
     * - RULE pH: universal 5.5 - 6.5 (optimal 5.8 - 6.2)
     * - RULE PPM: spesifik per tanaman & fase
     * - RULE Koreksi: 5ml A + 5ml B per 1000ml air = 1000 PPM
     * - RULE Suhu Air: 22°C - 28°C
     * - EC dihitung otomatis: EC = PPM / 500
     */
    public function run(): void
    {
        $tanamanMap = Tanaman::all()->pluck('id', 'nama');

        // pH universal: 5.5 - 6.5 (berlaku semua tanaman & fase)
        // Suhu air universal: 22°C - 28°C
        // Dosis: (target_ppm_midpoint / 1000) * 5 ml per liter
        // EC: PPM / 500

        $rulesData = [
            // ═══════════════════════════════════════════════════
            // SELADA (4 fase) — RPPM-01 s/d RPPM-04
            // ═══════════════════════════════════════════════════
            [
                'tanaman' => 'Selada', 'fase' => 'semai',
                'ppm_min' => 500, 'ppm_max' => 700,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Dosis rendah, akar belum kuat. Semai selada butuh 10-14 hari sebelum pindah tanam.'
            ],
            [
                'tanaman' => 'Selada', 'fase' => 'vegetatif_awal',
                'ppm_min' => 800, 'ppm_max' => 1000,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Pantau ketat, fase pertumbuhan aktif. Daun sejati mulai tumbuh lebar.'
            ],
            [
                'tanaman' => 'Selada', 'fase' => 'vegetatif_akhir',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Jaga suhu air tetap sejuk. Fase pembesaran batang dan daun rimbun.'
            ],
            [
                'tanaman' => 'Selada', 'fase' => 'panen',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Turunkan konsentrasi EC 3 hari sebelum panen agar rasa sayur manis dan tidak pahit. Total hingga panen ~40 hari.'
            ],

            // ═══════════════════════════════════════════════════
            // KANGKUNG (4 fase) — RPPM-05 s/d RPPM-08
            // ═══════════════════════════════════════════════════
            [
                'tanaman' => 'Kangkung', 'fase' => 'semai',
                'ppm_min' => 500, 'ppm_max' => 700,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Kangkung toleran suhu tinggi. Semai 7-10 hari sebelum pindah tanam.'
            ],
            [
                'tanaman' => 'Kangkung', 'fase' => 'vegetatif_awal',
                'ppm_min' => 800, 'ppm_max' => 1000,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Kangkung', 'fase' => 'vegetatif_akhir',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Kangkung', 'fase' => 'panen',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Total hingga panen ~21 hari.'
            ],

            // ═══════════════════════════════════════════════════
            // PAKCOY (4 fase) — RPPM-09 s/d RPPM-12
            // ═══════════════════════════════════════════════════
            [
                'tanaman' => 'Pakcoy', 'fase' => 'semai',
                'ppm_min' => 500, 'ppm_max' => 700,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Semai 7-10 hari sebelum pindah tanam.'
            ],
            [
                'tanaman' => 'Pakcoy', 'fase' => 'vegetatif_awal',
                'ppm_min' => 800, 'ppm_max' => 1000,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Pakcoy', 'fase' => 'vegetatif_akhir',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Pakcoy', 'fase' => 'panen',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Total hingga panen ~28-30 hari.'
            ],

            // ═══════════════════════════════════════════════════
            // CABAI (5 fase) — RPPM-13 s/d RPPM-17
            // Fase: semai, vegetatif, pembungaan, pembuahan, pembesaran
            // ═══════════════════════════════════════════════════
            [
                'tanaman' => 'Cabai', 'fase' => 'semai',
                'ppm_min' => 800, 'ppm_max' => 800,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Semai cabai dari bibit 21-30 hari sebelum pindah tanam.'
            ],
            [
                'tanaman' => 'Cabai', 'fase' => 'vegetatif',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Cabai', 'fase' => 'pembungaan',
                'ppm_min' => 1300, 'ppm_max' => 1500,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Masuk fase generatif ±45 hari (dari bibit). Tingkatkan nutrisi untuk bunga.'
            ],
            [
                'tanaman' => 'Cabai', 'fase' => 'pembuahan',
                'ppm_min' => 1500, 'ppm_max' => 1500,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Butuh nutrisi paling tinggi untuk pengisian buah.'
            ],
            [
                'tanaman' => 'Cabai', 'fase' => 'pembesaran',
                'ppm_min' => 1200, 'ppm_max' => 1200,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Kurangi air saat buah mulai memerah.'
            ],

            // ═══════════════════════════════════════════════════
            // MELON (5 fase) — RPPM-18 s/d RPPM-22
            // Fase: vegetatif, transisi, pembesaran, pematangan, panen
            // ═══════════════════════════════════════════════════
            [
                'tanaman' => 'Melon', 'fase' => 'semai',
                'ppm_min' => 900, 'ppm_max' => 1000,
                'ganti_larutan' => 7, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Semai melon 14 hari sebelum pindah tanam.'
            ],
            [
                'tanaman' => 'Melon', 'fase' => 'vegetatif',
                'ppm_min' => 1200, 'ppm_max' => 1500,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Masuk fase generatif ±25-30 hari.'
            ],
            [
                'tanaman' => 'Melon', 'fase' => 'pembesaran',
                'ppm_min' => 1500, 'ppm_max' => 1500,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Butuh nutrisi paling tinggi untuk pembesaran buah.'
            ],
            [
                'tanaman' => 'Melon', 'fase' => 'pematangan',
                'ppm_min' => 1200, 'ppm_max' => 1300,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Kurangi nutrisi agar gula dalam buah meningkat.'
            ],
            [
                'tanaman' => 'Melon', 'fase' => 'panen',
                'ppm_min' => 1100, 'ppm_max' => 1100,
                'ganti_larutan' => 5, 'isi_ulang' => 2, 'cek_ph_ec' => 1,
                'peringatan' => 'Siap panen. Ciri kematangan: tangkai mulai retak, aroma harum kuat.'
            ],
        ];

        foreach ($rulesData as $rule) {
            $tanamanId = $tanamanMap[$rule['tanaman']] ?? null;

            if ($tanamanId) {
                // Hitung dosis dari PPM sesuai formula referensi
                $ppmMid = ($rule['ppm_min'] + $rule['ppm_max']) / 2;
                $dosis = round(($ppmMid / 1000) * 5, 2);

                RuleNutrisi::updateOrCreate(
                    [
                        'tanaman_id' => $tanamanId,
                        'fase' => $rule['fase']
                    ],
                    [
                        'ph_min' => 5.5,  // Universal (RpH-01 s/d RpH-04)
                        'ph_max' => 6.5,
                        'ppm_min' => $rule['ppm_min'],
                        'ppm_max' => $rule['ppm_max'],
                        'dosis_a' => $dosis,  // Formula: (PPM/1000) * 5 ml/L
                        'dosis_b' => $dosis,
                        'ganti_larutan' => $rule['ganti_larutan'],
                        'isi_ulang' => $rule['isi_ulang'],
                        'cek_ph_ec' => $rule['cek_ph_ec'],
                        'suhu_min' => 22,  // RSuhu-02: batas bawah
                        'suhu_max' => 28,  // RSuhu-01: batas atas aman
                        'peringatan' => $rule['peringatan'],
                    ]
                );
            }
        }
    }
}
