<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWordsRequest;
use App\Http\Requests\UpdateWordsRequest;
use App\Models\Words;
use App\Models\Games;
use App\Models\Scores;
use App\Models\TeamUser;
use Illuminate\Http\Request;
use App\Events\WordCreated;
use App\Events\TimerStart;
use App\Events\TimerPause;
use App\Events\TimerResume;
use App\Services\TimerService;
use App\Events\NextRound;

class WordsController extends Controller
{

    public function createWord(Request $request) {
        // get active game
        $game = Games::where('is_active', true)->first();
        $user = auth()->user();

        $request->validate([
            'word' => 'required|string|max:255'
        ]);

        // return error if word already exists
        if (Words::where('word', $request->word)->where('game_id', $game->id)->exists()) {
            return response()->json(['error' => 'Word already exists'], 400);
        }

        $word = Words::create([
            'game_id' => $game->id,
            'word' => $request->word,
            'created_by' => $user->id,
        ]);
        
        broadcast(new WordCreated())->toOthers();

        return response()->json(['word' => $word], 201);
    }

    public function getWordsForGame() {
        $game = Games::where('is_active', true)->first();
        $words = Words::where('game_id', $game->id)->get();

        return response()->json(['words' => $words], 200);
    }

    public function getExpectedWordsForGame() {
        $game = Games::where('is_active', true)->first();

        if (!$game) {
            return response()->json(['message' => 'No active game'], 200);
        }

        $wordsCount = $game->number_of_words * $game->number_of_teams * 2;

        return response()->json(['words_to_insert' => $wordsCount], 200);
    }

    public function checkUserWords() {
        $game = Games::where('is_active', true)->first();
        $user = auth()->user();

        // get how many words user has to insert for game_id
        $wordsCount = $game->number_of_words;

        // get words for by created_by and game_id
        $words = Words::where('created_by', $user->id)->where('game_id', $game->id)->get();

        // return how many words user has left
        return response()->json(['words_to_insert' => $wordsCount - $words->count()], 200);
    }

    public function getWord() {
        $game = Games::where('is_active', true)->first();

        if (!Words::where('game_id', $game->id)->where('guessed', false)->exists()) {
            return response()->json(['message' => 'No more words'], 200);
        }
        
        $word = Words::where('game_id', $game->id)->where('guessed', false)->inRandomOrder()->first();

        return response()->json([$word->word], 200);
    }

    public function point(Request $request) {
        $game = Games::where('is_active', true)->first();
        $word = Words::where('game_id', $game->id)->where('word', $request->word)->first();

        $word->guessed = true;
        $word->save();

        // get team id for user and set score
        $user = auth()->user();
        $teamId = TeamUser::where('user_id', $user->id)->where('game_id', $game->id)->first()->team_id;

        // create score if not exists
        if (!Scores::where('team_id', $teamId)->exists()) {
            $score = Scores::create([
                'game_id' => $game->id,
                'team_id' => $teamId,
                'points' => 1,
            ]);
        } else {
            $score = Scores::where('team_id', $teamId)->first();
            $score->points = $score->points + 1;
            $score->save();
        }

        // check if not guessed words left
        if (!Words::where('game_id', $game->id)->where('guessed', false)->exists()) {
            $game->rounds++;
            $game->timer_start = null;
            $game->timer_elapsed = null;
            $game->timer_state = 'stopped';
            $isLastTurn = $game->number_of_teams * 2 == $game->current_turn;
            if ($isLastTurn) {
                $game->current_turn = 1;
            } else {
                $game->current_turn++;
            }
            $game->save();
            Words::where('game_id', $game->id)->update(['guessed' => false]);
            broadcast(new NextRound())->toOthers();

            return response()->json(['message' => 'next_round', 'round' => $game->rounds], 200);
        }

        return response()->json(['message' => 'success'], 200);
    }    

    public function startTimer() {
        $game = Games::where('is_active', true)->first();
    
        if ($game) {
            $game->timer_start = now(); // Set the start time to now
            $game->timer_state = 'running'; // Update the timer state
            $game->save();

            broadcast(new TimerStart($game->round_time))->toOthers();
        }

        return response()->json(['message' => 'Timer started'], 200);
    }

    public function pauseTimer() {
        $game = Games::where('is_active', true)->first();

        if ($game && $game->timer_state === 'running') {
            $elapsed = now()->diffInSeconds($game->timer_start);
            $game->timer_elapsed = $elapsed;
            $game->timer_state = 'paused';
            $game->save();

            broadcast(new TimerPause($elapsed))->toOthers();
        }

        return response()->json(['message' => 'Timer paused'], 200);
    }

    public function resumeTimer() {
        $game = Games::where('is_active', true)->first();

        if ($game && $game->timer_state === 'paused') {
            // Calculate remaining time
            $remainingTime = $game->round_time - $game->timer_elapsed;

            // Set new start time based on the remaining time
            $game->timer_start = now()->subSeconds($game->round_time - $remainingTime);
            $game->timer_state = 'running';
            $game->save();

            broadcast(new TimerResume($remainingTime))->toOthers();
        }

        return response()->json(['message' => 'Timer resumed'], 200);
    }

    public function stopTimer() {
        $game = Games::where('is_active', true)->first();

        if ($game) {
            $game->timer_start = null;
            $game->timer_elapsed = null;
            $game->timer_state = 'stopped';
            $game->save();
        }

        return response()->json(['message' => 'Timer stopped'], 200);
    }

    public function getCurrentTime() {
        $game = Games::where('is_active', true)->first();
        
        if ($game) {
            $timerService = new TimerService($game);
            $currentTime = $timerService->getRemainingTime();
        } else {
            $currentTime = 0;
        }

        return response()->json(['currentTime' => $currentTime], 200);
    }

    public function getCurrentTimerState() {
        $game = Games::where('is_active', true)->first();

        if ($game) {
            $remainingTime = 0;
            if ($game->timer_state === 'running') {
                $remainingTime = max(0, $game->round_time - now()->diffInSeconds($game->timer_start));
            } elseif ($game->timer_state === 'paused') {
                $remainingTime = max(0, $game->round_time - $game->timer_elapsed);
            }

            return response()->json([
                'remainingTime' => $remainingTime,
                'timerState' => $game->timer_state,
                'totalDuration' => $game->round_time
            ], 200);
        }

        return response()->json(['message' => 'No active game found'], 404);
    }

    public function skipWord(Request $request) {
        $game = Games::where('is_active', true)->first();
        $received_word = $request->word;

        if (!Words::where('game_id', $game->id)->where('guessed', false)->exists()) {
            return response()->json(['message' => 'No more words'], 200);
        }
        
        $word = Words::where('game_id', $game->id)->where('guessed', false)->where('word', '!=', $received_word)->inRandomOrder()->first();

        return response()->json([$word->word], 200);
    }
}
