<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreGamesRequest;
use App\Http\Requests\UpdateGamesRequest;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\AuthController;
use App\Models\Teams;
use App\Models\Games;
use App\Models\User;
use App\Events\GameCreated;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    // find if we have active game
    public function isGameActive()
    {
        $game = Games::where('is_active', true)->first();
        if ($game) {
            return response()->json(['game' => $game], 200);
        } else {
            return response()->json(['game' => null], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
        $game = Games::create([
            'number_of_teams' => $request->number_of_teams,
            'number_of_words' => $request->number_of_words,
            'round_time' => $request->round_time,
            'current_turn' => null,
            'random_pick_of_players' => $request->random_pick_of_players,
            'categories' => $request->categories,
        ]);

        $user = User::create([
            'name' => $request->name,
            'is_admin' => true,
            'game_id' => $game->id,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;
        broadcast(new GameCreated())->toOthers();

        return response()->json(['token' => $token, 'user' => $user, 'game_id' => $game->id], 201);
    }

}
