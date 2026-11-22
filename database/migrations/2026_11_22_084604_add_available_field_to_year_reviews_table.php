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
        Schema::table('year_reviews', function (Blueprint $table) {
            $table->dateTime('available_from')->nullable();
            $table->boolean('viewed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('year_reviews', function (Blueprint $table) {
            $table->dropColumn(['available_from', 'viewed']);
        });
    }
};
