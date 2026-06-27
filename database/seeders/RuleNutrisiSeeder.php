<?php

namespace Database\Seeders;

use App\Models\Tanaman;
use App\Models\RuleNutrisi;
use Illuminate\Database\Seeder;

class RuleNutrisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tanamanMap = Tanaman::all()->pluck('id', 'nama');

        $rulesData = [
            // SELADA
            [
                'tanaman' => 'Selada',
                'fase' => 'semai',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 0.6, 'ec_max' => 0.8,
                'ppm_min' => 300, 'ppm_max' => 400,
                'dosis_a' => 2.5, 'dosis_b' => 2.5,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 26,
                'peringatan' => 'Dosis rendah, akar belum kuat'
            ],
            [
                'tanaman' => 'Selada',
                'fase' => 'vegetatif_awal',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 1.2, 'ec_max' => 1.6,
                'ppm_min' => 600, 'ppm_max' => 800,
                'dosis_a' => 3.0, 'dosis_b' => 3.0,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 26,
                'peringatan' => 'Pantau ketat, fase pertumbuhan aktif'
            ],
            [
                'tanaman' => 'Selada',
                'fase' => 'vegetatif_akhir',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 1.6, 'ec_max' => 2.0,
                'ppm_min' => 800, 'ppm_max' => 1000,
                'dosis_a' => 4.0, 'dosis_b' => 4.0,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 24,
                'peringatan' => 'Jaga suhu air tetap sejuk'
            ],
            [
                'tanaman' => 'Selada',
                'fase' => 'panen',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 1.4, 'ec_max' => 1.8,
                'ppm_min' => 700, 'ppm_max' => 900,
                'dosis_a' => 3.5, 'dosis_b' => 3.5,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 25,
                'peringatan' => 'Turunkan EC agar rasa tidak pahit'
            ],

            // KANGKUNG
            [
                'tanaman' => 'Kangkung',
                'fase' => 'semai',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 0.5, 'ec_max' => 0.7,
                'ppm_min' => 280, 'ppm_max' => 350,
                'dosis_a' => 2.0, 'dosis_b' => 2.0,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 22, 'suhu_max' => 30,
                'peringatan' => 'Kangkung toleran suhu tinggi'
            ],
            [
                'tanaman' => 'Kangkung',
                'fase' => 'vegetatif_awal',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 1.0, 'ec_max' => 1.4,
                'ppm_min' => 500, 'ppm_max' => 700,
                'dosis_a' => 2.5, 'dosis_b' => 2.5,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 22, 'suhu_max' => 30,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Kangkung',
                'fase' => 'vegetatif_akhir',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 1.4, 'ec_max' => 1.8,
                'ppm_min' => 700, 'ppm_max' => 900,
                'dosis_a' => 3.5, 'dosis_b' => 3.5,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 22, 'suhu_max' => 30,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Kangkung',
                'fase' => 'panen',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 1.2, 'ec_max' => 1.6,
                'ppm_min' => 600, 'ppm_max' => 800,
                'dosis_a' => 3.0, 'dosis_b' => 3.0,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 22, 'suhu_max' => 28,
                'peringatan' => null
            ],

            // PAKCOY
            [
                'tanaman' => 'Pakcoy',
                'fase' => 'semai',
                'ph_min' => 6.0, 'ph_max' => 7.0,
                'ec_min' => 0.7, 'ec_max' => 0.9,
                'ppm_min' => 350, 'ppm_max' => 450,
                'dosis_a' => 2.5, 'dosis_b' => 2.5,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => 'pH toleransi lebih tinggi'
            ],
            [
                'tanaman' => 'Pakcoy',
                'fase' => 'vegetatif_awal',
                'ph_min' => 6.0, 'ph_max' => 7.0,
                'ec_min' => 1.2, 'ec_max' => 1.6,
                'ppm_min' => 600, 'ppm_max' => 800,
                'dosis_a' => 3.0, 'dosis_b' => 3.0,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Pakcoy',
                'fase' => 'vegetatif_akhir',
                'ph_min' => 6.0, 'ph_max' => 7.0,
                'ec_min' => 1.6, 'ec_max' => 2.0,
                'ppm_min' => 800, 'ppm_max' => 1000,
                'dosis_a' => 4.0, 'dosis_b' => 4.0,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 26,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Pakcoy',
                'fase' => 'panen',
                'ph_min' => 6.0, 'ph_max' => 7.0,
                'ec_min' => 1.8, 'ec_max' => 2.0,
                'ppm_min' => 900, 'ppm_max' => 1000,
                'dosis_a' => 4.0, 'dosis_b' => 4.0,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 26,
                'peringatan' => 'Siap panen hari 25-35'
            ],

            // TOMAT
            [
                'tanaman' => 'Tomat',
                'fase' => 'semai',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 0.8, 'ec_max' => 1.2,
                'ppm_min' => 400, 'ppm_max' => 600,
                'dosis_a' => 3.0, 'dosis_b' => 3.0,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => 'Tomat butuh cahaya penuh'
            ],
            [
                'tanaman' => 'Tomat',
                'fase' => 'vegetatif_awal',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 1.6, 'ec_max' => 2.0,
                'ppm_min' => 800, 'ppm_max' => 1000,
                'dosis_a' => 4.0, 'dosis_b' => 4.0,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Tomat',
                'fase' => 'vegetatif_akhir',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 2.0, 'ec_max' => 2.5,
                'ppm_min' => 1000, 'ppm_max' => 1250,
                'dosis_a' => 5.0, 'dosis_b' => 5.0,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => 'Mulai persiapan penyerbukan'
            ],
            [
                'tanaman' => 'Tomat',
                'fase' => 'panen',
                'ph_min' => 5.5, 'ph_max' => 6.5,
                'ec_min' => 2.0, 'ec_max' => 3.0,
                'ppm_min' => 1000, 'ppm_max' => 1500,
                'dosis_a' => 5.0, 'dosis_b' => 5.0,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => 'Tingkatkan kalium untuk kualitas buah'
            ],

            // CABAI
            [
                'tanaman' => 'Cabai',
                'fase' => 'semai',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 0.8, 'ec_max' => 1.2,
                'ppm_min' => 400, 'ppm_max' => 600,
                'dosis_a' => 3.0, 'dosis_b' => 3.0,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => 'Butuh kelembaban tinggi saat semai'
            ],
            [
                'tanaman' => 'Cabai',
                'fase' => 'vegetatif_awal',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 1.4, 'ec_max' => 1.8,
                'ppm_min' => 700, 'ppm_max' => 900,
                'dosis_a' => 3.5, 'dosis_b' => 3.5,
                'ganti_larutan' => 7,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Cabai',
                'fase' => 'vegetatif_akhir',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 1.8, 'ec_max' => 2.2,
                'ppm_min' => 900, 'ppm_max' => 1100,
                'dosis_a' => 4.5, 'dosis_b' => 4.5,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => null
            ],
            [
                'tanaman' => 'Cabai',
                'fase' => 'panen',
                'ph_min' => 6.0, 'ph_max' => 6.5,
                'ec_min' => 2.0, 'ec_max' => 2.8,
                'ppm_min' => 1000, 'ppm_max' => 1400,
                'dosis_a' => 5.0, 'dosis_b' => 5.0,
                'ganti_larutan' => 5,
                'isi_ulang' => 2, 'cek_ph_ec' => 1, 'suhu_min' => 20, 'suhu_max' => 28,
                'peringatan' => 'Kurangi air saat buah mulai memerah'
            ],
        ];

        foreach ($rulesData as $rule) {
            $tanamanId = $tanamanMap[$rule['tanaman']] ?? null;

            if ($tanamanId) {
                RuleNutrisi::updateOrCreate(
                    [
                        'tanaman_id' => $tanamanId,
                        'fase' => $rule['fase']
                    ],
                    [
                        'ph_min' => $rule['ph_min'],
                        'ph_max' => $rule['ph_max'],
                        'ec_min' => $rule['ec_min'],
                        'ec_max' => $rule['ec_max'],
                        'ppm_min' => $rule['ppm_min'],
                        'ppm_max' => $rule['ppm_max'],
                        'dosis_a' => $rule['dosis_a'],
                        'dosis_b' => $rule['dosis_b'],
                        'ganti_larutan' => $rule['ganti_larutan'],
                        'isi_ulang' => $rule['isi_ulang'],
                        'cek_ph_ec' => $rule['cek_ph_ec'],
                        'suhu_min' => $rule['suhu_min'],
                        'suhu_max' => $rule['suhu_max'],
                        'peringatan' => $rule['peringatan'],
                    ]
                );
            }
        }
    }
}
