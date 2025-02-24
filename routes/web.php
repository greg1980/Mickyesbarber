<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransformationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
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
});

Route::middleware(['auth'])->group(function () {
    Route::get('/transformations', [TransformationController::class, 'index'])->name('transformations.index');
    Route::post('/transformations', [TransformationController::class, 'store'])->name('transformations.store');
    Route::delete('/transformations/{transformation}', [TransformationController::class, 'destroy'])->name('transformations.destroy');
    Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/api/available-slots', [BookingController::class, 'getAvailableSlots'])->name('bookings.slots');
    Route::get('/api/user-bookings', [BookingController::class, 'getUserBookings']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'cancel']);
    Route::post('/bookings/{booking}/payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);
    Route::get('/barbers/{barber}/transformations', [TransformationController::class, 'getBarberTransformations']);
    Route::get('/api/available-barbers', [BookingController::class, 'getAvailableBarbers']);
    Route::get('/bookings/{booking}/payment', [BookingController::class, 'showPayment'])->name('booking.payment');
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'processPayment'])->name('booking.process-payment');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    Route::get('/admin/booking-stats', [BookingController::class, 'getAdminStats']);
});

// Auth routes will be available at /login, /register, etc.
require __DIR__.'/auth.php';
