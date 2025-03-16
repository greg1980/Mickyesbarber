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
use App\Http\Controllers\BarberDashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\BarberMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Broadcast;
use App\Events\TestEvent;
use Illuminate\Support\Facades\Log;

Broadcast::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])
        ->name('dashboard.data');

    // Booking Routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}/payment', [BookingController::class, 'showPayment'])->name('payment');
        Route::post('/{booking}/payment', [BookingController::class, 'processPayment'])->name('process-payment');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancelBooking'])->name('cancel');
        Route::post('/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('reschedule');
        Route::get('/slots', [BookingController::class, 'getAvailableSlots'])->name('slots');
        Route::get('/user', [BookingController::class, 'getUserBookings'])->name('user');
        Route::post('/{booking}/status', [BookingController::class, 'updateStatus'])->name('status');
        Route::get('/barbers', [BookingController::class, 'getAvailableBarbers'])->name('barbers');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Barber routes
    Route::get('/barber/register', [BarberController::class, 'showRegistrationForm'])->name('barber.register');
    Route::post('/barber/register', [BarberController::class, 'register'])->name('barber.store');
    Route::post('/barbers/{barber}/toggle-availability', [BarberController::class, 'toggleAvailability'])->name('barber.toggle-availability');
    Route::get('/barbers/{barber}/availability', [BarberController::class, 'getAvailability'])->name('barber.availability');

    // Barber Dashboard routes (protected by barber middleware)
    Route::middleware(['barber'])->prefix('barber')->group(function () {
        Route::get('/dashboard', [BarberDashboardController::class, 'index'])->name('barber.dashboard');
        Route::get('/dashboard-data', [BarberDashboardController::class, 'getDashboardData'])->name('barber.dashboard-data');
        Route::get('/calendar-data', [BarberDashboardController::class, 'getCalendarData'])->name('barber.calendar-data');
        Route::post('/bookings/{booking}/complete', [BarberDashboardController::class, 'completeAppointment'])->name('barber.complete-appointment');
        Route::post('/bookings/{booking}/remind', [BarberDashboardController::class, 'sendReminder'])->name('barber.send-reminder');
    });

    // Message routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/conversations', [MessageController::class, 'getConversations'])->name('messages.conversations');
    Route::get('/messages/{otherUser}', [MessageController::class, 'getMessages'])->name('messages.get');
    Route::post('/messages/{recipient}', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');

    // Admin routes (protected by admin middleware)
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.toggle-user-status');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.delete-user');
        Route::post('/users/{user}/assign-barber', [AdminController::class, 'assignBarber'])->name('admin.assign-barber');
        Route::delete('/users/{user}/remove-barber', [AdminController::class, 'removeBarber'])->name('admin.remove-barber');
        Route::get('/bookings', [AdminController::class, 'upcomingBookings'])->name('admin.bookings');
    });

    // Transformation routes
    Route::get('/transformations', [TransformationController::class, 'index'])->name('transformations.index');
    Route::post('/transformations', [TransformationController::class, 'store'])->name('transformations.store');
    Route::delete('/transformations/{transformation}', [TransformationController::class, 'destroy'])->name('transformations.destroy');
    Route::get('/barber/{barberId}/transformations', [TransformationController::class, 'getBarberTransformations'])->name('transformations.barber');
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
    Route::post('/bookings/{id}/reschedule', [BookingController::class, 'reschedule'])->name('booking.reschedule');

    // Keep the availability routes for barbers
    Route::post('/barbers/{barber}/toggle-availability', [BarberController::class, 'toggleAvailability'])->name('barber.toggle-availability');
    Route::get('/barbers/{barber}/availability', [BarberController::class, 'getAvailability'])->name('barber.availability');

    // Payment Routes
    Route::post('/payment/create-intent/{booking}', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
    Route::post('/payment/process/{booking}', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');
});

// Add this test route
Route::get('/test-pusher', function () {
    $socketId = request()->header('X-Socket-ID');

    Log::info('Starting test broadcast', [
        'broadcast_driver' => config('broadcasting.default'),
        'connection_config' => config('broadcasting.connections.' . config('broadcasting.default')),
        'socket_id' => $socketId,
        'headers' => request()->headers->all()
    ]);

    try {
        $event = new TestEvent();
        broadcast($event);

        return response()->json([
            'success' => true,
            'message' => 'Test event broadcasted',
            'debug' => [
                'time' => now()->toDateTimeString(),
                'socket_id' => $socketId,
                'broadcast_driver' => config('broadcasting.default'),
                'event_class' => TestEvent::class,
                'broadcast_config' => config('broadcasting.connections.' . config('broadcasting.default')),
                'headers' => request()->headers->all()
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Error broadcasting test event', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error broadcasting test event',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Add simple test route
Route::get('/simple-test', function () {
    try {
        Log::info('Starting simple test broadcast', [
            'socket_id' => request()->header('X-Socket-ID'),
            'broadcast_driver' => config('broadcasting.default'),
            'connection' => config('broadcasting.connections.' . config('broadcasting.default'))
        ]);

        $event = new \App\Events\SimpleTestEvent();
        broadcast($event);

        return response()->json([
            'status' => 'success',
            'message' => 'Simple test event dispatched',
            'debug' => [
                'time' => now()->toDateTimeString(),
                'socket_id' => request()->header('X-Socket-ID'),
                'broadcast_driver' => config('broadcasting.default')
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Error in simple test broadcast', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

// Auth routes will be available at /login, /register, etc.
require __DIR__.'/auth.php';
