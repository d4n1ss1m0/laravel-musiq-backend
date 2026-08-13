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
        Schema::create('playback_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->nullableMorphs('source');
            $table->foreignId('current_track_id')
                ->nullable()
                ->constrained('tracks')
                ->nullOnDelete();
            $table->unsignedBigInteger('current_position')->default(0);
            $table->boolean('shuffle')->default(false);
            $table->enum('repeat_mode', ['off','track','queue'])->default('off');
            $table->enum('state', ['playing', 'paused', 'finished'])->default('paused');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playback_sessions');
    }
};
