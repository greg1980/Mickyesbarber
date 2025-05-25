<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\BarberAvailabilitySchedule;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BarberDashboardController extends Controller
{
    /**
     * Display the barber dashboard.
     */
    public function index()
    {
        $barber = Auth::user()->barber;
        $schedules = $barber->availabilitySchedules()
            ->orderBy('day_of_week')
            ->get();
        $bookings = Booking::with(['user'])
            ->where('barber_id', $barber->id)
            ->orderBy('booking_time', 'asc')
            ->take(5)
            ->get();

        return Inertia::render('Barber/Dashboard', [
            'schedules' => $schedules,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Toggle the barber's availability status.
     */
    public function toggleAvailability(Request $request)
    {
        $barber = Auth::user()->barber;

        $barber->availability = !$barber->availability;
        $barber->save();

        return back()->with('success', 'Availability updated successfully.');
    }

    /**
     * Update the barber's availability schedule.
     */
    public function updateSchedule(Request $request)
    {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.is_available' => 'required|boolean',
        ]);

        $barber = Auth::user()->barber;

        // Delete existing schedules
        $barber->availabilitySchedules()->delete();

        // Create new schedules
        foreach ($request->schedules as $schedule) {
            $barber->availabilitySchedules()->create($schedule);
        }

        return back()->with('success', 'Availability schedule updated successfully.');
    }

    /**
     * Show today's appointments for the barber.
     */
    public function appointments()
    {
        $barber = Auth::user()->barber;
        $today = now()->toDateString();

        $appointments = $barber->bookings()
            ->with(['user', 'service'])
            ->whereDate('booking_time', $today)
            ->orderBy('booking_time')
            ->get();

        return Inertia::render('Barber/Appointments', [
            'appointments' => $appointments,
        ]);
    }
}
