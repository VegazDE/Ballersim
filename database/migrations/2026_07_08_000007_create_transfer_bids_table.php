<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_bids', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transfer_listing_id')->constrained('transfer_listings')->cascadeOnDelete();
            $table->foreignId('bidder_team_id')->constrained('teams')->cascadeOnDelete();
            $table->unsignedInteger('bid_amount');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique(['transfer_listing_id', 'bidder_team_id', 'submitted_at'], 'uq_transfer_bid_submission');
            $table->index(['transfer_listing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_bids');
    }
};
