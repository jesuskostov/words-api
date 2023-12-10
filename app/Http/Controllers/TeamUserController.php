<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamUserRequest;
use App\Http\Requests\UpdateTeamUserRequest;
use App\Models\TeamUser;
use Illuminate\Http\Request;

class TeamUserController extends Controller
{

    // get users per team
    public function getUsersPerTeam()
    {
        $users = TeamUser::with('user')->get();
        return response()->json($users);
    }

    // getUsersForTeam
    public function getUsersForTeam($teamId)
    {
        $users = TeamUser::where('team_id', $teamId)->with('user')->get();
        return response()->json($users);
    }

    // leaveTeam
    public function leaveTeam(Request $request)
    {
        $request->validate([
            'teamId' => 'required|integer',
        ]);

        $teamId = $request->teamId;
        $userId = auth()->user()->id;

        $teamUser = TeamUser::where('team_id', $teamId)->where('user_id', $userId)->first();
        if (!$teamUser) {
            return response()->json(['message' => 'User not found in this team'], 404);
        }

        $teamUser->delete();

        return response()->json(['message' => 'Successfully left team'], 200);
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
    public function store(StoreTeamUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamUser $teamUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeamUser $teamUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeamUserRequest $request, TeamUser $teamUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamUser $teamUser)
    {
        //
    }
}
