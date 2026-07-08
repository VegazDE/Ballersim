<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table): void {
            $table->json('match_report')->nullable()->after('away_score');
            $table->timestamp('simulated_at')->nullable()->after('match_report');
            $table->unsignedTinyInteger('home_xg')->nullable()->after('simulated_at');
            $table->unsignedTinyInteger('away_xg')->nullable()->after('home_xg');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table): void {
            $table->dropColumn(['match_report', 'simulated_at', 'home_xg', 'away_xg']);
        });
    }
};
