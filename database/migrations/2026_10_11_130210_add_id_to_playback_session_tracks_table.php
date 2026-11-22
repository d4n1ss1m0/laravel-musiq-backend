<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('playback_session_tracks', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('playback_session_tracks', function (Blueprint $table) {
            $table->id();
            $table->unique(['session_id', 'source_position']);
        });
    }

    public function down(): void
    {
        Schema::table('playback_session_tracks', function (Blueprint $table) {
            $table->dropUnique(['session_id', 'source_position']);
            $table->dropColumn('id');
        });

        Schema::table('playback_session_tracks', function (Blueprint $table) {
            $table->primary(['session_id', 'source_position']);
        });
    }
};
