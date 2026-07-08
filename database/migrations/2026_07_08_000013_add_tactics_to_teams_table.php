<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->string('formation', 16)->default('4-3-3')->after('division_name');
            $table->string('mentality', 16)->default('balanced')->after('formation');
            $table->unsignedTinyInteger('pressing')->default(50)->after('mentality');
            $table->unsignedTinyInteger('tempo')->default(50)->after('pressing');
            $table->unsignedTinyInteger('substitution_style')->default(50)->after('tempo');
            $table->unsignedTinyInteger('line_height')->default(50)->after('substitution_style');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropColumn(['formation', 'mentality', 'pressing', 'tempo', 'substitution_style', 'line_height']);
        });
    }
};
