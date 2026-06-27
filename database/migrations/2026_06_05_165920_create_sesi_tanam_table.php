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
        Schema::create('sesi_tanam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_id')->constrained('tanaman')->onDelete('cascade');
            $table->enum('sistem_hidroponik', ['nft', 'dft', 'rakit_apung', 'wick']);
            $table->enum('fase_saat_ini', ['semai', 'vegetatif_awal', 'vegetatif_akhir', 'panen']);
            $table->date('tanggal_mulai');
            $table->enum('status', ['aktif', 'panen'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_tanam');
    }
};
