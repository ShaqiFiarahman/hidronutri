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
        Schema::create('rule_nutrisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_id')->constrained('tanaman')->onDelete('cascade');
            $table->enum('fase', ['semai', 'vegetatif_awal', 'vegetatif_akhir', 'panen']);
            $table->decimal('ph_min', 4, 2);
            $table->decimal('ph_max', 4, 2);
            $table->decimal('ec_min', 4, 2);
            $table->decimal('ec_max', 4, 2);
            $table->integer('ppm_min');
            $table->integer('ppm_max');
            $table->decimal('dosis_a', 4, 2);
            $table->decimal('dosis_b', 4, 2);
            $table->integer('ganti_larutan');
            $table->integer('isi_ulang')->nullable();
            $table->integer('cek_ph_ec')->nullable();

            $table->text('peringatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_nutrisi');
    }
};
