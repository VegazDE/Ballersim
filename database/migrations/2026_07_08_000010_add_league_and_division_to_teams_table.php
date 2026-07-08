<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->foreignId('league_id')->nullable()->after('manager_user_id')->constrained()->nullOnDelete();
            $table->foreignId('division_id')->nullable()->after('league_id')->constrained()->nullOnDelete();

            $table->index(['league_id', 'division_id']);
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropIndex('teams_league_id_division_id_index');
            $table->dropConstrainedForeignId('division_id');
            $table->dropConstrainedForeignId('league_id');
        });
    }
};
