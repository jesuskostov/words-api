<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
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
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:30',
        ]);

        $user = User::firstOrCreate(['name' => $validatedData['name']]);

        // Store user information in Laravel session
        session(['user_id' => $user->id, 'user_name' => $user->name]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user]);

    }

    public function getCurrentUser()
    {

        // if (session('user_id')) {
        //     $user = User::find(session('user_id'));
        //     return response()->json(['user' => $user]);
        // }

        return response()->json(['message' => 'Hellooo'], 200);
    }
}
