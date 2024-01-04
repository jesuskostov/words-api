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
use App\Models\PlayerOrder;
use App\Models\Scores;

class TeamsController extends Controller
{

    public function createTeams() {
        $game = Games::where('is_active', true)->first();
        $gameId = $game->id;
        $expectedTeam = $game->number_of_teams;

        // if teams are already created, return them
        $teams = Teams::where('game_id', $gameId)->get();
        if ($teams->count() == $expectedTeam) {
            // return order of players and teams
            $players = User::where('game_id', $gameId)->get();
            $orderedPlayers = PlayerOrder::where('game_id', $gameId)->get();
            return response()->json(['teams' => $teams, 'order' => $orderedPlayers], 200);
        }

        $players = User::where('game_id', $gameId)->get();
        $shuffledPlayers = $players->shuffle();
        // Create teams with randomly paired players
        $teams = collect();
        for ($i = 0; $i < $shuffledPlayers->count(); $i += 2) {
            $team = Teams::create([
                'game_id' => $gameId,
                'name' => 'Team ' . ($i / 2 + 1), // Example team name
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
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
        // Assuming each team has two players, alternate between the first and second player of each team
        $maxPlayersInTeam = 2;
        for ($j = 0; $j < $maxPlayersInTeam; $j++) {
            foreach ($teams as $team) {
                $players = $team->users; // Get the players of the team
                if ($j < $players->count()) {
                    // Only add the player if they exist in the team (to handle teams with an odd number of players)
                    $orderedPlayers->push($players[$j]);
                }
            }
        }

        // Create PlayerOrder for each player
        foreach ($orderedPlayers as $index => $player) {
            PlayerOrder::create([
                'game_id' => $gameId,
                'user_id' => $player->id,
                'order' => $index + 1, // Order starts from 1
            ]);
        }


        return response()->json(['teams' => $teams, 'order' => $orderedPlayers], 201);

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

    public function getScores() {
        $game = Games::where('is_active', true)->first();
        $scores = Scores::where('game_id', $game->id)->get();
        return response()->json(['scores' => $scores], 200);
    }

    public function getScoreForTeam() {
        $user = auth()->user();
        $teamId = TeamUser::where('user_id', $user->id)->first()->team_id;
        $game = Games::where('is_active', true)->first();
        $scores = Scores::where('game_id', $game->id)->where('team_id', $teamId)->first();

        return response()->json(['scores' => $scores->points], 200);
        
    }
   
}