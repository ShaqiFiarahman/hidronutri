<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tanaman;
use App\Models\RuleNutrisi;

class FixCabaiSeeder extends Seeder
{
    public function run(): void
    {
        $cabai = Tanaman::where('nama', 'Cabai')->first();
        if ($cabai) {
            RuleNutrisi::where('tanaman_id', $cabai->id)
                ->whereNotIn('fase', ['semai', 'vegetatif', 'pembungaan', 'pembuahan', 'pembesaran'])
                ->delete();
        }
    }
}
