<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// create game
Route::post('/create-game', [GamesController::class, 'create']);
// get all games
Route::get('/teams', [TeamsController::class, 'index']);
// create user
Route::middleware('web')->post('/create-user', [AuthController::class, 'register']);
// get current user
Route::middleware('auth:sanctum')->get('/get-current-user', [AuthController::class, 'getUser']);
// join a team
Route::middleware('auth:sanctum')->post('/join-team', [TeamsController::class, 'joinTeam']);
// get all users for each team
Route::middleware('auth:sanctum')->get('/get-users-per-team', [TeamUserController::class, 'getUsersPerTeam']);
// detach user from team
Route::middleware('auth:sanctum')->post('/leave-team', [TeamUserController::class, 'leaveTeam']);
// get all users for a team by team id
Route::middleware('auth:sanctum')->get('/get-users-for-team/{teamId}', [TeamUserController::class, 'getUsersForTeam']);