<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan foreign key secara raw SQL agar bisa menunjuk ke schema auth
        DB::statement('ALTER TABLE sesi_tanam ADD CONSTRAINT fk_sesi_tanam_user_id FOREIGN KEY (user_id) REFERENCES auth.users (id) ON DELETE CASCADE;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE sesi_tanam DROP CONSTRAINT fk_sesi_tanam_user_id;');
    }
};
