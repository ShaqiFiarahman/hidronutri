<?php

namespace Database\Seeders;

use App\Models\Tanaman;
use App\Models\FaseTanaman;
use Illuminate\Database\Seeder;

class FaseTanamanSeeder extends Seeder
{
    /**
     * Mengisi tabel fase_tanaman berdasarkan data narasumber:
     * Tabel "RULE FASE — Durasi Transisi Antar Fase Pertumbuhan"
     *
     * ID Rule → Tanaman → Fase → Durasi
     * ─────────────────────────────────────────────────────
     * RFase-01: Selada  Semai → Pindah Tanam     : 10-14 hr (dipakai 12 sebagai tengah)
     * RFase-02: Selada  Vegetatif Awal → Akhir   : 24 hari
     * RFase-03: Selada  Total hingga Panen        : 40 hari
     * RFase-04: Kangkung Semai → Pindah Tanam    : 7-10 hr (dipakai 10)
     * RFase-05: Kangkung Total hingga Panen       : 21 hari
     * RFase-06: Pakcoy  Semai → Pindah Tanam     : 7-10 hr (dipakai 10)
     * RFase-07: Pakcoy  Total hingga Panen        : 28-30 hr (dipakai 30)
     * RFase-08: Cabai   Semai → Pindah Tanam     : 21-30 hr (dipakai 30)
     * RFase-09: Cabai   Masuk Fase Generatif      : 45 hari (dari bibit)
     * RFase-10: Melon   Semai → Pindah Tanam     : 14 hari
     * RFase-11: Melon   Masuk Fase Generatif      : 25-30 hr (dipakai 30, dari pindah tanam)
     *
     * Catatan Melon: Fase pertama adalah 'vegetatif' (RPPM-18, bukan 'semai')
     * karena narasumber tidak menyebutkan fase semai khusus untuk Melon.
     */
    public function run(): void
    {
        $tanamanMap = Tanaman::all()->pluck('id', 'nama');

        $data = [
            // ────────────────────────────────────────────────
            // SELADA — RFase-01 s/d RFase-03
            // Total: 40 hari
            // ────────────────────────────────────────────────
            ['tanaman' => 'Selada', 'fase' => 'semai',          'urutan' => 1, 'durasi' => 12, 'kumulatif' => 12],
            ['tanaman' => 'Selada', 'fase' => 'vegetatif_awal', 'urutan' => 2, 'durasi' => 16, 'kumulatif' => 28], // RFase-02: 24 hari setelah semai (kumulatif 28 - 12 = 16)
            ['tanaman' => 'Selada', 'fase' => 'vegetatif_akhir','urutan' => 3, 'durasi' => 12, 'kumulatif' => 40], // Sisa hingga 40 hari
            ['tanaman' => 'Selada', 'fase' => 'panen',          'urutan' => 4, 'durasi' => 0,  'kumulatif' => 40], // RFase-03: total 40 hari

            // ────────────────────────────────────────────────
            // KANGKUNG — RFase-04 s/d RFase-05
            // Total: 21 hari
            // ────────────────────────────────────────────────
            ['tanaman' => 'Kangkung', 'fase' => 'semai',          'urutan' => 1, 'durasi' => 10, 'kumulatif' => 10], // RFase-04: 7-10 hr
            ['tanaman' => 'Kangkung', 'fase' => 'vegetatif_awal', 'urutan' => 2, 'durasi' => 5,  'kumulatif' => 15],
            ['tanaman' => 'Kangkung', 'fase' => 'vegetatif_akhir','urutan' => 3, 'durasi' => 6,  'kumulatif' => 21], // RFase-05: total 21 hr
            ['tanaman' => 'Kangkung', 'fase' => 'panen',          'urutan' => 4, 'durasi' => 0,  'kumulatif' => 21],

            // ────────────────────────────────────────────────
            // PAKCOY — RFase-06 s/d RFase-07
            // Total: 28-30 hari (dipakai 30)
            // ────────────────────────────────────────────────
            ['tanaman' => 'Pakcoy', 'fase' => 'semai',          'urutan' => 1, 'durasi' => 10, 'kumulatif' => 10], // RFase-06: 7-10 hr
            ['tanaman' => 'Pakcoy', 'fase' => 'vegetatif_awal', 'urutan' => 2, 'durasi' => 10, 'kumulatif' => 20],
            ['tanaman' => 'Pakcoy', 'fase' => 'vegetatif_akhir','urutan' => 3, 'durasi' => 10, 'kumulatif' => 30], // RFase-07: total 28-30 hr
            ['tanaman' => 'Pakcoy', 'fase' => 'panen',          'urutan' => 4, 'durasi' => 0,  'kumulatif' => 30],

            // ────────────────────────────────────────────────
            // CABAI — RFase-08 s/d RFase-09
            // Total: ~100 hari (indikatif, fase panen terbuka)
            // RFase-08: Semai 21-30 hr (dipakai 30)
            // RFase-09: Masuk generatif di 45 hr dari bibit
            // ────────────────────────────────────────────────
            ['tanaman' => 'Cabai', 'fase' => 'semai',      'urutan' => 1, 'durasi' => 30, 'kumulatif' => 30],  // RFase-08
            ['tanaman' => 'Cabai', 'fase' => 'vegetatif',  'urutan' => 2, 'durasi' => 15, 'kumulatif' => 45],  // RFase-09: 45 hr dari bibit
            ['tanaman' => 'Cabai', 'fase' => 'pembungaan', 'urutan' => 3, 'durasi' => 20, 'kumulatif' => 65],
            ['tanaman' => 'Cabai', 'fase' => 'pembuahan',  'urutan' => 4, 'durasi' => 20, 'kumulatif' => 85],
            ['tanaman' => 'Cabai', 'fase' => 'pembesaran', 'urutan' => 5, 'durasi' => 15, 'kumulatif' => 100],

            // ────────────────────────────────────────────────
            // MELON — RFase-10 s/d RFase-11
            // Fase sesuai narasumber: Vegetatif → Transisi → Pembesaran → Pematangan → Panen
            // RPPM-18: Vegetatif 900-1000 | RPPM-19: Transisi 1200-1500 | dst.
            // RFase-10: Semai → Pindah Tanam 14 hari (ini adalah fase vegetatif awal)
            // RFase-11: Masuk Fase Generatif 25-30 hr dari pindah tanam (dipakai 30)
            // ────────────────────────────────────────────────
            ['tanaman' => 'Melon', 'fase' => 'vegetatif',  'urutan' => 1, 'durasi' => 14, 'kumulatif' => 14],  // RFase-10
            ['tanaman' => 'Melon', 'fase' => 'transisi',   'urutan' => 2, 'durasi' => 16, 'kumulatif' => 30],  // RFase-11: generatif di hr ke 25-30
            ['tanaman' => 'Melon', 'fase' => 'pembesaran', 'urutan' => 3, 'durasi' => 30, 'kumulatif' => 60],
            ['tanaman' => 'Melon', 'fase' => 'pematangan', 'urutan' => 4, 'durasi' => 20, 'kumulatif' => 80],
            ['tanaman' => 'Melon', 'fase' => 'panen',      'urutan' => 5, 'durasi' => 0,  'kumulatif' => 80],  // RPPM-22: 1100 PPM
        ];

        foreach ($data as $item) {
            $tanamanId = $tanamanMap[$item['tanaman']] ?? null;
            if (!$tanamanId) continue;

            FaseTanaman::updateOrCreate(
                ['tanaman_id' => $tanamanId, 'fase' => $item['fase']],
                [
                    'urutan'         => $item['urutan'],
                    'durasi_hari'    => $item['durasi'],
                    'kumulatif_hari' => $item['kumulatif'],
                ]
            );
        }
    }
}
