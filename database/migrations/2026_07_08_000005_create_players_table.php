<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('primary_position', 3);
            $table->unsignedTinyInteger('overall')->default(50);
            $table->unsignedInteger('market_value')->default(100000);
            $table->unsignedTinyInteger('fitness')->default(100);
            $table->boolean('is_transfer_listed')->default(false);
            $table->timestamps();

            $table->index(['team_id', 'overall']);
            $table->index('is_transfer_listed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
