<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\Barber;
use App\Models\Notification;

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

        $perPage = $request->input('perPage', 10);
        $users = $query->paginate($perPage)->withQueryString();
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
        // Use SQLite-compatible date formatting
        if (\DB::connection()->getDriverName() === 'sqlite') {
            $users = \App\Models\User::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
                ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $users = \App\Models\User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

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
            return \Carbon\Carbon::parse($b->booking_date)->format('D');
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
            ->with('user')
            ->orderBy('booking_date', 'desc')
            ->paginate(10);

        $bookings->getCollection()->transform(function($booking) {
            return [
                'id' => $booking->id,
                'customer_name' => $booking->user ? $booking->user->name : 'N/A',
                'date' => $booking->booking_date,
                'time' => $booking->booking_time,
                'price' => $booking->service_price,
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

    public function addBarber(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:existing,new',
            'bio' => 'required|string|max:1000',
            'years_of_experience' => 'required|integer|min:0|max:80',
            'mobile_contact' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9\-\+\(\)\s]+$/'
            ],
            // New user fields
            'name' => 'required_if:user_type,new|string|max:255',
            'email' => 'required_if:user_type,new|string|email|max:255|unique:users',
            'password' => 'required_if:user_type,new|string|min:8',
            // Existing user fields
            'existing_email' => 'required_if:user_type,existing|string|email|max:255|exists:users,email',
        ]);

        try {
            if ($request->user_type === 'new') {
                // Create new user account
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => User::ROLE_BARBER,
                ]);
            } else {
                // Find existing user
                $user = User::where('email', $request->existing_email)->first();

                if (!$user) {
                    return back()->withErrors(['existing_email' => 'User not found with this email address.']);
                }

                // Check if user is already a barber
                if (Barber::where('user_id', $user->id)->exists()) {
                    return back()->withErrors(['existing_email' => 'This user is already registered as a barber.']);
                }

                // Update user role to barber if they're currently a customer
                if ($user->role === User::ROLE_CUSTOMER) {
                    $user->update(['role' => User::ROLE_BARBER]);
                }
            }

            // Create barber profile
            Barber::create([
                'user_id' => $user->id,
                'bio' => $request->bio,
                'years_of_experience' => $request->years_of_experience,
                'mobile_contact' => $request->mobile_contact,
                'is_approved' => true, // Auto-approve admin-added barbers
            ]);

            // Notify the user
            Notification::create([
                'user_id' => $user->id,
                'type' => 'system',
                'title' => 'Barber Account Created',
                'message' => 'Your barber account has been created and approved by an administrator.',
                'data' => ['admin_id' => auth()->id()],
            ]);

            return redirect()->back()->with('success',
                $request->user_type === 'new'
                    ? 'New user and barber profile created successfully!'
                    : 'Barber profile added to existing user successfully!'
            );

        } catch (\Exception $e) {
            \Log::error('Error adding barber: ' . $e->getMessage());
            return back()->withErrors(['general' => 'An error occurred while adding the barber. Please try again.']);
        }
    }
}
