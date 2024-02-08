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
use App\Models\PlayerOrder;
use App\Models\TeamUser;
use App\Events\TurnUpdate;
use App\Events\GameStarted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function startGame(Request $request) {
        $game = Games::where('is_active', true)->first();
        $game->is_game_running = true;
        $game->save();

        broadcast(new GameStarted())->toOthers();

        return response()->json(['game' => $game], 200);
    }

    // find if we have active game
    public function isGameActive()
    {
        $game = Games::where('is_active', true)->first();
        
        // if game exist, return it
        if ($game) {
            $expectedPlayers = $game->number_of_teams * 2;
            $loggedPlayers = User::where('game_id', $game->id)->count();

            if ($expectedPlayers == $loggedPlayers) {
                return response()->json(['game' => $game, 'allPlayersSet' => true], 200);
            } else {
                return response()->json(['game' => $game, 'allPlayersSet' => false], 200);
            }

        } else {
            return response()->json(['game' => null], 200);
        }

    
    }

    public function isGameRunning() {
        $game = Games::where('is_active', true)->first();
        return response()->json(['isGameRunning' => $game->is_game_running], 200);
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
            'current_turn' => 1,
            'is_game_running' => false,
            'random_pick_of_players' => $request->random_pick_of_players,
            'categories' => $request->categories,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
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

    public function endRound() {
    
        $game = Games::where('is_active', true)->first();
        
        if (!$game) {
            // Log and handle the case where no active game is found
            Log::info('No active game found.');
            return;
        }

        $isLastTurn = $game->number_of_teams * 2 == $game->current_turn;

        if ($isLastTurn) {
            // Reset current_turn and save
            $game->current_turn = 1;
            $game->save();
            
            // Log for debugging
            Log::info('Last turn reached, resetting current turn.');
        } else {
            // Increment current_turn and save
            $game->current_turn++;
            $game->save();

            // Log for debugging
            Log::info('Incrementing current turn: ' . $game->current_turn);
        }

        // Broadcast the update
        broadcast(new TurnUpdate())->toOthers();

        return response()->json('turn updated', 200);

    }


    public function getOnTurn() {

        // get whose turn is it
        $game = Games::where('is_active', true)->first();
        $order = PlayerOrder::where('game_id', $game->id)->where('order', $game->current_turn)->first();
        $player = User::where('id', $order->user_id)->first();
        // get teammate
        $team = TeamUser::where('user_id', $player->id)->first();
        $teammate = TeamUser::where('team_id', $team->team_id)->where('user_id', '!=', $player->id)->first();
        $teammate = User::where('id', $teammate->user_id)->first();

        return response()->json([$player, $teammate], 200);

    }

}
