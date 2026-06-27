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
        Schema::create('riwayat_diagnosa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_tanam_id')->constrained('sesi_tanam')->onDelete('cascade');
            $table->decimal('ph_aktual', 4, 2);
            $table->decimal('ec_aktual', 4, 2);
            $table->integer('ppm_aktual');
            $table->json('hasil_diagnosa');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_diagnosa');
    }
};
