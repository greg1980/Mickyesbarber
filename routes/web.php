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

// Public routes
Route::get('/', function () {
    return Inertia::render('HomePage');
})->name('home');

Route::get('/services', function () {
    return Inertia::render('ServicePage');
})->name('services');

Route::get('/about', function () {
    return Inertia::render('AboutPage');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('ContactPage');
})->name('contact');

// Barber Registration Routes
Route::get('/register-barber', [BarberRegisterController::class, 'showForm'])
    ->name('barber.register.form');
Route::post('/register-barber', [BarberRegisterController::class, 'register'])
    ->name('barber.register');

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
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');

        // Barber approval routes
        Route::get('/admin/barber-approvals', [BarberApprovalController::class, 'index'])
            ->name('admin.barber.approvals');
        Route::post('/admin/barber-approvals/{barber}/approve', [BarberApprovalController::class, 'approve'])
            ->name('admin.barber.approve');
    });

    // Barber routes
    Route::middleware(['auth', 'role:barber'])->group(function () {
        Route::get('/barber/dashboard', [BarberDashboardController::class, 'index'])
            ->name('barber.dashboard');
        Route::post('/barber/toggle-availability', [BarberDashboardController::class, 'toggleAvailability'])
            ->name('barber.availability.toggle');
        Route::post('/barber/update-schedule', [BarberDashboardController::class, 'updateSchedule'])
            ->name('barber.schedule.update');
        Route::get('/barber/appointments', [BarberDashboardController::class, 'appointments'])
            ->name('barber.appointments');
    });

    // Customer routes
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/customer/dashboard', function () {
            return Inertia::render('Customer/Dashboard');
        })->name('customer.dashboard');

        // Booking routes
        Route::get('/booking', function () {
            return Inertia::render('Booking/Index');
        })->name('booking.index');
    });

    // Booking routes
    Route::get('/booking/create', function () {
        return Inertia::render('Booking/Create');
    })->name('booking.create');

    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

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

    // Add reschedule booking route
    Route::post('/booking/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');

    // Payment routes
    Route::post('/bookings/{booking}/payment-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

    // Transformation routes
    Route::get('/transformations', [TransformationController::class, 'index'])->name('transformations.index');
    Route::post('/transformations', [TransformationController::class, 'store'])->name('transformations.store');
    Route::delete('/transformations/{id}', [TransformationController::class, 'destroy'])->name('transformations.destroy');
    Route::post('/transformations/{id}/approve', [TransformationController::class, 'approve'])->middleware('role:admin')->name('transformations.approve');
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
});

// Route::get('/api/available-barbers', [BookingController::class, 'getAvailableBarbers']);
// Route::get('/api/available-slots', [BookingController::class, 'getAvailableSlots']);
// (Moved to routes/api.php)

Route::get('/test-profile', function () {
    return view('test-profile');
});

require __DIR__.'/auth.php';
