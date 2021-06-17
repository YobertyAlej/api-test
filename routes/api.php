<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
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

Route::post('login', [AuthController::class, 'login']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::get('users/byEmail/{email}', [UserController::class, 'showByEmail']);
    Route::post('users/{user_id}/grantRole', [UserController::class, 'grantRole']);
    Route::post('users/{user_id}/revokeRole', [UserController::class, 'revokeRole']);

    Route::resource('roles', RoleController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::get('roles/byName/{name}', [RoleController::class, 'showByName']);
    Route::post('roles/{role}/grantPermission', [RoleController::class, 'grantPermission']);
    Route::post('roles/{role}/revokePermission', [RoleController::class, 'revokePermission']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::fallback(function(){
    return response()->json([
        'error' => 'Route not found'
    ], 404);
});
