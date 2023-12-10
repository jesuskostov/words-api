<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWordsRequest;
use App\Http\Requests\UpdateWordsRequest;
use App\Models\Words;

class WordsController extends Controller
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
