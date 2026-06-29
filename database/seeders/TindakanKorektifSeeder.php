<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TindakanKorektifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\TindakanKorektif::truncate();

        $data = [
            ['parameter' => 'pH', 'kondisi' => 'rendah', 'tindakan' => 'Air saat ini terlalu asam. PENTING: Jangan tambahkan pH Up dulu! Pastikan Anda sudah memasukkan nutrisi (A & B) sesuai target PPM dan aduk rata. Jika setelah nutrisi tercampur nilai pH masih kurang, barulah gunakan cairan pH Up sedikit demi sedikit.'],
            ['parameter' => 'pH', 'kondisi' => 'tinggi', 'tindakan' => 'Air saat ini terlalu basa. PENTING: Jangan tambahkan pH Down dulu! Selalu berikan nutrisi (A & B) terlebih dahulu hingga mencapai target PPM dan biarkan tercampur. Jika setelah ditambahkan nutrisi nilai pH masih tinggi, barulah gunakan cairan pH Down secara bertahap.'],
            ['parameter' => 'EC', 'kondisi' => 'rendah', 'tindakan' => 'Kepekatan nutrisi masih kurang. Tambahkan larutan nutrisi (A dan B) dengan takaran seimbang.'],
            ['parameter' => 'EC', 'kondisi' => 'tinggi', 'tindakan' => 'Nutrisi terlalu pekat. Segera tambahkan air bersih (sebaiknya lakukan di pagi/sore hari) untuk mengencerkannya. Jika sudah sangat berlebih, disarankan untuk menguras tandon.'],
            ['parameter' => 'PPM', 'kondisi' => 'rendah', 'tindakan' => 'Tanaman kekurangan nutrisi. Sebagai patokan dasar: berikan 5 ml nutrisi A + 5 ml nutrisi B untuk setiap 1 liter air demi menaikkan 100 PPM.'],
            ['parameter' => 'PPM', 'kondisi' => 'tinggi', 'tindakan' => 'Nutrisi terlalu pekat dan berisiko merusak akar! Segera tambahkan air bersih ke dalam tandon (lakukan saat pagi atau sore hari). Jika pekatnya sudah kelewatan, sebaiknya kuras air tandon Anda dan buat racikan baru.'],
        ];

        foreach ($data as $item) {
            \App\Models\TindakanKorektif::create($item);
        }
    }
}
