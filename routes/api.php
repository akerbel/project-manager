<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SituationController;
use App\Http\Controllers\UserController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/email/verify', [AuthController::class, 'emailVerificationNotice'])->middleware('auth:sanctum')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'emailVerifySend'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::post('/category', [CategoryController::class, 'post']);
    Route::put('/category/{id}', [CategoryController::class, 'put']);
    Route::delete('/category/{id}', [CategoryController::class, 'delete']);
    Route::get('/category/{id}', [CategoryController::class, 'get']);
    Route::get('/categories', [CategoryController::class, 'getAll']);

    Route::post('/project', [ProjectController::class, 'post']);
    Route::patch('/project/{id}', [ProjectController::class, 'patch']);
    Route::delete('/project/{id}', [ProjectController::class, 'delete']);
    Route::get('/project/{id}', [ProjectController::class, 'get']);
    Route::get('/projects', [ProjectController::class, 'getAll']);

    Route::post('/situation', [SituationController::class, 'post']);
    Route::patch('/situation/{id}', [SituationController::class, 'patch']);
    Route::delete('/situation/{id}', [SituationController::class, 'delete']);
    Route::get('/situation/{id}', [SituationController::class, 'get']);
    Route::get('/situations/{project_id}', [SituationController::class, 'getAll']);

    Route::post('/user', [UserController::class, 'post']);
    Route::patch('/user/{id?}', [UserController::class, 'patch']);
    Route::delete('/user/{id}', [UserController::class, 'delete']);
    Route::get('/user/{id?}', [UserController::class, 'get']);
    Route::get('/users', [UserController::class, 'getAll']);
});

