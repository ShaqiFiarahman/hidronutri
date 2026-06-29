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
        Schema::create('log_perawatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_tanam_id')->constrained('sesi_tanam')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('tipe', ['cek', 'isi_ulang']);
            $table->decimal('ph', 4, 2)->nullable();
            $table->integer('ppm')->nullable();
            $table->decimal('suhu', 4, 1)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['selesai', 'perlu_perhatian'])->default('selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_perawatan');
    }
};
