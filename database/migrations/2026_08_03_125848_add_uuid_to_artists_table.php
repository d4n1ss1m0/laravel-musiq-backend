<?php

use App\Models\Artist;
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
        Schema::table('artists', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        Artist::query()
            ->whereNull('uuid')
            ->each(function (Artist $artist) {
                $artist->update([
                    'uuid' => (string) Str::uuid7(),
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
