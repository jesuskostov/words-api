<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_of_teams',
        'number_of_words',
        'round_time',
        'current_turn',
    ];

}
