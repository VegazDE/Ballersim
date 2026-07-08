<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->unsignedTinyInteger('position');
            $table->unsignedTinyInteger('teams_target')->default(12);
            $table->timestamps();

            $table->unique(['league_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
