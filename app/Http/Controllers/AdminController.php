<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barber;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'total_barbers' => Barber::count(),
                'active_barbers' => Barber::where('is_available', true)->count(),
            ]
        ]);
    }

    public function users()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::select('id', 'name', 'email', 'is_active', 'is_admin', 'created_at')
                ->with('barber')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }

    public function toggleUserStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'User status updated successfully');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }

    public function assignBarber(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'required|string',
            'years_of_experience' => 'required|integer|min:0',
            'specialties' => 'required|array|min:1',
            'profile_photo' => 'required|image|max:2048',
        ]);

        $profilePhotoPath = $request->file('profile_photo')->store('barber-photos', 'public');

        Barber::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'bio' => $validated['bio'],
            'years_of_experience' => $validated['years_of_experience'],
            'specialties' => $validated['specialties'],
            'profile_photo' => $profilePhotoPath,
            'is_available' => true,
        ]);

        return back()->with('success', 'User assigned as barber successfully');
    }

    public function removeBarber(User $user)
    {
        $user->barber()->delete();
        return back()->with('success', 'Barber role removed successfully');
    }

    public function upcomingBookings(Request $request)
    {
        try {
            // Debug: Check if we're hitting the method
            \Log::info('Entering upcomingBookings method', [
                'user' => auth()->user()->only(['id', 'name', 'email', 'is_admin'])
            ]);

            // First, let's check raw bookings count
            $totalBookings = Booking::count();
            \Log::info('Total bookings in database:', ['count' => $totalBookings]);

            // Get all bookings without pagination first to debug
            $allBookings = Booking::with(['user', 'barber'])
                ->orderBy('booking_date', 'desc')
                ->orderBy('booking_time', 'desc')
                ->get();

            \Log::info('All bookings:', [
                'count' => $allBookings->count(),
                'first_booking' => $allBookings->first(),
                'has_user' => $allBookings->first()?->user !== null,
                'has_barber' => $allBookings->first()?->barber !== null
            ]);

            // Now get paginated results
            $bookings = Booking::with(['user', 'barber'])
                ->select([
                    'id',
                    'user_id',
                    'barber_id',
                    'booking_date',
                    'booking_time',
                    'status',
                    'service_price',
                    'deposit_amount'
                ])
                ->orderBy('booking_date', 'desc')
                ->orderBy('booking_time', 'desc');

            // Apply date filter
            if ($request->date_filter) {
                $bookings->when($request->date_filter, function ($query, $filter) {
                    return match($filter) {
                        'today' => $query->whereDate('booking_date', today()),
                        'tomorrow' => $query->whereDate('booking_date', today()->addDay()),
                        'this_week' => $query->whereBetween('booking_date', [
                            today(),
                            today()->endOfWeek(),
                        ]),
                        'next_week' => $query->whereBetween('booking_date', [
                            today()->next()->startOfWeek(),
                            today()->next()->endOfWeek(),
                        ]),
                        'this_month' => $query->whereMonth('booking_date', now()->month)
                            ->whereYear('booking_date', now()->year),
                        'next_month' => $query->whereMonth('booking_date', now()->addMonth()->month)
                            ->whereYear('booking_date', now()->addMonth()->year),
                        'future' => $query->where('booking_date', '>=', today()),
                        'past' => $query->where('booking_date', '<', today()),
                        default => $query,
                    };
                });
            }

            // Apply status filter
            if ($request->status) {
                $bookings->where('status', $request->status);
            }

            $paginatedBookings = $bookings->paginate(10)->through(function ($booking) {
                return [
                    'id' => $booking->id,
                    'user' => [
                        'id' => $booking->user->id,
                        'name' => $booking->user->name,
                        'email' => $booking->user->email,
                    ],
                    'barber' => $booking->barber ? [
                        'id' => $booking->barber->id,
                        'name' => $booking->barber->name,
                    ] : null,
                    'booking_date' => $booking->booking_date,
                    'booking_time' => $booking->booking_time,
                    'status' => $booking->status,
                    'service_price' => $booking->service_price,
                    'deposit_amount' => $booking->deposit_amount,
                ];
            });

            \Log::info('Final paginated bookings:', [
                'total' => $paginatedBookings->total(),
                'per_page' => $paginatedBookings->perPage(),
                'current_page' => $paginatedBookings->currentPage(),
                'data' => $paginatedBookings->items()
            ]);

            return Inertia::render('Admin/Bookings', [
                'bookings' => $paginatedBookings,
                'filters' => [
                    'date_filter' => $request->date_filter,
                    'status' => $request->status,
                ],
                'date_filters' => [
                    'today' => 'Today',
                    'tomorrow' => 'Tomorrow',
                    'this_week' => 'This Week',
                    'next_week' => 'Next Week',
                    'this_month' => 'This Month',
                    'next_month' => 'Next Month',
                    'future' => 'All Future',
                    'past' => 'Past Bookings',
                ],
                'statuses' => [
                    'pending_payment' => 'Pending Payment',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                    'completed' => 'Completed',
                    'rescheduled' => 'Rescheduled',
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in upcomingBookings:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
