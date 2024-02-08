<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScoresRequest;
use App\Http\Requests\UpdateScoresRequest;
use App\Models\Scores;
use App\Models\Games;
use App\Models\TeamUser;
use App\Models\User;

class ScoresController extends Controller
{

    public function getScoreboard() {
        // get active game
        $game = Games::where('is_active', 1)->first();
        $scores = Scores::where('game_id', $game->id)->get();

        $team_1 = TeamUser::where('game_id', $game->id)->where('team_id', $scores[0]->team_id)->get();
        $team_2 = TeamUser::where('game_id', $game->id)->where('team_id', $scores[1]->team_id)->get();

        $player_1 = User::find($team_1[0]->user_id);
        $player_2 = User::find($team_1[1]->user_id);
        $player_3 = User::find($team_2[0]->user_id);
        $player_4 = User::find($team_2[1]->user_id);

        $team_1 = [
            'team_id' => $team_1[0]->team_id,
            'score' => $scores[0]->points,
            'player_1' => [
                'name' => $player_1->name,
                'photo' => $player_1->photo_path,
            ],
            'player_2' => [
                'name' => $player_2->name,
                'photo' => $player_2->photo_path,
            ],
        ];

        $team_2 = [
            'team_id' => $team_2[0]->team_id,
            'score' => $scores[1]->points,
            'player_1' => [
                'name' => $player_3->name,
                'photo' => $player_3->photo_path,
            ],
            'player_2' => [
                'name' => $player_4->name,
                'photo' => $player_4->photo_path,
            ],
        ];

        return response()->json([$team_1, $team_2], 200);
    }

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScoresRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Scores $scores)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scores $scores)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScoresRequest $request, Scores $scores)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scores $scores)
    {
        //
    }
}
