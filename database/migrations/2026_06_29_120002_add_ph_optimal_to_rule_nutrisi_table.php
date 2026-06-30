<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom ph_optimal_min dan ph_optimal_max ke rule_nutrisi
     * sesuai dengan RpH-03: pH optimal 5.8 - 6.2
     */
    public function up(): void
    {
        Schema::table('rule_nutrisi', function (Blueprint $table) {
            $table->decimal('ph_optimal_min', 4, 2)->nullable()->after('ph_max');
            $table->decimal('ph_optimal_max', 4, 2)->nullable()->after('ph_optimal_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rule_nutrisi', function (Blueprint $table) {
            $table->dropColumn(['ph_optimal_min', 'ph_optimal_max']);
        });
    }
};
