<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGamesRequest;
use App\Http\Requests\UpdateGamesRequest;
use App\Models\Games;
use Illuminate\Http\Request;
use App\Http\Controllers\TeamsController;
use App\Models\Teams;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        ]);

        // create teams and pass number_of_teams
        $teamsController = new TeamsController();
        $teamsController->create($request->number_of_teams, $game->id);
    }

}
