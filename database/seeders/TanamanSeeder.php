<?php

namespace Database\Seeders;

use App\Models\Tanaman;
use Illuminate\Database\Seeder;

class TanamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tanamanList = [
            [
                'nama' => 'Selada',
                'emoji' => '🥬',
                'kategori' => 'daun',
                'foto_url' => 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?w=400&q=80&auto=format&fit=crop',
            ],
            [
                'nama' => 'Kangkung',
                'emoji' => '🌿',
                'kategori' => 'daun',
                'foto_url' => 'images/kangkung.png',
            ],
            [
                'nama' => 'Pakcoy',
                'emoji' => '🥦',
                'kategori' => 'daun',
                'foto_url' => 'https://images.unsplash.com/photo-1597362925123-77861d3fbac7?w=400&q=80&auto=format&fit=crop',
            ],
            [
                'nama' => 'Tomat',
                'emoji' => '🍅',
                'kategori' => 'buah',
                'foto_url' => 'https://images.unsplash.com/photo-1592841200221-a6898f307baa?w=400&q=80&auto=format&fit=crop',
            ],
            [
                'nama' => 'Cabai',
                'emoji' => '🌶️',
                'kategori' => 'buah',
                'foto_url' => 'https://images.unsplash.com/photo-1583119022894-919a68a3d0e3?w=400&q=80&auto=format&fit=crop',
            ],
        ];

        foreach ($tanamanList as $item) {
            Tanaman::updateOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
