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
        Schema::dropIfExists('riwayat_diagnosa');

        Schema::table('rule_nutrisi', function (Blueprint $table) {
            $table->dropColumn('isi_ulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rule_nutrisi', function (Blueprint $table) {
            $table->integer('isi_ulang')->nullable();
        });

        Schema::create('riwayat_diagnosa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_tanam_id')->constrained('sesi_tanam')->onDelete('cascade');
            $table->decimal('ph_aktual', 4, 2);
            $table->decimal('ec_aktual', 4, 2)->nullable();
            $table->integer('ppm_aktual');
            $table->json('hasil_diagnosa')->nullable();
            $table->timestamps();
        });
    }
};
