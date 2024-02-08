<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Games extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'number_of_teams',
        'number_of_words',
        'round_time',
        'current_turn',
        'is_game_running',
        'random_pick_of_players',
        'categories',
    ];

}
