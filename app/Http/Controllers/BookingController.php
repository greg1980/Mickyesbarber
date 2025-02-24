<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingCancelled;
use App\Models\Barber;

class BookingController extends Controller
{
    const SERVICE_PRICE = 25.00;
    const DEPOSIT_PERCENTAGE = 0.25;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'service_price' => 'required|numeric',
            'deposit_amount' => 'required|numeric',
        ]);

        $booking = Booking::create([
            'user_id' => auth()->id(),
            ...$validated,
            'status' => 'pending_payment',
        ]);

        return redirect()->route('booking.payment', $booking);
    }

    public function cancelBooking($id)
    {
        $booking = Auth::user()->bookings()->findOrFail($id);
        $bookingDate = Carbon::parse($booking->booking_date);
        $today = Carbon::today();

        // If cancellation is on the same day as appointment
        if ($bookingDate->isToday()) {
            return back()->with('error', 'Same-day cancellations are not allowed. Please contact us to reschedule.');
        }

        // If cancellation is before appointment day
        if ($bookingDate->isAfter($today)) {
            $booking->update(['status' => 'cancelled']);
            // Process refund of deposit
            $this->processRefund($booking);
            return back()->with('success', 'Booking cancelled and deposit will be refunded.');
        }

        return back()->with('error', 'Unable to cancel this booking.');
    }

    public function rescheduleBooking(Request $request, $id)
    {
        $booking = Auth::user()->bookings()->findOrFail($id);

        $validated = $request->validate([
            'new_date' => 'required|date|after:today',
            'new_time' => 'required',
        ]);

        $booking->update([
            'booking_date' => $validated['new_date'],
            'booking_time' => $validated['new_time'],
            'status' => 'rescheduled',
        ]);

        return back()->with('success', 'Appointment rescheduled successfully.');
    }

    private function processRefund($booking)
    {
        // Implement refund logic here using your payment provider
        // Only process refund for cancellations before the appointment day
    }

    public function getAvailableSlots(Request $request)
    {
        $date = $request->date;

        // Get all bookings for the selected date
        $bookedSlots = Booking::where('booking_date', $date)
            ->pluck('booking_time')
            ->map(fn($time) => $time->format('H:i'))
            ->toArray();

        // Define all possible time slots
        $allSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30'
        ];

        // Filter out booked slots
        $availableSlots = array_values(array_diff($allSlots, $bookedSlots));

        return response()->json(['slots' => $availableSlots]);
    }

    public function getUserBookings()
    {
        $bookings = Auth::user()->bookings()
            ->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->get();

        return response()->json(['bookings' => $bookings]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $oldStatus = $booking->status;
        $booking->update($validated);

        // Send appropriate notifications
        if ($oldStatus !== $booking->status) {
            switch ($booking->status) {
                case 'cancelled':
                    Mail::to($booking->user->email)
                        ->send(new BookingCancelled($booking));
                    break;
                case 'completed':
                    // Could add a completion email here
                    break;
            }
        }

        return back()->with('success', 'Booking updated successfully');
    }

    public function getAdminStats()
    {
        $stats = [
            'today' => Booking::whereDate('booking_date', today())->count(),
            'upcoming' => Booking::where('booking_date', '>', today())->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'revenue' => Booking::where('status', 'completed')->sum('service_price'),
        ];

        return response()->json($stats);
    }

    public function getAvailableBarbers(Request $request)
    {
        $date = $request->date;
        $time = $request->time;

        // Get all barbers with their details
        $barbers = Barber::select('id', 'name', 'profile_photo', 'years_of_experience', 'specialties', 'bio')
            ->get();

        // Get bookings for the requested date and time
        $bookedBarberIds = Booking::where('booking_date', $date)
            ->where('booking_time', $time)
            ->pluck('barber_id');

        // Filter out booked barbers
        $availableBarberIds = $barbers->pluck('id')
            ->diff($bookedBarberIds)
            ->values()
            ->all();

        return response()->json([
            'barbers' => $barbers,
            'available_barber_ids' => $availableBarberIds
        ]);
    }

    public function showPayment(Booking $booking)
    {
        return Inertia::render('Booking/Payment', [
            'booking' => $booking->load('barber'),
            'amount' => $booking->deposit_amount,
        ]);
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
            // Add any other payment validation you need
        ]);

        // Process payment logic here

        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed'
        ]);

        return redirect()->route('dashboard')->with('success', 'Booking confirmed successfully!');
    }
}
