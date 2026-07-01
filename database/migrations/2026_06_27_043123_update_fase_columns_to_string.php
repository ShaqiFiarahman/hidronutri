<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah kolom 'fase' dari ENUM ke VARCHAR agar fleksibel
     * menampung berbagai jenis fase per tanaman (Cabai: pembungaan, pembuahan, dll; Melon: transisi, pematangan, dll).
     */
    public function up(): void
    {
        // PostgreSQL: ubah tipe kolom langsung via ALTER dan hapus check constraint
        DB::statement("ALTER TABLE rule_nutrisi DROP CONSTRAINT IF EXISTS rule_nutrisi_fase_check");
        DB::statement("ALTER TABLE rule_nutrisi ALTER COLUMN fase TYPE VARCHAR(50)");
        
        DB::statement("ALTER TABLE sesi_tanam DROP CONSTRAINT IF EXISTS sesi_tanam_fase_saat_ini_check");
        DB::statement("ALTER TABLE sesi_tanam ALTER COLUMN fase_saat_ini TYPE VARCHAR(50)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke ENUM jika di-rollback
        DB::statement("ALTER TABLE rule_nutrisi ALTER COLUMN fase TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE sesi_tanam ALTER COLUMN fase_saat_ini TYPE VARCHAR(50)");
    }
};
