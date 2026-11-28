<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_playlist_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('playlist_id')->constrained('playlists')->cascadeOnDelete();
            $table->string('sort');
            $table->timestamps();

            $table->unique(['user_id', 'playlist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_playlist_settings');
    }
};
