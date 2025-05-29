<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\BarberRating;
use App\Models\Transformation;
use App\Http\Controllers\BookingController;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::get('/barber/monthly-ratings', function () {
        $user = auth()->user();
        $barber = $user->barber ?? null;
        if (!$barber) {
            return response()->json([]);
        }
        $raw = Transformation::where('barber_id', $barber->id)
            ->whereNotNull('rating')
            ->selectRaw('MONTH(created_at) as month, AVG(rating) as avg_rating')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('avg_rating', 'month')
            ->toArray();
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        $result = [];
        foreach ($months as $num => $name) {
            $result[] = [
                'month' => $name,
                'rating' => isset($raw[$num]) ? round($raw[$num], 2) : null
            ];
        }
        return response()->json($result);
    });

    Route::get('/debug-user', function () {
        $user = auth()->user();
        return [
            'user' => $user,
            'barber' => $user?->barber,
        ];
    });
});

Route::get('/available-barbers', [App\Http\Controllers\BookingController::class, 'getAvailableBarbers']);
Route::get('/available-slots', [BookingController::class, 'getAvailableSlots']);