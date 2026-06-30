<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel fase_tanaman untuk menyimpan data durasi tiap fase pertumbuhan.
     * Data ini sebelumnya hardcoded di RuleBasedEngine::getDurasiFaseMap().
     * Sumber: Tabel RULE FASE – Durasi Transisi Antar Fase Pertumbuhan (narasumber).
     */
    public function up(): void
    {
        Schema::create('fase_tanaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_id')->constrained('tanaman')->onDelete('cascade');
            $table->string('fase', 50);
            $table->integer('urutan');          // Urutan fase (1, 2, 3, ...) untuk sorting
            $table->integer('durasi_hari');     // Durasi fase ini saja
            $table->integer('kumulatif_hari'); // Total hari sejak hari pertama tanam
            $table->timestamps();

            $table->unique(['tanaman_id', 'fase']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fase_tanaman');
    }
};
