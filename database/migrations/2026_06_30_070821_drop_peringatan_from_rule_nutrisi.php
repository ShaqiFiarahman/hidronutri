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
            $table->dropColumn('peringatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rule_nutrisi', function (Blueprint $table) {
            $table->text('peringatan')->nullable();
        });
    }
};
