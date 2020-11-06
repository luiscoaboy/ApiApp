<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Test implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $Mensaje;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($Mensaje)
    {
        $this->Mensaje = $Mensaje;
    }

    public function broadcastOn()
    {
        return new Channel('test');
    }
}
