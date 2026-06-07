<?php

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

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TripController;

Route::post('/v1/auth/register', [AuthController::class, 'register']);
Route::post('/v1/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', function (Request $request) {
        return ['data' => $request->user()];
    });

    Route::apiResource('trips', TripController::class);
    Route::apiResource('trips.activities', App\Http\Controllers\Api\ActivityController::class);

    // Journey Tracking Routes
    Route::post('/trips/{trip}/journey', [App\Http\Controllers\Api\JourneyController::class, 'start']);
    Route::get('/journeys/{journey}', [App\Http\Controllers\Api\JourneyController::class, 'show']);
    Route::patch('/journeys/{journey}/activities/{activity}', [App\Http\Controllers\Api\JourneyController::class, 'updateActivityStatus']);

    // Import Routes
    Route::get('/trips/import/template', [App\Http\Controllers\Api\TripImportController::class, 'downloadTemplate']);
    Route::post('/trips/{trip}/import', [App\Http\Controllers\Api\TripImportController::class, 'import']);

    // History Routes
    Route::get('/history', [App\Http\Controllers\Api\HistoryController::class, 'index']);
    Route::get('/history/{id}', [App\Http\Controllers\Api\HistoryController::class, 'show']);
    Route::delete('/history/{id}', [App\Http\Controllers\Api\HistoryController::class, 'destroy']);
});
