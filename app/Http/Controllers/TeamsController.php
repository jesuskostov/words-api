<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamsRequest;
use App\Http\Requests\UpdateTeamsRequest;
use App\Models\Teams;
use Illuminate\Http\Request;
use App\Events\GameCreated;
use App\Models\TeamUser;
use App\Models\Games;
use App\Models\User;


class TeamsController extends Controller
{

    public function createTeams() {
        $game = Games::where('is_active', true)->first();
        $gameId = $game->id;

        $players = User::where('game_id', $gameId)->get();
        $shuffledPlayers = $players->shuffle();
        // Create teams with randomly paired players
        $teams = collect();
        for ($i = 0; $i < $shuffledPlayers->count(); $i += 2) {
            $team = Teams::create([
                'game_id' => $gameId,
                'name' => 'Team ' . ($i / 2 + 1), // Example team name
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                // Other team properties...
            ]);

            // Associate players with the team and include game_id
            $team->users()->attach($shuffledPlayers[$i]->id, ['game_id' => $gameId]);
            if ($i + 1 < $shuffledPlayers->count()) {
                $team->users()->attach($shuffledPlayers[$i + 1]->id, ['game_id' => $gameId]);
            }

            // Add the team to the collection
            $teams->push($team);
        }

        // Now, set the order of turns for the players in the teams
        $orderedPlayers = collect();
        // First, add the first player from each team
        foreach ($teams as $team) {
            $players = $team->users; // Assuming each team has exactly two players
            $orderedPlayers->push($players[0]); // Add the first player
        }

        // Then, add the second player from each team
        foreach ($teams as $team) {
            $players = $team->users; // Re-confirming each team has exactly two players
            $orderedPlayers->push($players[1]); // Add the second player
        }

        // The $orderedPlayers now contains players in the required order
        // Display the ordered list of players
        foreach ($orderedPlayers as $player) {
            echo $player->name . ' - ';
        }

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Teams::all();
    }

    // getTeamsForGame
    public function getTeamsForGame($gameId)
    {
        return Teams::where('game_id', $gameId)->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($numberOfTeams, $gameId)
    {
        // create teams by filling name, color with a random color
        // for ($i = 0; $i < $numberOfTeams; $i++) {
        //     $team = Teams::create([
        //         'game_id' => $gameId,
        //         'name' => 'Team ' . ($i + 1),
        //         'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),                
        //     ]);
        // }

        // broadcast(new GameCreated())->toOthers();
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