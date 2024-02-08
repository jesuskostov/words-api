<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->integer('number_of_teams');
            $table->integer('number_of_words');
            $table->integer('round_time');
            $table->integer('rounds')->default(1);
            $table->boolean('random_pick_of_players')->default(false);
            $table->integer('current_turn')->default(1);
            $table->boolean('is_game_running')->default(false);
            $table->integer('timer_start')->nullable();
            $table->enum('timer_state', ['running', 'paused', 'stopped'])->default('stopped');
            $table->integer('timer_elapsed')->nullable();
            $table->string('categories')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
