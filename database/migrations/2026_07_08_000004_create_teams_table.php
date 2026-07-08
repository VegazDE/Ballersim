<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->boolean('is_cpu')->default(false);
            $table->string('division_name', 32)->nullable();
            $table->timestamps();

            $table->index('division_name');
            $table->index('is_cpu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
