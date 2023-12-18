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
            $table->boolean('random_pick_of_players')->default(false);
            $table->foreignId('current_turn')->nullable()->constrained('users')->onDelete('set null');
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
