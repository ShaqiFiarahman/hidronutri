<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbaiki nama fase Melon di tabel rule_nutrisi agar sesuai data narasumber:
     * - 'semai'     → 'vegetatif'  (RPPM-18: Melon Vegetatif, 900-1000 PPM)
     * - 'vegetatif' → 'transisi'   (RPPM-19: Melon Transisi, 1200-1500 PPM)
     *
     * Urutan update penting: ubah 'vegetatif' dulu ke nama sementara agar tidak clash.
     */
    public function up(): void
    {
        $melonId = DB::table('tanaman')->where('nama', 'Melon')->value('id');

        if ($melonId) {
            // Langkah 1: rename 'vegetatif' ke nama sementara agar tidak bentrok
            DB::table('rule_nutrisi')
                ->where('tanaman_id', $melonId)
                ->where('fase', 'vegetatif')
                ->update(['fase' => '_transisi_tmp']);

            // Langkah 2: rename 'semai' → 'vegetatif'
            DB::table('rule_nutrisi')
                ->where('tanaman_id', $melonId)
                ->where('fase', 'semai')
                ->update(['fase' => 'vegetatif']);

            // Langkah 3: rename nama sementara → 'transisi'
            DB::table('rule_nutrisi')
                ->where('tanaman_id', $melonId)
                ->where('fase', '_transisi_tmp')
                ->update(['fase' => 'transisi']);
        }
    }

    /**
     * Kembalikan nama fase Melon ke kondisi semula jika di-rollback.
     */
    public function down(): void
    {
        $melonId = DB::table('tanaman')->where('nama', 'Melon')->value('id');

        if ($melonId) {
            DB::table('rule_nutrisi')
                ->where('tanaman_id', $melonId)
                ->where('fase', 'transisi')
                ->update(['fase' => '_vegetatif_tmp']);

            DB::table('rule_nutrisi')
                ->where('tanaman_id', $melonId)
                ->where('fase', 'vegetatif')
                ->update(['fase' => 'semai']);

            DB::table('rule_nutrisi')
                ->where('tanaman_id', $melonId)
                ->where('fase', '_vegetatif_tmp')
                ->update(['fase' => 'vegetatif']);
        }
    }
};
