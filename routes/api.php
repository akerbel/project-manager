<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/category', [CategoryController::class, 'post']);
Route::middleware('auth:sanctum')->put('/category/{id}', [CategoryController::class, 'put']);
Route::middleware('auth:sanctum')->delete('/category/{id}', [CategoryController::class, 'delete']);
Route::get('/category/{id}', [CategoryController::class, 'get']);
Route::get('/categories', [CategoryController::class, 'getAll']);

Route::middleware('auth:sanctum')->post('/project', [ProjectController::class, 'post']);
Route::middleware('auth:sanctum')->patch('/project/{id}', [ProjectController::class, 'patch']);
Route::middleware('auth:sanctum')->delete('/project/{id}', [ProjectController::class, 'delete']);
Route::middleware('auth:sanctum')->get('/project/{id}', [ProjectController::class, 'get']);
Route::middleware('auth:sanctum')->get('/projects', [ProjectController::class, 'getAll']);
