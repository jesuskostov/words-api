<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
        ]);

        $user = User::create([
            'name' => $request->name,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    public function getUser()
    {
        // get user based on token
        $user = auth()->user();

        return response()->json(['user' => $user], 200);
    }
}
