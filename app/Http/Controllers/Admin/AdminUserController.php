<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'created_at', 'role', 'email_verified_at', 'profile_photo_path')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%");
        }

        if ($request->input('trashed') === '1') {
            $query->onlyTrashed();
        }

        $users = $query->paginate(10)->withQueryString();
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        $inactiveUsers = User::whereNull('email_verified_at')->count();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => $request->only('search', 'trashed'),
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
        ]);
    }

    public function completedBookingsCount(User $user)
    {
        $count = \App\Models\Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        return response()->json(['count' => $count]);
    }

    public function userStats(User $user)
    {
        $lastVisited = \App\Models\Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('booking_date')
            ->value('booking_date');
        $upcomingBookings = \App\Models\Booking::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->where(function($q) {
                $q->where('booking_date', '>', now()->toDateString())
                  ->orWhere(function($q2) {
                      $q2->where('booking_date', now()->toDateString())
                         ->where('booking_time', '>=', now()->toTimeString());
                  });
            })
            ->count();
        return response()->json([
            'last_visited' => $lastVisited,
            'upcoming_bookings' => $upcomingBookings
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return response()->json(['success' => true]);
    }

    public function userGrowth()
    {
        $users = \App\Models\User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill missing months with 0
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months->push([
                'month' => $month,
                'count' => 0
            ]);
        }
        foreach ($users as $user) {
            $months = $months->map(function ($item) use ($user) {
                if ($item['month'] === $user->month) {
                    $item['count'] = $user->count;
                }
                return $item;
            });
        }
        return response()->json($months->values());
    }

    public function barberSchedule($barberId)
    {
        $schedules = \App\Models\BarberAvailabilitySchedule::where('barber_id', $barberId)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get(['day_of_week', 'start_time', 'end_time', 'is_available']);
        return response()->json($schedules);
    }

    public function allBarbers()
    {
        $barbers = \App\Models\Barber::with('user')
            ->where('is_approved', 1)
            ->get();
        return response()->json(['barbers' => $barbers]);
    }

    public function barberBookingStats($barberId)
    {
        $bookings = \App\Models\Booking::where('barber_id', $barberId)->get();

        // Bookings by day of week
        $byDay = $bookings->groupBy(function($b) {
            return \Carbon\Carbon::parse($b->date)->format('D');
        })->map->count();

        // Completed vs Cancelled
        $completed = $bookings->where('status', 'completed')->count();
        $cancelled = $bookings->where('status', 'cancelled')->count();

        // Total bookings
        $total = $bookings->count();

        return response()->json([
            'byDay' => $byDay,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'total' => $total,
        ]);
    }

    public function barberBookings($barberId)
    {
        $bookings = \App\Models\Booking::where('barber_id', $barberId)
            ->with('customer')
            ->orderBy('date', 'desc')
            ->paginate(10);

        $bookings->getCollection()->transform(function($booking) {
            return [
                'id' => $booking->id,
                'customer_name' => $booking->customer->name,
                'date' => $booking->date,
                'time' => $booking->time,
                'price' => $booking->price,
            ];
        });

        return response()->json($bookings);
    }

    public function revokeApproval($barberId)
    {
        $barber = \App\Models\Barber::findOrFail($barberId);
        $barber->is_approved = false;
        $barber->save();
        // Change user role to customer
        $user = $barber->user;
        if ($user) {
            $user->role = 'customer';
            $user->save();
            // Send notification email
            \Mail::to($user->email)->send(new \App\Mail\BarberStatusRevoked($user));
        }
        return response()->json(['success' => true]);
    }

    public function allBarbersForManagement()
    {
        $barbers = \App\Models\Barber::with(['user' => function ($query) {
                $query->withTrashed(); // Include soft deleted users
            }])
            ->get();
        return response()->json(['barbers' => $barbers]);
    }
}
