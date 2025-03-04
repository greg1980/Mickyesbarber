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
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BookingController extends Controller
{
    const SERVICE_PRICE = 25.00;
    const DEPOSIT_PERCENTAGE = 0.25;

    public function index()
    {
        return Inertia::render('Bookings/Index', [
            'bookings' => auth()->user()->bookings()
                ->with('barber')
                ->latest()
                ->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Bookings/Create', [
            'barbers' => Barber::select('id', 'name', 'profile_photo', 'years_of_experience', 'specialties')
                ->where('is_available', true)
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after:today',
            'time' => 'required',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate service price and deposit
        $servicePrice = self::SERVICE_PRICE;
        $depositAmount = $servicePrice * self::DEPOSIT_PERCENTAGE;

        $booking = auth()->user()->bookings()->create([
            'barber_id' => $validated['barber_id'],
            'booking_date' => $validated['date'],
            'booking_time' => $validated['time'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'service_price' => $servicePrice,
            'deposit_amount' => $depositAmount,
            'payment_status' => 'pending'
        ]);

        // Redirect to payment page
        return redirect()->route('booking.payment', $booking);
    }

    public function showPayment(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $booking->deposit_amount * 100,
            'currency' => 'gbp',
            'metadata' => [
                'booking_id' => $booking->id
            ]
        ]);

        return Inertia::render('Bookings/Payment', [
            'booking' => $booking->load('barber'),
            'clientSecret' => $intent->client_secret,
        ]);
    }

    public function processPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'stripe_payment_id' => $request->payment_intent
        ]);

        return redirect()->route('bookings.user')->with('success', 'Booking confirmed successfully!');
    }

    public function getUserBookings()
    {
        return Inertia::render('Bookings/Index', [
            'bookings' => auth()->user()->bookings()
                ->with('barber')
                ->latest()
                ->paginate(10)
        ]);
    }

    public function cancelBooking(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully');
    }

    public function reschedule(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => 'required|date|after:today',
            'time' => 'required',
        ]);

        $booking->update([
            'appointment_date' => $validated['date'],
            'appointment_time' => $validated['time'],
            'status' => 'rescheduled'
        ]);

        return back()->with('success', 'Booking rescheduled successfully');
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after:today',
        ]);

        $barber = Barber::find($request->barber_id);
        $bookedSlots = $barber->bookings()
            ->whereDate('appointment_date', $request->date)
            ->pluck('appointment_time')
            ->toArray();

        $allSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
            '15:00', '15:30', '16:00', '16:30', '17:00'
        ];

        $availableSlots = array_values(array_diff($allSlots, $bookedSlots));

        return response()->json(['available_slots' => $availableSlots]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no-show'
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Booking status updated successfully');
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

    public function getDashboardData()
    {
        $user = auth()->user();

        $upcomingAppointments = $user->bookings()
            ->where('booking_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->count();

        $lastVisit = $user->bookings()
            ->where('status', 'completed')
            ->latest('booking_date')
            ->first();

        // Calculate loyalty points (10 points per completed booking)
        $loyaltyPoints = $user->bookings()
            ->where('status', 'completed')
            ->count() * 10;

        return response()->json([
            'upcomingAppointments' => $upcomingAppointments,
            'lastVisit' => $lastVisit ? $lastVisit->booking_date->format('M d, Y') : null,
            'loyaltyPoints' => $loyaltyPoints
        ]);
    }
}
