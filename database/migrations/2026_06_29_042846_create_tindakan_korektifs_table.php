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
        Schema::create('tindakan_korektifs', function (Blueprint $table) {
            $table->id();
            $table->string('parameter'); // pH, EC, PPM
            $table->string('kondisi'); // rendah, tinggi
            $table->text('tindakan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindakan_korektifs');
    }
};
