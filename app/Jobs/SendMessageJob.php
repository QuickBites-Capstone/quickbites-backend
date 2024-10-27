<?php

namespace App\Jobs;

use App\Events\MessageSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $icon;
    protected $header;
    protected $message;
    protected $customerId;

    public function __construct($icon, $header, $message, $customerId)
    {
        $this->icon = $icon;
        $this->header = $header;
        $this->message = $message;
        $this->customerId = $customerId;
    }

    public function handle()
    {
        event(new MessageSent($this->icon, $this->header, $this->message, $this->customerId));
    }
}
