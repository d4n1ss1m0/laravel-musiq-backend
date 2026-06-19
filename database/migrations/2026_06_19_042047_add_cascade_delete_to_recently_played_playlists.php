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
        Schema::table('recently_played_playlists', function (Blueprint $table) {
            $table->dropForeign(['playlist_id']);

            $table->foreign('playlist_id')
                ->references('id')
                ->on('playlists')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recently_played_playlists', function (Blueprint $table) {
            $table->dropForeign(['playlist_id']);

            $table->foreign('playlist_id')
                ->references('id')
                ->on('playlists');
        });
    }
};
