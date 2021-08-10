<?php

use App\Http\Controllers\AdminController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/user/register', [AdminController::class, 'user_register']);
Route::post('/user/logout', [AdminController::class, 'logout']);
Route::post('/user/update', [AdminController::class, 'user_update']);
Route::get('/user/varify', [AdminController::class, 'user_varify']);
