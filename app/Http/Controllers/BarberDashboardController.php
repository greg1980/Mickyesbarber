<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Message;
use App\Mail\AppointmentReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Inertia\Inertia;

class BarberDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Barber/Dashboard');
    }

    public function getDashboardData(Request $request)
    {
        $barber = $request->user()->barber;
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // Get today's appointments
        $todayAppointments = $barber->bookings()
            ->whereDate('booking_date', $today)
            ->with('user')
            ->orderBy('booking_time')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client_name' => $booking->user->name,
                    'booking_time' => $booking->booking_time,
                    'service' => $booking->service,
                    'status' => $booking->status
                ];
            });

        // Calculate weekly revenue
        $weeklyRevenue = $barber->bookings()
            ->whereBetween('booking_date', [$weekStart, $weekEnd])
            ->where('status', 'completed')
            ->sum('service_price');

        // Get total unique clients
        $totalClients = $barber->bookings()
            ->distinct('user_id')
            ->count('user_id');

        // Get average rating
        $averageRating = $barber->transformations()
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;

        // Get recent messages
        $recentMessages = Message::where('recipient_id', $barber->user_id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_name' => $message->sender->name,
                    'content' => $message->content,
                    'created_at' => $message->created_at,
                    'is_read' => $message->is_read
                ];
            });

        return response()->json([
            'barberId' => $barber->id,
            'todayAppointments' => $todayAppointments,
            'weeklyRevenue' => $weeklyRevenue,
            'totalClients' => $totalClients,
            'averageRating' => $averageRating,
            'recentMessages' => $recentMessages
        ]);
    }

    public function getCalendarData(Request $request)
    {
        $date = Carbon::parse($request->query('date', now()));
        $barber = $request->user()->barber;

        $appointments = $barber->bookings()
            ->whereDate('booking_date', $date)
            ->with('user')
            ->orderBy('booking_time')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'client_name' => $booking->user->name,
                    'booking_date' => $booking->booking_date,
                    'booking_time' => $booking->booking_time,
                    'service' => $booking->service,
                    'status' => $booking->status
                ];
            });

        return response()->json([
            'appointments' => $appointments
        ]);
    }

    public function completeAppointment(Request $request, Booking $booking)
    {
        // Verify the booking belongs to the barber
        if ($booking->barber_id !== $request->user()->barber->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->update([
            'status' => 'completed'
        ]);

        return response()->json([
            'message' => 'Appointment marked as completed',
            'booking' => $booking
        ]);
    }

    public function sendReminder(Request $request, Booking $booking)
    {
        // Verify the booking belongs to the barber
        if ($booking->barber_id !== $request->user()->barber->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Send reminder email
        Mail::to($booking->user->email)->send(new AppointmentReminder($booking));

        return response()->json([
            'message' => 'Reminder sent successfully'
        ]);
    }
}
