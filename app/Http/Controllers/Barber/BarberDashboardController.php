<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\BarberAvailabilitySchedule;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

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
            // Notify all admins
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'system',
                    'title' => 'Booking Completed',
                    'message' => 'Booking #' . $booking->id . ' has been completed by ' . ($booking->barber->user->name ?? 'Barber') . '.',
                    'data' => ['booking_id' => $booking->id],
                ]);
            }
        }

        return response()->json([
            'message' => 'Appointment status updated successfully',
            'booking' => $booking->fresh(['user', 'service'])
        ]);
    }

    /**
     * Show all transformations for the logged-in barber.
     */
    public function transformations(Request $request)
    {
        $barber = Auth::user()->barber;

        // Determine pagination limit based on device type
        $isMobile = $request->header('User-Agent') && (
            str_contains(strtolower($request->header('User-Agent')), 'mobile') ||
            str_contains(strtolower($request->header('User-Agent')), 'android') ||
            str_contains(strtolower($request->header('User-Agent')), 'iphone')
        );

        $perPage = $isMobile ? 4 : 10;

        $transformations = \App\Models\Transformation::with('user')
            ->where('barber_id', $barber->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return Inertia::render('Barber/Transformations', [
            'transformations' => $transformations,
        ]);
    }

    /**
     * Show all bookings for the logged-in barber.
     */
    public function bookings(Request $request)
    {
        $barber = Auth::user()->barber;
        $search = $request->query('search');
        $status = $request->query('status');
        $date = $request->query('date');

        // Determine pagination limit based on device type
        $isMobile = $request->header('User-Agent') && (
            str_contains(strtolower($request->header('User-Agent')), 'mobile') ||
            str_contains(strtolower($request->header('User-Agent')), 'android') ||
            str_contains(strtolower($request->header('User-Agent')), 'iphone')
        );

        $perPage = $isMobile ? 4 : 15;

        $query = $barber->bookings()
            ->with(['user', 'service'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        // Apply search filter
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Apply status filter
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Apply date filter
        if ($date) {
            $query->whereDate('booking_date', $date);
        }

        $bookings = $query->paginate($perPage)
            ->through(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service_name' => $booking->service->name ?? 'N/A',
                    'booking_date' => $booking->booking_date->format('Y-m-d'),
                    'booking_time' => is_object($booking->booking_time) ? $booking->booking_time->format('H:i') : $booking->booking_time,
                    'booking_datetime' => $booking->booking_date->format('Y-m-d') . ' ' . (is_object($booking->booking_time) ? $booking->booking_time->format('H:i:s') : $booking->booking_time),
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'service_price' => $booking->service_price,
                    'notes' => $booking->notes,
                    'user' => [
                        'name' => $booking->user->name ?? '',
                        'profile_photo_url' => $booking->user->profile_photo_url ?? '',
                        'email' => $booking->user->email ?? '',
                    ],
                ];
            });

        return Inertia::render('Barber/Bookings', [
            'bookings' => $bookings,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'date' => $date,
            ],
        ]);
    }

    /**
     * Export bookings as PDF for the logged-in barber.
     */
    public function exportBookingsPDF(Request $request)
    {
        $barber = Auth::user()->barber;
        $search = $request->query('search');
        $status = $request->query('status');
        $date = $request->query('date');

        $query = $barber->bookings()
            ->with(['user', 'service'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        // Apply same filters as the regular bookings method
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($date) {
            $query->whereDate('booking_date', $date);
        }

        $bookings = $query->get()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'service_name' => $booking->service->name ?? 'N/A',
                'booking_date' => $booking->booking_date->format('M d, Y'),
                'booking_time' => is_object($booking->booking_time) ? $booking->booking_time->format('H:i') : $booking->booking_time,
                'status' => ucfirst($booking->status),
                'payment_status' => ucfirst(str_replace('_', ' ', $booking->payment_status ?? 'N/A')),
                'service_price' => number_format($booking->service_price, 2),
                'customer_name' => $booking->user->name ?? 'N/A',
                'customer_email' => $booking->user->email ?? 'N/A',
            ];
        });

        // Generate report data
        $reportData = [
            'barber_name' => $barber->user->name ?? 'N/A',
            'generated_at' => now()->format('M d, Y \a\t H:i'),
            'total_bookings' => $bookings->count(),
            'total_revenue' => '£' . number_format($bookings->sum(function($booking) {
                return (float) str_replace(',', '', $booking['service_price']);
            }), 2),
            'bookings' => $bookings,
            'filters' => [
                'search' => $search,
                'status' => $status ? ucfirst($status) : 'All',
                'date' => $date ? \Carbon\Carbon::parse($date)->format('M d, Y') : 'All Dates',
            ],
        ];

        $pdf = Pdf::loadView('pdfs.bookings-report', $reportData);

        $filename = 'bookings-report-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
