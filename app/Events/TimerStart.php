<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TimerStart implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $duration;

    public function __construct($duration) {
        $this->duration = $duration;
    }

    public function broadcastOn() {
        return new Channel('game-timer');
    }
}
