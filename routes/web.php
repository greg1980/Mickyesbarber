<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\BarberRegisterController;
use App\Http\Controllers\Admin\BarberApprovalController;
use App\Http\Controllers\Barber\BarberDashboardController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransformationController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\BarberStatsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Customer\CustomerStatsController;
use App\Http\Controllers\BarberRegistrationController;

// CSRF refresh route
Route::get('/refresh-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->middleware(['web']);

// Public routes
Route::get('/', function () {
    return Inertia::render('HomePage');
})->name('home');

Route::get('/services', function () {
    $services = \App\Models\Service::where('is_active', true)
                                   ->orderBy('sort_order')
                                   ->get();

    return Inertia::render('ServicePage', [
        'services' => $services
    ]);
})->name('services');

Route::get('/about', function () {
    // Get approved barbers with their ratings
    $barbers = \App\Models\Barber::where('is_approved', true)
        ->with(['user', 'ratings'])
        ->get()
        ->map(function ($barber) {
            $ratings = $barber->ratings->pluck('rating')->filter();
            $avgRating = $ratings->count() > 0 ? round($ratings->avg(), 1) : 0;
            $totalReviews = $ratings->count();

            return [
                'id' => $barber->id,
                'name' => $barber->user->name ?? 'Unknown',
                'bio' => $barber->bio ?? 'Professional barber dedicated to providing excellent service.',
                'years_of_experience' => $barber->years_of_experience ?? 5,
                'photo_url' => $barber->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($barber->user->name ?? 'User') . '&background=6B7280&color=fff&size=128',
                'avg_rating' => $avgRating,
                'total_reviews' => $totalReviews,
                'title' => $barber->years_of_experience >= 10 ? 'Master Barber' :
                          ($barber->years_of_experience >= 5 ? 'Senior Barber' : 'Professional Barber'),
            ];
        })
        ->sortByDesc('avg_rating')
        ->take(6) // Limit to top 6 barbers
        ->values();

    return Inertia::render('AboutPage', [
        'barbers' => $barbers
    ]);
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('ContactPage');
})->name('contact');

// Barber Registration Routes
Route::get('/register-barber', [BarberRegisterController::class, 'showForm'])
    ->name('barber.register.form');
Route::post('/register-barber', [BarberRegisterController::class, 'register'])
    ->name('barber.register.submit');

// Dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'barber') {
            return redirect()->route('barber.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    })->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin', 'verified'])->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/slots/today', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'todaySlots'])->name('admin.slots.today');
        Route::get('/admin/barbers/pending', [\App\Http\Controllers\Admin\BarberApprovalController::class, 'pending'])->name('admin.barbers.pending');
        Route::get('/admin/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/transformations', function () {
            return Inertia::render('Admin/Transformations');
        })->name('admin.transformations');
        Route::get('/admin/finances', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'index'])->name('admin.finances');
        Route::get('/admin/finances/monthly-revenue', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'getMonthlyRevenueData']);
        Route::get('/admin/finances/daily-revenue', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'getDailyRevenueData']);
        Route::get('/admin/finances/stats', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'getRevenueStats']);

        // PDF Export Routes
        Route::get('/admin/finances/export/full-report', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'exportFullReport']);
        Route::get('/admin/finances/export/revenue', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'exportRevenueReport']);
        Route::get('/admin/finances/export/monthly', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'exportMonthlyReport']);
        Route::get('/admin/finances/export/daily', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'exportDailyReport']);
        Route::get('/admin/bookings', function () {
            return Inertia::render('Admin/Bookings');
        })->name('admin.bookings');
        Route::get('/admin/users/{user}/completed-bookings-count', [\App\Http\Controllers\Admin\AdminUserController::class, 'completedBookingsCount']);
        Route::get('/admin/users/{user}/stats', [\App\Http\Controllers\Admin\AdminUserController::class, 'userStats']);
        Route::delete('/admin/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy']);
        Route::post('/admin/users/{user}/restore', [\App\Http\Controllers\Admin\AdminUserController::class, 'restore']);
        Route::get('/admin/users/growth', [\App\Http\Controllers\Admin\AdminUserController::class, 'userGrowth']);
        Route::get('/admin/transformations/pending', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'pendingTransformations']);
        Route::get('/admin/transformations/approved', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'approvedTransformations']);
        Route::get('/admin/barbers/{barber}/schedule', [\App\Http\Controllers\Admin\AdminUserController::class, 'barberSchedule']);
        Route::get('/admin/barbers', [\App\Http\Controllers\Admin\AdminUserController::class, 'allBarbers']);
        Route::get('/admin/barbers-manage', [\App\Http\Controllers\Admin\AdminUserController::class, 'allBarbersForManagement']);
        Route::get('/admin/barbers/{barber}/booking-stats', [\App\Http\Controllers\Admin\AdminUserController::class, 'barberBookingStats']);
        Route::get('/admin/barbers/{barber}/bookings', [\App\Http\Controllers\Admin\AdminUserController::class, 'barberBookings']);
        Route::post('/admin/barbers/{barber}/revoke-approval', [\App\Http\Controllers\Admin\AdminUserController::class, 'revokeApproval']);

        // Service Management Routes
        Route::get('/admin/services', [\App\Http\Controllers\Admin\AdminServiceController::class, 'index'])->name('admin.services.index');
        Route::post('/admin/services', [\App\Http\Controllers\Admin\AdminServiceController::class, 'store']);
        Route::get('/admin/services/{service}', [\App\Http\Controllers\Admin\AdminServiceController::class, 'show']);
        Route::put('/admin/services/{service}', [\App\Http\Controllers\Admin\AdminServiceController::class, 'update']);
        Route::delete('/admin/services/{service}', [\App\Http\Controllers\Admin\AdminServiceController::class, 'destroy']);
        Route::post('/admin/services/{service}/toggle', [\App\Http\Controllers\Admin\AdminServiceController::class, 'toggle']);
        Route::post('/admin/services/update-order', [\App\Http\Controllers\Admin\AdminServiceController::class, 'updateOrder']);
        Route::get('/admin/services-api/active', [\App\Http\Controllers\Admin\AdminServiceController::class, 'getActiveServices']);

        // Barber approval routes
        Route::get('/admin/barber-approvals', [BarberApprovalController::class, 'index'])
            ->name('admin.barber.approvals');
        Route::post('/admin/barber-approvals/{barber}/approve', [BarberApprovalController::class, 'approve'])
            ->name('admin.barber.approve');
        Route::post('/admin/barber-approvals/{barber}/decline', [BarberApprovalController::class, 'decline'])
            ->name('admin.barber.decline');

        // Barber manage routes
        Route::get('/admin/barbers/manage', function () {
            return Inertia::render('Admin/ManageBarbers');
        })->name('admin.barbers.manage');
        Route::post('/admin/barber/add', [\App\Http\Controllers\Admin\AdminUserController::class, 'addBarber'])
            ->name('admin.barber.add');

        // Transformation routes
        Route::post('/admin/transformations/{id}/approve', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'approveTransformation']);
        Route::post('/admin/transformations/{id}/reject', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'rejectTransformation']);
    });

    // Barber routes
    Route::middleware(['auth', 'role:barber', 'verified'])->group(function () {
        Route::get('/barber/dashboard', [BarberDashboardController::class, 'index'])
            ->name('barber.dashboard');
        Route::post('/barber/toggle-availability', [BarberDashboardController::class, 'toggleAvailability'])
            ->name('barber.availability.toggle');
        Route::post('/barber/update-schedule', [BarberDashboardController::class, 'updateSchedule'])
            ->name('barber.schedule.update');
        Route::get('/barber/appointments', [BarberDashboardController::class, 'appointments'])
            ->name('barber.appointments');
        Route::get('/barber/monthly-ratings', [BarberStatsController::class, 'monthlyRatings']);
        Route::get('/barber/todays-appointments-count', [BarberStatsController::class, 'todaysAppointmentsCount']);
        Route::get('/barber/todays-completed-appointments-count', [BarberStatsController::class, 'todaysCompletedAppointmentsCount']);
        Route::get('/barber/monthly-completed-appointments-count', [BarberStatsController::class, 'monthlyCompletedAppointmentsCount']);
        Route::get('/bookings', [BarberDashboardController::class, 'bookings'])->name('bookings');
        Route::get('/barber/bookings/export-pdf', [BarberDashboardController::class, 'exportBookingsPDF'])->name('barber.bookings.export-pdf');
    });

    // Customer routes
    Route::middleware(['role:customer', 'verified'])->group(function () {
        Route::get('/customer/dashboard', function () {
            return Inertia::render('Customer/Dashboard');
        })->name('customer.dashboard');

        // Booking routes
        Route::get('/booking', function () {
            return Inertia::render('Booking/Index');
        })->name('booking.index');
    });

    // These routes are moved to the authenticated section below

    // Transformation routes
    Route::get('/transformations', [TransformationController::class, 'index'])->name('transformations.index');
    Route::post('/transformations', [TransformationController::class, 'store'])->name('transformations.store');
    Route::delete('/transformations/{id}', [TransformationController::class, 'destroy'])->name('transformations.destroy');
    Route::post('/transformations/{id}/approve', [TransformationController::class, 'approve'])->middleware('role:admin')->name('transformations.approve');

    // --- AJAX/AUTHENTICATED ENDPOINTS ---
    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/read/all', [NotificationController::class, 'deleteAllRead'])->name('notifications.deleteAllRead');
        // Returns the next upcoming appointment for the logged-in user (barber or customer)
        Route::get('/next-appointment', [NotificationController::class, 'nextAppointment'])->name('notifications.next');
    });
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/bookings', [CustomerDashboardController::class, 'bookings'])->name('customer.bookings');
    Route::post('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');

    // Booking routes (accessible to all authenticated users)
    Route::get('/booking/create', function () {
        $services = \App\Models\Service::where('is_active', true)
                                      ->orderBy('sort_order')
                                      ->get(['id', 'name', 'slug', 'price', 'description']);

        return Inertia::render('Booking/Create', [
            'user' => auth()->user(),
            'services' => $services
        ]);
    })->name('booking.create');

    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    // Booking management routes
    Route::get('/booking/{booking}', function () {
        return Inertia::render('Booking/Show');
    })->name('booking.show');

    Route::get('/booking/{booking}/edit', function () {
        return Inertia::render('Booking/Edit');
    })->name('booking.edit');

    Route::put('/booking/{booking}', function () {
        // Handle booking update
    })->name('booking.update');

    Route::delete('/booking/{booking}', function () {
        // Handle booking deletion
    })->name('booking.destroy');

    // Add cancel booking route
    Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancelBooking'])->name('booking.cancel');

    // Add check-in route
    Route::post('/booking/{booking}/check-in', [BookingController::class, 'checkIn'])->name('booking.check-in');

    // Add reschedule booking route
    Route::post('/booking/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');

    // Payment routes
    Route::post('/bookings/{booking}/payment-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

    // Add available barbers and slots endpoints for booking
    Route::get('/api/available-barbers', [BookingController::class, 'getAvailableBarbers'])->name('api.available-barbers');
    Route::get('/api/available-slots', [BookingController::class, 'getAvailableSlots'])->name('api.available-slots');
    Route::get('/users/customers', [BookingController::class, 'getUsers'])->name('users.customers');

    // Barber routes
    Route::middleware(['auth', 'role:barber'])->prefix('barber')->name('barber.')->group(function () {
        Route::get('/dashboard', [BarberDashboardController::class, 'index'])->name('dashboard');
        Route::get('/appointments', [BarberDashboardController::class, 'appointments'])->name('appointments');
        Route::patch('/appointments/{booking}/status', [BarberDashboardController::class, 'updateAppointmentStatus'])->name('appointments.status');
        Route::post('/toggle-availability', [BarberDashboardController::class, 'toggleAvailability'])->name('availability.toggle');
        Route::post('/update-schedule', [BarberDashboardController::class, 'updateSchedule'])->name('schedule.update');
        Route::get('/monthly-ratings', [BarberStatsController::class, 'monthlyRatings']);
        Route::get('/todays-appointments-count', [BarberStatsController::class, 'todaysAppointmentsCount']);
        Route::get('/todays-completed-appointments-count', [BarberStatsController::class, 'todaysCompletedAppointmentsCount']);
        Route::get('/monthly-completed-appointments-count', [BarberStatsController::class, 'monthlyCompletedAppointmentsCount']);
        Route::get('/bookings', [BarberDashboardController::class, 'bookings'])->name('bookings');
        Route::get('/transformations', [\App\Http\Controllers\Barber\BarberDashboardController::class, 'transformations'])->name('transformations');
    });

    Route::get('/customer/stats/monthly-spending', [CustomerStatsController::class, 'monthlySpending'])->name('customer.stats.monthly-spending');
    Route::get('/customer/stats/barber-booking-distribution', [CustomerStatsController::class, 'barberBookingDistribution'])->name('customer.stats.barber-booking-distribution');
    Route::get('/customer/stats/favourite-barber', [CustomerStatsController::class, 'favouriteBarber'])->name('customer.stats.favourite-barber');
    Route::get('/customer/stats/next-appointment', [CustomerStatsController::class, 'nextAppointment'])->name('customer.stats.next-appointment');

    Route::get('/register/barber', [BarberRegistrationController::class, 'create'])->name('barber.register');
    Route::post('/register/barber', [BarberRegistrationController::class, 'store'])->name('barber.register.store');
});

// Route::get('/api/available-barbers', [BookingController::class, 'getAvailableBarbers']);
// Route::get('/api/available-slots', [BookingController::class, 'getAvailableSlots']);
// (Moved to routes/api.php)

// Public API routes (no authentication required)
Route::get('/api/transformations/approved', [\App\Http\Controllers\TransformationController::class, 'getApprovedTransformations'])->name('api.transformations.approved');

Route::get('/test-profile', function () {
    return view('test-profile');
});

// Test email verification route (remove this after testing)
Route::get('/test-email-verification', function() {
    if (auth()->check()) {
        $user = auth()->user();
        return response()->json([
            'user_id' => $user->id,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'is_verified' => $user->hasVerifiedEmail(),
        ]);
    }
    return response()->json(['error' => 'Not authenticated']);
})->middleware('auth');

require __DIR__.'/auth.php';
