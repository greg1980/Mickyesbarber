<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $time;
    public $debug;

    public function __construct()
    {
        Log::info('TestEvent constructed');
        $this->message = 'This is a test message';
        $this->time = now()->toDateTimeString();
        $this->debug = [
            'socket_id' => request()->header('X-Socket-ID'),
            'connection' => config('broadcasting.default'),
            'event_name' => 'test-event',
            'channel' => 'test-channel'
        ];
    }

    public function broadcastOn(): Channel
    {
        $channel = new Channel('test-channel');
        Log::info('TestEvent broadcasting on channel', [
            'channel' => $channel->name,
            'socket_id' => request()->header('X-Socket-ID'),
            'connection' => config('broadcasting.default')
        ]);
        return $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'test-event';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        $data = [
            'message' => $this->message,
            'time' => $this->time,
            'debug' => array_merge($this->debug, [
                'broadcast_time' => now()->toDateTimeString(),
                'socket_id' => request()->header('X-Socket-ID')
            ])
        ];
        Log::info('TestEvent broadcasting with data', $data);
        return $data;
    }
}
