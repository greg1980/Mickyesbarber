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
            ->where(function ($query) {
                $query->where('booking_date', '>', now()->toDateString())
                      ->orWhere(function ($q) {
                          $q->where('booking_date', now()->toDateString())
                            ->where('booking_time', '>=', now()->toTimeString());
                      });
            })
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->take(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service_name' => $booking->service->name ?? null,
                    'booking_time' => $booking->booking_date->format('Y-m-d') . 'T' . (is_object($booking->booking_time) ? $booking->booking_time->format('H:i:s') : $booking->booking_time),
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'payment_method' => $booking->stripe_payment_id ? 'Card' : 'N/A',
                    'notes' => $booking->notes,
                    'user' => [
                        'name' => $booking->user->name ?? '',
                        'profile_photo_url' => $booking->user->profile_photo_url ?? '',
                    ],
                ];
            });

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
    public function appointments(Request $request)
    {
        $barber = Auth::user()->barber;
        $date = $request->query('date', now()->toDateString());
        $search = $request->query('search');

        $query = $barber->bookings()
            ->with(['user', 'service'])
            ->whereDate('booking_date', $date);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $appointments = $query
            ->orderBy('booking_time')
            ->paginate(10)
            ->through(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'service_name' => $appointment->service->name ?? null,
                    'booking_time' => $appointment->booking_date->format('Y-m-d') . 'T' . (is_object($appointment->booking_time) ? $appointment->booking_time->format('H:i:s') : $appointment->booking_time),
                    'status' => $appointment->status,
                    'user' => [
                        'name' => $appointment->user->name ?? '',
                        'profile_photo_url' => $appointment->user->profile_photo_url ?? '',
                    ],
                ];
            });

        return Inertia::render('Barber/Appointments', [
            'appointments' => $appointments,
            'selectedDate' => $date,
            'search' => $search,
        ]);
    }

    public function updateAppointmentStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:completed,cancelled,rescheduled'
        ]);

        // Ensure the booking belongs to the authenticated barber
        if ($booking->barber_id !== Auth::user()->barber->id) {
            abort(403, 'Unauthorized action.');
        }

        $booking->update([
            'status' => $request->status
        ]);

        // If marking as completed, create a transformation record
        if ($request->status === 'completed') {
            $booking->transformation()->create([
                'user_id' => $booking->user_id,
                'barber_id' => $booking->barber_id,
                'service_id' => $booking->service_id,
                'before_image' => null,
                'after_image' => null,
                'rating' => null,
                'review' => null,
                'style' => '',
            ]);
        }

        return response()->json([
            'message' => 'Appointment status updated successfully',
            'booking' => $booking->fresh(['user', 'service'])
        ]);
    }
}
