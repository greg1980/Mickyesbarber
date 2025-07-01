<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::whereNotNull('email_verified_at')->count(),
            'activeBarbers' => User::where('role', 'barber')
                ->whereHas('barber', function($q) {
                    $q->where('is_approved', 1);
                })->count(),
            'totalAppointments' => Booking::count(),
        ];

        // Gather recent activities from various models
        $activities = [];

        // New barber registrations (pending approval)
        $barberRegistrations = \App\Models\Barber::with('user')
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(function($barber) {
                return [
                    'description' => 'New barber registration: ' . ($barber->user->name ?? 'Unknown'),
                    'date' => $barber->created_at->format('Y-m-d h:i A'),
                ];
            });
        $activities = array_merge($activities, $barberRegistrations->toArray());

        // Barber approvals
        $barberApprovals = \App\Models\Barber::with('user')
            ->where('is_approved', 1)
            ->orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(function($barber) {
                return [
                    'description' => 'Barber approved: ' . ($barber->user->name ?? 'Unknown'),
                    'date' => $barber->updated_at->format('Y-m-d h:i A'),
                ];
            });
        $activities = array_merge($activities, $barberApprovals->toArray());

        // New customer registrations
        $customerRegistrations = User::where('role', 'customer')
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(function($user) {
                return [
                    'description' => 'New customer registration: ' . $user->name,
                    'date' => $user->created_at->format('Y-m-d h:i A'),
                ];
            });
        $activities = array_merge($activities, $customerRegistrations->toArray());

        // New bookings
        $bookings = Booking::with(['user', 'barber.user'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(function($booking) {
                return [
                    'description' => 'Booking created for ' . ($booking->user->name ?? 'Unknown') . ' with ' . ($booking->barber->user->name ?? 'Unknown'),
                    'date' => $booking->created_at->format('Y-m-d h:i A'),
                ];
            });
        $activities = array_merge($activities, $bookings->toArray());

        // Booking cancellations
        $cancelledBookings = Booking::with('user')
            ->where('status', 'cancelled')
            ->orderByDesc('updated_at')
            ->take(10)
            ->get()
            ->map(function($booking) {
                return [
                    'description' => 'Booking cancelled: ' . ($booking->user->name ?? 'Unknown'),
                    'date' => $booking->updated_at->format('Y-m-d h:i A'),
                ];
            });
        $activities = array_merge($activities, $cancelledBookings->toArray());

        // Transformation submissions
        if (class_exists('App\\Models\\Transformation')) {
            $transformations = \App\Models\Transformation::with('user')
                ->orderByDesc('created_at')
                ->take(10)
                ->get()
                ->map(function($trans) {
                    return [
                        'description' => 'Transformation submitted by ' . ($trans->user->name ?? 'Unknown'),
                        'date' => $trans->created_at->format('Y-m-d h:i A'),
                    ];
                });
            $activities = array_merge($activities, $transformations->toArray());
        }

        // Sort all activities by date descending and take the latest 10
        usort($activities, function($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });
        $recentActivity = array_slice($activities, 0, 10);

        // Find the top barber by completed haircuts, then by rating
        $topBarber = \App\Models\User::where('role', 'barber')
            ->with(['barber', 'barber.bookings' => function($q) {
                $q->where('status', 'completed');
            }, 'barber.ratings'])
            ->get()
            ->map(function($barber) {
                $haircuts = $barber->barber && $barber->barber->bookings ? $barber->barber->bookings->count() : 0;
                $ratings = $barber->barber && $barber->barber->ratings ? $barber->barber->ratings->pluck('rating') : collect();
                $avgRating = $ratings->count() ? round($ratings->avg(), 1) : 0;
                return [
                    'name' => $barber->name,
                    'photo' => $barber->profile_photo_url ?? null,
                    'haircuts' => $haircuts,
                    'rating' => $avgRating,
                ];
            })
            ->sortByDesc('haircuts')
            ->sortByDesc('rating')
            ->first();

        // Pending barbers (is_approved is null)
        $pendingBarbers = \App\Models\Barber::with('user')
            ->whereNull('is_approved')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($barber) {
                return [
                    'id' => $barber->id,
                    'name' => $barber->user->name ?? '',
                    'email' => $barber->user->email ?? '',
                    'applied_date' => $barber->created_at ? $barber->created_at->format('Y-m-d') : '',
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'topBarber' => $topBarber,
            'pendingBarbers' => $pendingBarbers,
        ]);
    }

    /**
     * Get today's appointment slots with booking info and status.
     */
    public function todaySlots()
    {
        $date = Carbon::today()->toDateString();
        $slotTimes = [
            '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];
        $slots = collect($slotTimes)->map(function ($time) use ($date) {
            $booking = Booking::with('user')
                ->where('booking_date', $date)
                ->where('booking_time', $time)
                ->whereNotIn('status', ['cancelled'])
                ->first();
            if ($booking) {
                return [
                    'time' => Carbon::createFromFormat('H:i', $time)->format('h:i A'),
                    'booking' => [
                        'user' => [
                            'name' => $booking->user->name ?? '',
                            'email' => $booking->user->email ?? '',
                            'photo' => $booking->user->profile_photo_url ?? '',
                        ],
                        'status' => $booking->status,
                    ],
                ];
            } else {
                return [
                    'time' => Carbon::createFromFormat('H:i', $time)->format('h:i A'),
                    'booking' => null,
                ];
            }
        });
        return response()->json(['slots' => $slots]);
    }

    public function pendingTransformations()
    {
        $transformations = \App\Models\Transformation::with(['user'])
            ->where('is_approved', false)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'user' => [
                        'name' => $t->user->name,
                        'profile_photo_url' => $t->user->profile_photo_url,
                    ],
                    'before' => $t->before_photo ? asset('storage/' . $t->before_photo) : null,
                    'after' => $t->after_photo ? asset('storage/' . $t->after_photo) : null,
                    'style' => $t->style,
                    'review' => $t->review,
                    'rating' => $t->rating,
                ];
            });
        return response()->json($transformations);
    }

    public function approveTransformation($id)
    {
        $transformation = \App\Models\Transformation::findOrFail($id);
        $transformation->is_approved = true;
        $transformation->save();
        return response()->json(['success' => true]);
    }

    public function rejectTransformation($id)
    {
        $transformation = \App\Models\Transformation::findOrFail($id);
        $transformation->is_approved = false;
        $transformation->status = 'rejected';
        $transformation->save();
        return response()->json(['success' => true]);
    }

    public function approvedTransformations()
    {
        $transformations = \App\Models\Transformation::with(['user'])
            ->where('is_approved', true)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'user' => [
                        'name' => $t->user->name,
                        'profile_photo_url' => $t->user->profile_photo_url,
                    ],
                    'before' => $t->before_photo ? asset('storage/' . $t->before_photo) : null,
                    'after' => $t->after_photo ? asset('storage/' . $t->after_photo) : null,
                    'style' => $t->style,
                    'review' => $t->review,
                ];
            });
        return response()->json($transformations);
    }
}
