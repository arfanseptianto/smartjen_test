<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SchoolController;
use App\Http\Controllers\API\AdminController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->post('/school/get_all', [SchoolController::class, 'get_all']);

Route::middleware('auth:sanctum')->post('/admin/get_all_user', [AdminController::class, 'get_all_users']);
Route::middleware('auth:sanctum')->post('/admin/add_new_user', [AdminController::class, 'add_new_user']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
