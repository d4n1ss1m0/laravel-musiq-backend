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
        Schema::table('playback_sessions', function (Blueprint $table) {
            $table->integer('pause_position_seconds')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playback_sessions', function (Blueprint $table) {
            $table->dropColumn('pause_position_seconds');
        });
    }
};
