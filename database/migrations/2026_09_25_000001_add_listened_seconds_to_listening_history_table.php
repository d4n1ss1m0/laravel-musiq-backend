<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listening_history', function (Blueprint $table) {
            $table->unsignedBigInteger('listened_seconds')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('listening_history', function (Blueprint $table) {
            $table->dropColumn('listened_seconds');
        });
    }
};
