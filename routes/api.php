<?php

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\LikeController;
use App\Http\Controllers\API\V1\LoginController;
use App\Http\Controllers\API\V1\PhotoController;

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

Route::middleware('auth:sanctum')->group(function () {
    // route for logout
    Route::post('logout', [LoginController::class, 'logout']);
    // route for fetch-profile
    Route::get('user/profile', [LoginController::class, 'profile']);
});

Route::controller(PhotoController::class)->group(function () {
    // route for get photo, not protected middleware sanctum
    Route::get('/photos', 'index');
    
    // route for create photo, protected middleware sanctum
    Route::post('/photos', 'store')->middleware('auth:sanctum');
    
    // route for get photo, not protected middleware sanctum
    Route::get('/photos/{id}', 'show');
    
    // route for update photo, protected middleware sanctum
    Route::put('/photos/{id}', 'update')->middleware('auth:sanctum');
    
    // route for delete photo, protected middleware sanctum
    Route::delete('/photos/{id}', 'destroy')->middleware('auth:sanctum');

    // route for like photo, protected middleware sanctum
    Route::put('/photos/{id}/like', 'like')->middleware('auth:sanctum');
        
    // route for unlike photo, protected middleware sanctum
    Route::put('/photos/{id}/unlike', 'unlike')->middleware('auth:sanctum');    
});

// route for login
Route::post('login', [LoginController::class, 'login']);
