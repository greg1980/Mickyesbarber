<?php

namespace App\Http\Controllers;

use App\Models\Transformation;
use Illuminate\Http\Request;

class BarberStatsController extends Controller
{
    /**
     * Get monthly ratings for the authenticated barber
     */
    public function monthlyRatings()
    {
        \Log::info('Sanctum Debug', [
            'user' => auth()->user(),
            'session' => session()->all(),
            'cookies' => request()->cookies->all(),
        ]);
        try {
            $user = auth()->user();
            $barber = $user->barber ?? null;

            if (!$barber) {
                return response()->json([
                    'error' => 'Barber profile not found'
                ], 404);
            }

            $raw = Transformation::where('barber_id', $barber->id)
                ->whereNotNull('rating')
                ->selectRaw('MONTH(created_at) as month, AVG(rating) as avg_rating')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ];

            $result = [];
            foreach ($months as $num => $name) {
                $monthData = $raw->firstWhere('month', $num);
                $result[] = [
                    'month' => $name,
                    'rating' => $monthData ? round($monthData->avg_rating, 2) : 0
                ];
            }

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error in monthlyRatings: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching ratings'
            ], 500);
        }
    }

    public function todaysAppointmentsCount()
    {
        $user = auth()->user();
        $barber = $user->barber ?? null;

        if (!$barber) {
            return response()->json(['count' => 0]);
        }

        $today = now()->toDateString();

        $count = \App\Models\Booking::where('barber_id', $barber->id)
            ->where('booking_date', $today)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return response()->json(['count' => $count]);
    }

    public function todaysCompletedAppointmentsCount()
    {
        $user = auth()->user();
        $barber = $user->barber ?? null;

        if (!$barber) {
            return response()->json(['count' => 0]);
        }

        $today = now()->toDateString();

        $count = \App\Models\Booking::where('barber_id', $barber->id)
            ->where('booking_date', $today)
            ->where('status', 'completed')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function monthlyCompletedAppointmentsCount()
    {
        $user = auth()->user();
        $barber = $user->barber ?? null;

        if (!$barber) {
            return response()->json(['count' => 0]);
        }

        $now = now();
        $count = \App\Models\Booking::where('barber_id', $barber->id)
            ->whereMonth('booking_date', $now->month)
            ->whereYear('booking_date', $now->year)
            ->where('status', 'completed')
            ->count();

        return response()->json(['count' => $count]);
    }
}
