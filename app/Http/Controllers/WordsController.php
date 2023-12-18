<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWordsRequest;
use App\Http\Requests\UpdateWordsRequest;
use App\Models\Words;
use App\Models\Games;
use Illuminate\Http\Request;
use App\Events\WordCreated;

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
