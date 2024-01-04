<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamUserController;
use App\Http\Controllers\WordsController;
use App\Http\Controllers\PlayerOrderController;

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


/*
|--------------------------------------------------------------------------
| GAME
|--------------------------------------------------------------------------
*/
// create game
Route::post('/create-game', [GamesController::class, 'create']);
// get if is game active
Route::get('/is-game-active', [GamesController::class, 'isGameActive']);
// get all games
Route::get('/teams', [TeamsController::class, 'index']);
// End turn
Route::middleware('auth:sanctum')->post('/end-round', [GamesController::class, 'endRound']);
// get who is on turn
Route::middleware('auth:sanctum')->get('/get-current-turn', [GamesController::class, 'getOnTurn']);

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/
// create user
Route::middleware('web')->post('/create-user', [AuthController::class, 'register']);
// login user
Route::middleware('web')->post('/login', [AuthController::class, 'login']);
// save photo for user
Route::middleware('auth:sanctum')->post('/save-photo', [AuthController::class, 'savePhoto']);
// get current user
Route::middleware('auth:sanctum')->get('/get-current-user', [AuthController::class, 'getUser']);
// check if user exist with that name
Route::middleware('web')->post('/check-user', [AuthController::class, 'checkUser']);
// return user which is admin
Route::middleware('web')->get('/get-admin', [AuthController::class, 'getAdmin']);
// get all users
Route::middleware('auth:sanctum')->get('/get-users', [AuthController::class, 'index']);
// get user by team id
Route::middleware('auth:sanctum')->get('/get-user-by-team-id/{teamId}', [AuthController::class, 'getUserByTeamId']);

/*
|--------------------------------------------------------------------------
| TEAM
|--------------------------------------------------------------------------
*/
// join a team
Route::middleware('auth:sanctum')->post('/join-team', [TeamsController::class, 'joinTeam']);
// get all users for each team
Route::middleware('auth:sanctum')->get('/get-users-per-team', [TeamUserController::class, 'getUsersPerTeam']);
// get all users for a team by team id
Route::middleware('auth:sanctum')->get('/get-users-for-team/{teamId}', [TeamUserController::class, 'getUsersForTeam']);
// get all teams by game id
Route::middleware('auth:sanctum')->get('/get-teams-for-game/{gameId}', [TeamsController::class, 'getTeamsForGame']);
// detach user from team
Route::middleware('auth:sanctum')->post('/leave-team', [TeamUserController::class, 'leaveTeam']);
// create team and turns
Route::middleware('auth:sanctum')->post('/mix-teams', [TeamsController::class, 'createTeams']);
// get score for all teams
Route::middleware('auth:sanctum')->get('/get-score', [TeamsController::class, 'getScore']);
// get score for team by team id
Route::middleware('auth:sanctum')->get('/get-score-for-team', [TeamsController::class, 'getScoreForTeam']);
/*
|--------------------------------------------------------------------------
| WORDS
|--------------------------------------------------------------------------
*/
// Create words
Route::middleware('auth:sanctum')->post('/create-word', [WordsController::class, 'createWord']);
// Get words for game
Route::middleware('auth:sanctum')->get('/get-words-for-game', [WordsController::class, 'getWordsForGame']);
// check user how many words he has left
Route::middleware('auth:sanctum')->get('/check-user-words', [WordsController::class, 'checkUserWords']);
// get word
Route::middleware('auth:sanctum')->get('/get-word', [WordsController::class, 'getWord']);
// make a point
Route::middleware('auth:sanctum')->post('/point', [WordsController::class, 'point']);
// start timer 
Route::middleware('auth:sanctum')->post('/start-timer', [WordsController::class, 'startTimer']);