<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TeamUser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable

{
    use HasFactory, HasApiTokens;

    protected $fillable = [ 'name', 'is_admin', 'photo_path', 'game_id' ];

    // a user can have many teams
    public function teams()
    {
        return $this->belongsToMany(TeamUser::class, 'team_users', 'user_id', 'team_id');
    }

}
