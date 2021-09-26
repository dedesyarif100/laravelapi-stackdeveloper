<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\API_ProductController;
use App\Http\Controllers\API_CustomerController;
use App\Http\Controllers\API_TransactionController;
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

Route::namespace('App\Http\Controllers')->group(function() {
    Route::get('products', [API_ProductController::class, 'getProduct']);
    Route::get('products/{id}', [API_ProductController::class, 'getProductId']);
    Route::post('add-products', [API_ProductController::class, 'addProduct']);
    Route::put('update-products/{id}', [API_ProductController::class, 'updateProduct']);
    Route::delete('delete-products/{id}', [API_ProductController::class, 'deleteProduct']);
});

Route::namespace('App\Http\Controllers')->group(function() {
    Route::get('customers', [API_CustomerController::class, 'getCustomer']);
    Route::get('customers/{id}', [API_CustomerController::class, 'getCustomerId']);
    Route::post('add-customers', [API_CustomerController::class, 'addCustomer']);
    Route::put('update-customers/{id}', [API_CustomerController::class, 'updateCustomer']);
    Route::delete('delete-customers/{id}', [API_CustomerController::class, 'deleteCustomer']);
});

Route::namespace('App\Http\Controllers')->group(function() {
    Route::get('transactions', [API_TransactionController::class, 'getTransaction']);
    Route::get('transactions/{id}', [API_TransactionController::class, 'getTransactionId']);
    Route::post('add-transactions', [API_TransactionController::class, 'addTransaction']);
    Route::put('update-transactions/{id}', [API_TransactionController::class, 'updateTransaction']);
    Route::delete('delete-transactions/{id}', [API_TransactionController::class, 'deleteTransaction']);
});
