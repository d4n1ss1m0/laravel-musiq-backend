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
        Schema::table('playback_session_tracks', function (Blueprint $table) {
            $table->enum('origin', ['source', 'manual'])->default('source');
            $table->enum('placement', ['next', 'tail'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playback_session_tracks', function (Blueprint $table) {
            $table->dropColumn('origin');
            $table->dropColumn('placement');
        });
    }
};
