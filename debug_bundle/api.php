<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\BarberRating;
use App\Models\Transformation;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BarberStatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/available-barbers', [BookingController::class, 'getAvailableBarbers']);
    Route::get('/available-slots', [BookingController::class, 'getAvailableSlots']);
    Route::get('/barber/monthly-ratings', [BarberStatsController::class, 'monthlyRatings']);
});


