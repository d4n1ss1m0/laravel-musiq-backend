<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listening_attempts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('history_item_id')
                ->constrained('listening_history')
                ->cascadeOnDelete();

            $table->unsignedInteger('credited_seconds')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            $table->index(['history_item_id', 'finished_at']);
            $table->index('last_reported_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listening_attempts');
    }
};
