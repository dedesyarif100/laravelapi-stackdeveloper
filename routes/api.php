<?php

use App\Http\Controllers\APIController;
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

Route::namespace('App\Http\Controllers')->group(function() {
    Route::get('users', [APIController::class, 'getUser']);
    Route::get('users/{id?}', [APIController::class, 'getUserId']);
    Route::post('add-users', [APIController::class, 'addUsers']);
    Route::post('add-multiple-users', [APIController::class, 'addMultipleUsers']);
    Route::put('update-user-details/{id}', [APIController::class, 'updateUserDetails']);
    Route::patch('update-user-name/{id}', [APIController::class, 'updateUsername']);
    Route::delete('delete-user/{id}', [APIController::class, 'deleteUser']);
    Route::delete('delete-user-withjson', [APIController::class, 'deleteUserWithJson']);
    Route::delete('delete-multiple-users/{ids}', [APIController::class, 'deleteMultipleUsers']);
    Route::delete('delete-multiple-users-withjson', [APIController::class, 'deleteMultipleUsersWithJson']);
});
