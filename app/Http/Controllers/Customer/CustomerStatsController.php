<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class CustomerStatsController extends Controller
{
    public function monthlySpending()
    {
        try {
            $userId = Auth::id();
            $year = now()->year;
            $months = collect(range(1, 12))->map(function ($m) use ($year) {
                return [
                    'month' => sprintf('%04d-%02d', $year, $m),
                    'total' => 0
                ];
            })->values()->all();

            $spending = Booking::where('user_id', $userId)
                ->where('status', '!=', 'cancelled')
                ->whereYear('booking_date', $year)
                ->selectRaw('DATE_FORMAT(booking_date, "%Y-%m") as month, SUM(service_price) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($spending as $item) {
                foreach ($months as &$month) {
                    if ($month['month'] === $item->month) {
                        $month['total'] = (float) $item->total;
                        break;
                    }
                }
            }

            return response()->json($months);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function barberBookingDistribution()
    {
        try {
            $userId = Auth::id();
            $year = now()->year;
            $barberCounts = Booking::where('user_id', $userId)
                ->where('status', '!=', 'cancelled')
                ->whereYear('booking_date', $year)
                ->with('barber.user')
                ->get()
                ->groupBy(function($booking) {
                    return $booking->barber && $booking->barber->user ? $booking->barber->user->name : 'Unknown';
                })
                ->map(function($group, $name) {
                    return [
                        'name' => $name,
                        'count' => $group->count()
                    ];
                })->values();
            return response()->json($barberCounts);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function favouriteBarber()
    {
        try {
            $userId = Auth::id();
            $year = now()->year;
            $bookingWeight = 1;
            $ratingWeight = 5;

            $barberStats = \App\Models\Transformation::where('user_id', $userId)
                ->whereYear('created_at', $year)
                ->whereNotNull('barber_id')
                ->groupBy('barber_id')
                ->selectRaw('barber_id, COUNT(*) as bookings_count, AVG(rating) as avg_rating')
                ->get()
                ->map(function ($row) use ($bookingWeight, $ratingWeight) {
                    $barber = \App\Models\Barber::find($row->barber_id);
                    $user = $barber ? $barber->user : null;
                    $score = ($row->bookings_count * $bookingWeight) + ($row->avg_rating * $ratingWeight);
                    return [
                        'barber_id' => $row->barber_id,
                        'bookings_count' => $row->bookings_count,
                        'avg_rating' => round($row->avg_rating, 2),
                        'score' => $score,
                        'barber_name' => $user ? $user->name : 'Unknown',
                        'barber_photo_url' => $user ? $user->profile_photo_url : null,
                    ];
                })
                ->sortByDesc('score')
                ->values();

            $favourite = $barberStats->first();

            return response()->json([
                'favourite' => $favourite,
                'all' => $barberStats,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function nextAppointment()
    {
        try {
            $userId = Auth::id();
            $now = now();
            $next = \App\Models\Booking::where('user_id', $userId)
                ->where('status', '!=', 'cancelled')
                ->where(function($q) use ($now) {
                    $q->where('booking_date', '>', $now->toDateString())
                      ->orWhere(function($q2) use ($now) {
                          $q2->where('booking_date', $now->toDateString())
                             ->where('booking_time', '>=', $now->toTimeString());
                      });
                })
                ->with(['barber.user'])
                ->orderBy('booking_date')
                ->orderBy('booking_time')
                ->first();

            if (!$next) {
                return response()->json(['appointment' => null]);
            }

            $barber = $next->barber && $next->barber->user ? $next->barber->user : null;
            return response()->json([
                'appointment' => [
                    'id' => $next->id,
                    'date' => $next->booking_date,
                    'time' => $next->booking_time,
                    'barber_name' => $barber ? $barber->name : 'Unknown',
                    'barber_photo_url' => $barber ? $barber->profile_photo_url : null,
                    'status' => $next->status,
                    'checked_in_at' => $next->checked_in_at,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
