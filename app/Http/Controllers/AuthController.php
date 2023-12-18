<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Storage;
use App\Jobs\BroadcastUserCreated;
use App\Models\Games;

class AuthController extends Controller
{

    public function index()
    {
        $game = Games::where('is_active', true)->first();
        $users = User::where('game_id', $game->id)->get();
        return response()->json(['users' => $users], 200);
    }

    public function register(Request $request)
    {
        $game = Games::where('is_active', true)->first();

        // if user exists then login if not create user
        $user = User::where('name', $request->name)->where('game_id', $game->id)->first();

        if ($user) {
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        // create user
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'game_id' => $game->id,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'user'=> $user], 201);
    }

    public function getUser()
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

    public function getAdmin()
    {
        $game = Games::where('is_active', true)->first();

        $user = User::where('game_id', $game->id)->where('is_admin', true)->first();

        return response()->json(['user' => $user], 200);
    }

    public function checkUser(Request $request)
    {
        $game = Games::where('is_active', true)->first();

        $user = User::where('name', $request->name)->where('game_id', $game->id)->first();

        if ($user) {
            return response()->json(['user' => $user], 200);
        } else {
            return response()->json(['user' => null], 200);
        }
    }

    public function login(Request $request) {
        $user = User::where('name', $request->name)->first();
        if ($user) {
            $token = $user->createToken('authToken')->plainTextToken;
            BroadcastUserCreated::dispatch($user);

            return response()->json(['token' => $token, 'user' => $user], 200);

        } else {
            return response()->json(['user' => null], 200);
        }
    }

    public function savePhoto(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'photo' => 'required|image',
        ]);

        $user = User::find($request->user_id);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo_path = Storage::url($path);
            $user->save();

            broadcast(new UserCreated())->toOthers();
            return response()->json(['message' => 'Photo uploaded successfully.', 'photo_path' => $user->photo_path], 200);
        }

        return response()->json(['message' => 'Failed to upload photo.'], 500);
    }
}
