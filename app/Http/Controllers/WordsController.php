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

class WordsController extends Controller
{

    // createWord
    public function createWord(Request $request)
    {
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

    // get all words for game
    public function getWordsForGame()
    {
        $game = Games::where('is_active', true)->first();
        $words = Words::where('game_id', $game->id)->get();

        return response()->json(['words' => $words], 200);
    }

    // checkUserWords
    public function checkUserWords()
    {
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

        return response()->json(['message' => 'success'], 200);
    }

    public function startTimer() {
        
        broadcast(new TimerStart())->toOthers();

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
    public function store(StoreWordsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Words $words)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Words $words)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWordsRequest $request, Words $words)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Words $words)
    {
        //
    }
}
