<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeamUser;

class Teams extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'color',
    ];

    // a team can have many users
    // public function users()
    // {
    //     return $this->belongsToMany(TeamUser::class, 'team_users', 'team_id', 'user_id');
    // }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_users', 'team_id', 'user_id');
    }

}
