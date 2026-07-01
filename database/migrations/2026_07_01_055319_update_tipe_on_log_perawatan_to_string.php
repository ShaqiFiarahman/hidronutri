<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the check constraint and alter the column to VARCHAR
        DB::statement('ALTER TABLE log_perawatan DROP CONSTRAINT IF EXISTS log_perawatan_tipe_check');
        DB::statement('ALTER TABLE log_perawatan ALTER COLUMN tipe TYPE VARCHAR(50)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To reverse, we'd have to recreate the constraint, but typically string is fine
    }
};
