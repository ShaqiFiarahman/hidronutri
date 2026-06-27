<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SesiTanam;
use App\Models\RuleNutrisi;

class CheckJadwalSeeder extends Seeder
{
    public function run(): void
    {
        $sesi = SesiTanam::where('status', 'aktif')->latest()->first();
        $rule = RuleNutrisi::where('tanaman_id', $sesi->tanaman_id)
            ->where('fase', $sesi->fase_saat_ini)
            ->first();
            
        echo "Cek: " . $rule->cek_ph_ec . "\n";
        echo "Isi Ulang: " . $rule->isi_ulang . "\n";
        echo "Ganti: " . $rule->ganti_larutan . "\n";
        
        $jadwalSeminggu = [];
        $usiaHari = 0;
        for ($i = 0; $i < 7; $i++) {
            $totalHariSejakMulai = $usiaHari + $i;
            $kegiatan = [];

            $intervalCek = $rule->cek_ph_ec ?? 1;
            if ($totalHariSejakMulai % $intervalCek === 0) {
                $kegiatan[] = 'cek';
            }

            $intervalIsiUlang = $rule->isi_ulang ?? 2;
            if ($totalHariSejakMulai % $intervalIsiUlang === 0) {
                $kegiatan[] = 'isi';
            }

            $intervalGanti = $rule->ganti_larutan ?? 7;
            if ($totalHariSejakMulai % $intervalGanti === 0) {
                $kegiatan[] = 'ganti';
            }

            if (!empty($kegiatan)) {
                $jadwalSeminggu[] = $kegiatan;
            }
        }
        echo "Jadwal count: " . count($jadwalSeminggu) . "\n";
    }
}
