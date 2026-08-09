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
        Schema::create('playback_manual_queue', function (Blueprint $table) {
            $table->foreignId('session_id')
                ->constrained('playback_sessions')
                ->cascadeOnDelete();
            $table->foreignId('track_id')
                ->constrained('tracks')
                ->cascadeOnDelete();
            $table->enum('placement', ['next', 'tail']);
            $table->unsignedBigInteger('source_position');
            $table->unsignedBigInteger('playback_position');

            $table->primary(['session_id', 'placement', 'source_position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playback_manual_queue');
    }
};
