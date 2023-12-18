<?php

namespace App\Jobs;

use App\Events\UserCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BroadcastUserCreated implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;    
    }

    public function handle()
    {
        broadcast(new UserCreated($this->user))->toOthers();
    }
}
