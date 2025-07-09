<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['barber.user', 'service'])
            ->where('user_id', Auth::id())
            ->orderBy('booking_time', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Customer/Dashboard', [
            'bookings' => $bookings,
        ]);
    }

    public function bookings()
    {
        $bookings = Booking::with(['barber.user', 'service'])
            ->where('user_id', Auth::id())
            ->orderBy('booking_time', 'desc')
            ->get();

        return Inertia::render('Customer/Bookings', [
            'bookings' => $bookings,
        ]);
    }
}
