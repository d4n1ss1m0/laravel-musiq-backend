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
        Schema::table('track_playlists', function (Blueprint $table) {
            $table->unique(['track_id', 'playlist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('track_playlists', function (Blueprint $table) {
            $table->dropUnique(['track_id', 'playlist_id']);
        });
    }
};
