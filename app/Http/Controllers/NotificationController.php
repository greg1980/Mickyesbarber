<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get the next upcoming appointment for the logged-in user (barber or customer).
     * Returns null if none found.
     */
    public function nextAppointment(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

        if ($user->role === 'barber' && $user->barber) {
            $appointment = Booking::with(['user', 'service'])
                ->where('barber_id', $user->barber->id)
                ->where('booking_date', '>=', $now->toDateString())
                ->where(function($q) use ($now) {
                    $q->where('booking_date', '>', $now->toDateString())
                      ->orWhere(function($q2) use ($now) {
                          $q2->where('booking_date', $now->toDateString())
                             ->where('booking_time', '>=', $now->toTimeString());
                      });
                })
                ->whereNotIn('status', ['cancelled'])
                ->orderBy('booking_date')
                ->orderBy('booking_time')
                ->first();

            // Create notification for upcoming appointment if found
            if ($appointment) {
                $this->createAppointmentNotification($user, $appointment);
            }
        } elseif ($user->role === 'customer') {
            $appointment = Booking::with(['barber.user', 'service'])
                ->where('user_id', $user->id)
                ->where('booking_date', '>=', $now->toDateString())
                ->where(function($q) use ($now) {
                    $q->where('booking_date', '>', $now->toDateString())
                      ->orWhere(function($q2) use ($now) {
                          $q2->where('booking_date', $now->toDateString())
                             ->where('booking_time', '>=', $now->toTimeString());
                      });
                })
                ->whereNotIn('status', ['cancelled'])
                ->orderBy('booking_date')
                ->orderBy('booking_time')
                ->first();

            // Create notification for upcoming appointment if found
            if ($appointment) {
                $this->createAppointmentNotification($user, $appointment);
            }
        } else {
            $appointment = null;
        }

        return response()->json([
            'appointment' => $appointment
        ]);
    }

    /**
     * Get all notifications for the logged-in user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead(Request $request)
    {
        $user = Auth::user();
        $deletedCount = Notification::where('user_id', $user->id)
            ->where('is_read', true)
            ->delete();

        return response()->json([
            'success' => true,
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Create a notification for an upcoming appointment.
     */
    private function createAppointmentNotification($user, $appointment)
    {
        $title = 'Upcoming Appointment';
        $message = sprintf(
            'You have an appointment on %s at %s',
            Carbon::parse($appointment->booking_date)->format('F j, Y'),
            Carbon::parse($appointment->booking_time)->format('g:i A')
        );

        // Check if a similar notification already exists
        $existingNotification = Notification::where('user_id', $user->id)
            ->where('type', 'appointment')
            ->where('data->booking_id', $appointment->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if (!$existingNotification) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'appointment',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'booking_id' => $appointment->id,
                    'booking_date' => $appointment->booking_date,
                    'booking_time' => $appointment->booking_time,
                ],
            ]);
        }
    }
}
