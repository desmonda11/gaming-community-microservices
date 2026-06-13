<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->integer('matches_played')->default(0);
            $table->integer('win')->default(0);
            $table->integer('lose')->default(0);
            $table->integer('kill')->default(0);
            $table->integer('death')->default(0);
            $table->integer('assist')->default(0);
            $table->decimal('kda', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
    }
};
