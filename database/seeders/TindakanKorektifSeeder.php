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
            ['parameter' => 'pH',  'kondisi' => 'rendah', 'tindakan' => 'Air saat ini terlalu asam. PENTING: Jangan tambahkan pH Up dulu! Pastikan Anda sudah memasukkan nutrisi (A & B) sesuai target PPM dan aduk rata. Jika setelah nutrisi tercampur nilai pH masih kurang, barulah gunakan cairan pH Up sedikit demi sedikit.'],
            ['parameter' => 'pH',  'kondisi' => 'tinggi', 'tindakan' => 'Air saat ini terlalu basa. PENTING: Jangan tambahkan pH Down dulu! Selalu berikan nutrisi (A & B) terlebih dahulu hingga mencapai target PPM dan biarkan tercampur. Jika setelah ditambahkan nutrisi nilai pH masih tinggi, barulah gunakan cairan pH Down secara bertahap.'],
            // EC tidak memiliki rule terpisah — EC dihitung otomatis dari PPM (EC = PPM / 500)
            ['parameter' => 'PPM', 'kondisi' => 'rendah', 'tindakan' => 'Tanaman kekurangan nutrisi. Sebagai patokan dasar: berikan 5 ml nutrisi A + 5 ml nutrisi B untuk setiap 1 liter air demi menaikkan 100 PPM.'],
            ['parameter' => 'PPM', 'kondisi' => 'tinggi', 'tindakan' => 'Nutrisi terlalu pekat dan berisiko merusak akar! Segera tambahkan air bersih ke dalam tandon (lakukan saat pagi atau sore hari). Jika pekatnya sudah kelewatan, sebaiknya kuras air tandon Anda dan buat racikan baru.'],
            ['parameter' => 'Suhu', 'kondisi' => 'rendah', 'tindakan' => 'Suhu air terlalu dingin. Gunakan pemanas air akuarium (heater) jika suhu terus drop, atau kurangi intensitas pendingin jika menggunakan water chiller.'],
            ['parameter' => 'Suhu', 'kondisi' => 'tinggi', 'tindakan' => 'Suhu air terlalu panas. Posisikan tandon cukup tertutup dan terhindar dari sinar matahari langsung. Pada instalasi bersirkulasi, pastikan debit air cukup (1,5 - 3 L/menit) dan ketersediaan air tandon cukup. Pada greenhouse, tambahkan exhaust fan/head fan untuk membuang udara panas.'],
        ];

        foreach ($data as $item) {
            \App\Models\TindakanKorektif::create($item);
        }
    }
}
