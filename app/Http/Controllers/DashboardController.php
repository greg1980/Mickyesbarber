<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Get upcoming appointments
        $upcomingAppointments = Booking::where('user_id', $user->id)
            ->where('booking_date', '>=', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Get last visit (most recent completed booking)
        $lastVisit = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest('booking_date')
            ->first();

        // Get booking statistics
        $bookings = Booking::where('user_id', $user->id)->get();

        // Log the raw bookings data
        \Log::info('Raw bookings data:', $bookings->toArray());

        $bookingStats = [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'deposit_paid' => $bookings->where('payment_status', 'deposit_paid')->count(),
            'fully_paid' => $bookings->where('payment_status', 'fully_paid')->count(),
        ];

        // Log the calculated stats
        \Log::info('Calculated booking stats:', $bookingStats);

        // Calculate loyalty points (1 point per completed booking)
        $loyaltyPoints = $bookings->where('status', 'completed')->count();

        return response()->json([
            'upcomingAppointments' => $upcomingAppointments,
            'lastVisit' => $lastVisit ? $lastVisit->booking_date->format('M d, Y') : null,
            'loyaltyPoints' => $loyaltyPoints,
            'bookingStats' => $bookingStats,
        ]);
    }
}
