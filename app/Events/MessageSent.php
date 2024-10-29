<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $icon;
    public $header;
    public $message;
    public $customerId;

    public function __construct($icon, $header, $message, $customerId)
    {
        $this->icon = $icon;
        $this->header = $header;
        $this->message = $message;
        $this->customerId = $customerId;
    }

    public function broadcastOn()
    {
        return [
            new Channel('chat.' . $this->customerId), // Public channel
        ];
    }
}
