<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tanaman;
use App\Models\RuleNutrisi;

class CleanupSeeder extends Seeder
{
    public function run(): void
    {
        $tomat = Tanaman::where('nama', 'Tomat')->first();
        if ($tomat) {
            RuleNutrisi::where('tanaman_id', $tomat->id)->delete();
            $tomat->delete();
        }
    }
}
