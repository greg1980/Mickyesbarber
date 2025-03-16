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
use Illuminate\Support\Facades\Log;

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
                ->get(),
            'stripe_key' => config('services.stripe.key')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after:today',
            'time' => 'required',
            'notes' => 'nullable|string|max:500',
            'payment_type' => 'required|in:deposit,full'
        ]);

        Log::info('Creating new booking', [
            'user_id' => auth()->id(),
            'barber_id' => $validated['barber_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'payment_type' => $validated['payment_type']
        ]);

        // Calculate service price and initial amounts
        $servicePrice = self::SERVICE_PRICE;
        $initialDeposit = 0; // Start with 0 deposit
        $initialBalance = $servicePrice; // Full amount as initial balance

        $booking = auth()->user()->bookings()->create([
            'barber_id' => $validated['barber_id'],
            'booking_date' => $validated['date'],
            'booking_time' => $validated['time'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'service_price' => $servicePrice,
            'deposit_amount' => $initialDeposit,
            'balance_amount' => $initialBalance,
            'payment_status' => 'pending'
        ]);

        Log::info('Booking created', [
            'booking_id' => $booking->id,
            'service_price' => $booking->service_price,
            'deposit_amount' => $booking->deposit_amount,
            'balance_amount' => $booking->balance_amount
        ]);

        // Return JSON response with booking data
        return response()->json([
            'id' => $booking->id,
            'service_price' => $booking->service_price,
            'deposit_amount' => $booking->deposit_amount,
            'balance_amount' => $booking->balance_amount,
            'status' => $booking->status,
            'message' => 'Booking created successfully'
        ]);
    }

    public function showPayment(Booking $booking, Request $request)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Get the payment type from the request
        $paymentType = $request->payment_type ?? 'deposit';

        // Calculate payment amount based on payment type
        $paymentAmount = $paymentType === 'deposit'
            ? ($booking->service_price * self::DEPOSIT_PERCENTAGE)
            : $booking->service_price;

        $intent = PaymentIntent::create([
            'amount' => $paymentAmount * 100, // Convert to cents for Stripe
            'currency' => 'gbp',
            'metadata' => [
                'booking_id' => $booking->id,
                'payment_type' => $paymentType
            ]
        ]);

        return Inertia::render('Bookings/Payment', [
            'booking' => $booking->load('barber'),
            'clientSecret' => $intent->client_secret,
            'paymentAmount' => $paymentAmount,
            'paymentType' => $paymentType,
            'depositAmount' => $booking->service_price * self::DEPOSIT_PERCENTAGE,
            'fullAmount' => $booking->service_price
        ]);
    }

    public function processPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Get the payment amount from the request
        $paymentAmount = $request->amount;

        // Calculate the new balance
        $newBalance = $booking->service_price - ($booking->deposit_amount + $paymentAmount);

        // Update booking with the payment
        $booking->update([
            'deposit_amount' => $booking->deposit_amount + $paymentAmount,
            'balance_amount' => $newBalance,
            'payment_status' => $newBalance <= 0 ? 'fully_paid' : 'deposit_paid',
            'status' => 'confirmed',
            'stripe_payment_id' => $request->payment_intent_id
        ]);

        return redirect()->route('bookings.user')->with('success',
            $newBalance <= 0
                ? 'Full payment processed successfully!'
                : 'Deposit payment processed successfully!'
        );
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
            'booking_date' => $validated['date'],
            'booking_time' => $validated['time'],
            'status' => 'rescheduled'
        ]);

        return back()->with('success', 'Booking rescheduled successfully');
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $barber = Barber::find($request->barber_id);
        $bookedSlots = $barber->bookings()
            ->whereDate('booking_date', $request->date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('booking_time')
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
