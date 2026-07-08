<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('matchday');
            $table->unsignedTinyInteger('leg')->default(1);
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();
            $table->timestamp('kickoff_at')->nullable();
            $table->enum('status', ['scheduled', 'live', 'finished'])->default('scheduled');
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->timestamps();

            $table->index(['season_id', 'division_id', 'matchday']);
            $table->index('status');
            $table->unique(['season_id', 'division_id', 'matchday', 'home_team_id', 'away_team_id'], 'uq_fixture_pairing');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
