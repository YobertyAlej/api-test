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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::get('users/byEmail/{email}', [UserController::class, 'showByEmail'])->name('users.byEmail');
    Route::post('users/{user_id}/grantRole', [UserController::class, 'grantRole'])->name('users.grantRole');
    Route::post('users/{user_id}/revokeRole', [UserController::class, 'revokeRole'])->name('users.revokeRole');

    Route::resource('roles', RoleController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::get('roles/byName/{name}', [RoleController::class, 'showByName'])->name('users.byName');
    Route::post('roles/{role}/grantPermission', [RoleController::class, 'grantPermission'])->name('roles.grantPermission');
    Route::post('roles/{role}/revokePermission', [RoleController::class, 'revokePermission'])->name('roles.revokePermission');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::fallback(function(){
    return response()->json([
        'error' => 'Route not found'
    ], 404);
});
