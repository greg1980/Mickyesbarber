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
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;
use App\Mail\BookingRescheduled;
use App\Events\BookingUpdated;

class BookingController extends Controller
{
    const SERVICE_PRICE = 25.00;
    const DEPOSIT_PERCENTAGE = 0.25;

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'barber_id' => 'required|exists:barbers,id',
                'booking_date' => 'required|date',
                'booking_time' => 'required',
                'service_price' => 'required|numeric',
                'deposit_amount' => 'required|numeric',
            ]);

            // Convert time from 12-hour to 24-hour format for storage
            $booking_time = Carbon::createFromFormat('g:i A', $validated['booking_time'])->format('H:i:s');

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'barber_id' => $validated['barber_id'],
                'booking_date' => $validated['booking_date'],
                'booking_time' => $booking_time,
                'service_price' => $validated['service_price'],
                'deposit_amount' => $validated['deposit_amount'],
                'status' => 'pending_payment',
            ]);

            // Get updated booked slots for the date
            $bookedSlots = $this->getBookedSlots($validated['booking_date']);

            // Broadcast the booking creation
            broadcast(new BookingUpdated($booking, $bookedSlots, 'created'));

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking' => $booking,
                'payment_url' => route('booking.payment', $booking->id)
            ]);
        } catch (\Exception $e) {
            \Log::error('Booking creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
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

            // Send cancellation email to customer
            Mail::to($booking->user->email)->send(new BookingCancellation($booking));

            // Send cancellation email to barber
            if ($booking->barber && $booking->barber->user) {
                Mail::to($booking->barber->user->email)->send(new BookingCancellation($booking));
            }

            // Process refund of deposit
            $this->processRefund($booking);

            // Get updated booked slots for the date
            $bookedSlots = $this->getBookedSlots($booking->booking_date);

            // Broadcast the cancellation
            broadcast(new BookingUpdated($booking, $bookedSlots, 'cancelled'));

            return back()->with('success', 'Booking cancelled and deposit will be refunded.');
        }

        return back()->with('error', 'Unable to cancel this booking.');
    }

    public function rescheduleBooking(Request $request, $id)
    {
        try {
            $booking = Auth::user()->bookings()->findOrFail($id);

            $validated = $request->validate([
                'new_date' => 'required|date|after:today',
                'new_time' => 'required',
            ]);

            // Convert new time from 12-hour to 24-hour format for storage
            $new_time = Carbon::createFromFormat('g:i A', $validated['new_time'])->format('H:i:s');

            // Check if the new time slot is available
            $existingBooking = Booking::where('booking_date', $validated['new_date'])
                ->where('booking_time', $new_time)
                ->whereNotIn('status', ['cancelled'])
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot is no longer available. Please select another time.'
                ], 422);
            }

            // Store old datetime for email
            $oldDateTime = [
                'date' => $booking->booking_date,
                'time' => Carbon::parse($booking->booking_time)->format('g:i A')
            ];

            $booking->update([
                'booking_date' => $validated['new_date'],
                'booking_time' => $new_time,
                'status' => 'rescheduled',
            ]);

            // Send rescheduling confirmation email to customer
            Mail::to($booking->user->email)->send(new BookingRescheduled($booking, $oldDateTime));

            // Send rescheduling notification to barber
            if ($booking->barber && $booking->barber->user) {
                Mail::to($booking->barber->user->email)->send(new BookingRescheduled($booking, $oldDateTime));
            }

            // Get updated booked slots for both old and new dates
            $oldDateBookedSlots = $this->getBookedSlots($oldDateTime['date']);
            $newDateBookedSlots = $this->getBookedSlots($validated['new_date']);

            // Broadcast for both dates
            broadcast(new BookingUpdated($booking, $oldDateBookedSlots, 'rescheduled_from'));
            broadcast(new BookingUpdated($booking, $newDateBookedSlots, 'rescheduled_to'));

            return response()->json([
                'success' => true,
                'message' => 'Appointment rescheduled successfully.',
                'booking' => [
                    ...collect($booking->refresh())->except(['booking_time'])->toArray(),
                    'booking_time' => Carbon::parse($booking->booking_time)->format('g:i A'),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Booking reschedule failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reschedule booking. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function processRefund($booking)
    {
        // Implement refund logic here using your payment provider
        // Only process refund for cancellations before the appointment day
    }

    public function getAvailableSlots(Request $request)
    {
        $date = $request->date;

        // Get all bookings for the selected date that are not cancelled
        $bookedSlots = Booking::where('booking_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('booking_time')
            ->map(function($time) {
                return Carbon::parse($time)->format('g:i A');
            })
            ->toArray();

        // Define all possible time slots with AM/PM format (1-hour intervals)
        $allSlots = [
            '9:00 AM', '10:00 AM', '11:00 AM',
            '1:00 PM', '2:00 PM', '3:00 PM',
            '4:00 PM', '5:00 PM', '6:00 PM', '7:00 PM'
        ];

        // Return both all slots and booked slots
        return response()->json([
            'slots' => $allSlots,
            'booked_slots' => $bookedSlots
        ]);
    }

    public function getUserBookings()
    {
        $bookings = Auth::user()->bookings()
            ->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->get()
            ->map(function($booking) {
                return [
                    ...collect($booking)->except(['booking_time'])->toArray(),
                    'booking_time' => Carbon::parse($booking->booking_time)->format('g:i A'),
                ];
            });

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

        // Get all barbers who are marked as available
        $barbers = Barber::where('is_available', true)
            ->select('id', 'name', 'profile_photo', 'years_of_experience', 'specialties', 'bio')
            ->get();

        // Get bookings for the requested date and time that are not cancelled
        $bookedBarberIds = Booking::where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereNotIn('status', ['cancelled'])
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
            'amount' => (float) $booking->deposit_amount,
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

        // Send booking confirmation email
        Mail::to($booking->user->email)->send(new BookingConfirmation($booking));

        return redirect()->route('dashboard')->with('success', 'Booking confirmed successfully!');
    }

    private function getBookedSlots($date)
    {
        return Booking::where('booking_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('booking_time')
            ->map(function($time) {
                return Carbon::parse($time)->format('g:i A');
            })
            ->toArray();
    }
}
