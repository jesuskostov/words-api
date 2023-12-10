<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;


    public function __construct()
    {
        //
    }

    public function broadcastOn()
    {
        return new Channel('game-created');
    }
}
