<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TeamUser;

class User extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [ 'name' ];

    // a user can have many teams
    public function teams()
    {
        return $this->belongsToMany(TeamUser::class, 'team_users', 'user_id', 'team_id');
    }

}
