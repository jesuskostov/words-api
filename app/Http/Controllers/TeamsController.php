<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamsRequest;
use App\Http\Requests\UpdateTeamsRequest;
use App\Models\Teams;
use Illuminate\Http\Request;
use App\Events\GameCreated;
use App\Models\TeamUser;

class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Teams::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($numberOfTeams, $gameId)
    {
        // create teams by filling name, color with a random color
        for ($i = 0; $i < $numberOfTeams; $i++) {
            $team = Teams::create([
                'game_id' => $gameId,
                'name' => 'Team ' . ($i + 1),
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),                
            ]);
        }

        broadcast(new GameCreated())->toOthers();
    }

    public function joinTeam(Request $request)
    {
        $request->validate([
            'teamId' => 'required|integer',
        ]);

        $team = Teams::find($request->teamId);
        if (!$team) {
            return response()->json(['message' => 'Team not found'], 404);
        }

        $userId = auth()->user()->id;
        $gameId = $team->game_id;

        if (TeamUser::where('user_id', $userId)->where('game_id', $gameId)->exists()) {
            return response()->json(['message' => 'User already in a team for this game'], 409);
        }

        $team->users()->attach($userId, ['game_id' => $gameId]);

        return response()->json(['message' => 'Successfully joined team'], 200);
    }

   
}