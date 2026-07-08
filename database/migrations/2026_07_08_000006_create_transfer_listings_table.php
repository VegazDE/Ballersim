<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_listings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedInteger('asking_price');
            $table->unsignedInteger('minimum_bid')->nullable();
            $table->timestamp('listed_at');
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['open', 'sold', 'cancelled', 'expired'])->default('open');
            $table->foreignId('sold_to_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->unsignedInteger('sold_price')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index('seller_team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_listings');
    }
};
