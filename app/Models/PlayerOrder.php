<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PlayerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'user_id',
        'order',
    ];

    // public function user() {
    //     return $this->belongsTo(User::class);
    // }
    
}
