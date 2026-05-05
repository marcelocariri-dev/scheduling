<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User as ModelsUser;
use App\Http\Controllers\AgendamentoApiController;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\LocalApiController;
use App\Http\Controllers\UserApiController;

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação (Sanctum)
|--------------------------------------------------------------------------
*/

//// rotas públicas
Route::post ('/register', [AuthApiController::class, 'register']);
Route::post ('/login', [AuthApiController::class, 'login']);

Route::get('/ping', fn () => response()->json([
    'message' => 'pong',
    'timestamp' => now()
]));


//// rotas privadas
Route::middleware(['auth:sanctum'])->group(function () {


    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    Route::apiResource('/agendamentos',AgendamentoApiController::class);
    Route::apiResource('/locais', LocalApiController::class);
    Route::apiResource('users', UserApiController::class);



});
