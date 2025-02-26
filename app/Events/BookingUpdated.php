<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class BookingUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $bookedSlots;
    public $action;

    public function __construct(Booking $booking, array $bookedSlots, string $action = 'updated')
    {
        $this->booking = $booking;
        $this->bookedSlots = $bookedSlots;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        return [
            new Channel('bookings'),
            new Channel('user.' . $this->booking->user_id),
            new Channel('barber.' . $this->booking->barber_id)
        ];
    }

    public function broadcastAs()
    {
        return 'booking.' . $this->action;
    }

    public function broadcastWith()
    {
        return [
            'booking' => [
                'id' => $this->booking->id,
                'date' => $this->booking->booking_date,
                'time' => Carbon::parse($this->booking->booking_time)->format('g:i A'),
                'status' => $this->booking->status,
                'barber_id' => $this->booking->barber_id,
                'user_id' => $this->booking->user_id
            ],
            'booked_slots' => $this->bookedSlots,
            'action' => $this->action
        ];
    }
}
