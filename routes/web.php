<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransformationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fix barber registration routes
    Route::get('/barber/register', [BarberController::class, 'showRegistrationForm'])->name('barber.register');
    Route::post('/barber/register', [BarberController::class, 'register'])->name('barber.register.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/transformations', [TransformationController::class, 'index'])->name('transformations.index');
    Route::post('/transformations', [TransformationController::class, 'store'])->name('transformations.store');
    Route::delete('/transformations/{transformation}', [TransformationController::class, 'destroy'])->name('transformations.destroy');
    Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/api/available-slots', [BookingController::class, 'getAvailableSlots'])->name('bookings.slots');
    Route::get('/api/user-bookings', [BookingController::class, 'getUserBookings']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'cancelBooking']);
    Route::post('/bookings/{booking}/payment-intent', [PaymentController::class, 'createPaymentIntent'])->name('booking.payment-intent');
    Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook'])->name('stripe.webhook');
    Route::get('/barbers/{barber}/transformations', [TransformationController::class, 'getBarberTransformations']);
    Route::get('/api/available-barbers', [BookingController::class, 'getAvailableBarbers']);
    Route::get('/bookings/{booking}/payment', [BookingController::class, 'showPayment'])->name('booking.payment');
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
    Route::post('/bookings/{id}/reschedule', [BookingController::class, 'rescheduleBooking'])->name('booking.reschedule');

    // Keep the availability routes for barbers
    Route::post('/barbers/{barber}/toggle-availability', [BarberController::class, 'toggleAvailability'])->name('barber.toggle-availability');
    Route::get('/barbers/{barber}/availability', [BarberController::class, 'getAvailability'])->name('barber.availability');
});

// Admin routes
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/admin/users/{user}/assign-barber', [AdminController::class, 'assignBarber'])->name('admin.users.assign-barber');
    Route::delete('/admin/users/{user}/remove-barber', [AdminController::class, 'removeBarber'])->name('admin.users.remove-barber');

    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    Route::get('/admin/booking-stats', [BookingController::class, 'getAdminStats']);
});

// Auth routes will be available at /login, /register, etc.
require __DIR__.'/auth.php';
