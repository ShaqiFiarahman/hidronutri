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
        Schema::table('sesi_tanam', function (Blueprint $table) {
            // Add user_id as string/uuid because Supabase users.id is UUID
            // nullable so we don't break existing data
            $table->uuid('user_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_tanam', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
