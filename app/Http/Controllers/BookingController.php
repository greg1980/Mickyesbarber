<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\BarberAvailabilitySchedule;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingCreated;
use Illuminate\Support\Facades\Mail;
use App\Mail\RescheduleConfirmation;
use App\Mail\CancellationConfirmation;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\User;

class BookingController extends Controller
{
    // GET /api/available-barbers?date=YYYY-MM-DD&time=HH:MM
    public function getAvailableBarbers(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $date = $request->input('date');
        $time = $request->input('time');
        $dayOfWeek = strtolower(date('l', strtotime($date))); // e.g., 'monday'

        // Get all barbers who are marked as available
        $barbers = Barber::where('availability', true)->with('user')->get();

        // Get bookings for the requested date and time that are not cancelled
        $bookedBarberIds = Booking::where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('barber_id');

        // Filter barbers by detailed schedule
        $availableBarbers = $barbers->filter(function ($barber) use ($dayOfWeek, $time) {
            return $barber->availabilitySchedules()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->where('start_time', '<=', $time)
                ->where('end_time', '>', $time)
                ->exists();
        })->whereNotIn('id', $bookedBarberIds)->values();

        return response()->json([
            'barbers' => $availableBarbers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'barber_id' => 'required|exists:barbers,id',
            'notes' => 'nullable|string|max:500',
            'user_id' => 'nullable|exists:users,id',
            'skip_payment' => 'nullable|boolean',
        ]);

        // Double-booking prevention
        $conflict = Booking::where('barber_id', $validated['barber_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('booking_time', $validated['booking_time'])
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'error' => 'This barber is already booked for the selected date and time.'
            ], 409);
        }

        // Get service from database
        $service = \App\Models\Service::where('slug', $validated['service'])
                                     ->where('is_active', true)
                                     ->first();

        if (!$service) {
            return response()->json([
                'error' => 'Selected service is not available.'
            ], 422);
        }

        $service_price = $service->price;
        $deposit_amount = $validated['skip_payment'] ? 0 : round($service_price * 0.25, 2);
        $balance_amount = $service_price - $deposit_amount;

        // Determine the user_id based on the request
        $user_id = $validated['user_id'] ?? $request->user()->id;

        $booking = Booking::create([
            'user_id' => $user_id,
            'barber_id' => $validated['barber_id'],
            'service_id' => $service->id,
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'service_price' => $service_price,
            'deposit_amount' => $deposit_amount,
            'balance_amount' => $balance_amount,
            'status' => $validated['skip_payment'] ? 'confirmed' : 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Load related barber and user for frontend display
        $booking = Booking::with('barber.user', 'user')->find($booking->id);

        // Send booking confirmation email
        if ($booking->user && $booking->user->email) {
            Mail::to($booking->user->email)->send(new BookingCreated($booking));
        }

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking,
            'skip_payment' => $validated['skip_payment'] ?? false,
        ]);
    }

    public function cancelBooking(\App\Models\Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        // Send cancellation confirmation email
        $booking = Booking::with('barber.user', 'user')->find($booking->id);
        if ($booking->user && $booking->user->email) {
            Mail::to($booking->user->email)->send(new CancellationConfirmation($booking));
        }

        return response()->json(['success' => true]);
    }

    public function reschedule(Request $request, \App\Models\Booking $booking)
    {
        \Log::info('Reschedule request data', $request->all());

        try {
            $validated = $request->validate([
                'booking_date' => 'required|date|after_or_equal:today',
                'booking_time' => 'required|date_format:H:i',
                'barber_id' => 'required|exists:barbers,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', $e->errors());
            throw $e;
        }

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if the booking is in the past
        $bookingDate = substr($booking->booking_date, 0, 10); // YYYY-MM-DD
        $bookingTime = strlen($booking->booking_time) > 8 ? substr($booking->booking_time, 11, 5) : substr($booking->booking_time, 0, 5); // HH:MM
        $bookingDateTime = new \DateTime($bookingDate . ' ' . $bookingTime);
        if ($bookingDateTime < new \DateTime()) {
            return response()->json([
                'error' => 'Cannot reschedule past appointments.'
            ], 400);
        }

        // Double-booking prevention for the selected barber
        $conflict = \App\Models\Booking::where('barber_id', $validated['barber_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('booking_time', $validated['booking_time'])
            ->where('id', '!=', $booking->id)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'error' => 'This barber is already booked for the selected date and time.'
            ], 409);
        }

        $booking->update([
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'barber_id' => $validated['barber_id'],
            'status' => 'rescheduled',
        ]);

        // Send reschedule confirmation email
        $booking = Booking::with('barber.user', 'user')->find($booking->id);
        if ($booking->user && $booking->user->email) {
            Mail::to($booking->user->email)->send(new RescheduleConfirmation($booking));
        }

        return response()->json(['success' => true, 'booking' => $booking]);
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after_or_equal:today',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        $barberId = $request->input('barber_id');
        $date = $request->input('date');
        $bookingId = $request->input('booking_id');

        // All possible slots
        $allSlots = [
            '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];

        // Get booked slots for the barber on the date, excluding the current booking if rescheduling
        $query = Booking::where('barber_id', $barberId)
            ->whereDate('booking_date', $date)
            ->whereNotIn('status', ['cancelled']);
        if ($bookingId) {
            $query->where('id', '!=', $bookingId);
        }
        $bookedSlots = $query->pluck('booking_time')->toArray();

        $availableSlots = array_values(array_diff($allSlots, $bookedSlots));

        return response()->json(['available_slots' => $availableSlots]);
    }

    /**
     * Handle customer check-in for an appointment.
     */
    public function checkIn(Booking $booking)
    {
        // Verify the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update booking status
        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        // Create notification for the barber
        Notification::create([
            'user_id' => $booking->barber->user_id,
            'type' => 'check_in',
            'title' => 'Customer Checked In',
            'message' => sprintf(
                '%s has checked in for their appointment at %s',
                $booking->user->name,
                Carbon::parse($booking->booking_time)->format('g:i A')
            ),
            'data' => [
                'booking_id' => $booking->id,
                'customer_name' => $booking->user->name,
                'booking_time' => $booking->booking_time,
            ],
        ]);

        return response()->json([
            'message' => 'Successfully checked in',
            'booking' => $booking->fresh(['user', 'service'])
        ]);
    }

    /**
     * Get all users for admin/barber booking selection
     */
    public function getUsers()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'barber'])) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::where('role', 'customer')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json(['users' => $users]);
    }
}
