<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SimpleTestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct()
    {
        $this->message = 'Simple test message';
        Log::info('SimpleTestEvent constructed');
    }

    public function broadcastOn(): array
    {
        Log::info('SimpleTestEvent broadcasting on test-channel');
        return [new Channel('test-channel')];
    }

    public function broadcastAs(): string
    {
        return 'simple.test';
    }
}
