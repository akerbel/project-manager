<?php

use App\Http\Controllers\CategoryController;
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

Route::post('/category', [CategoryController::class, 'post']);
Route::put('/category/{id}', [CategoryController::class, 'put']);
Route::delete('/category/{id}', [CategoryController::class, 'delete']);
Route::get('/category/{id}', [CategoryController::class, 'get']);
Route::get('/categories', [CategoryController::class, 'getAll']);
