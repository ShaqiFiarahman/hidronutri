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
        Schema::table('rule_nutrisi', function (Blueprint $table) {
            $table->dropColumn(['ec_min', 'ec_max']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rule_nutrisi', function (Blueprint $table) {
            $table->decimal('ec_min', 4, 2)->nullable();
            $table->decimal('ec_max', 4, 2)->nullable();
        });
    }
};
