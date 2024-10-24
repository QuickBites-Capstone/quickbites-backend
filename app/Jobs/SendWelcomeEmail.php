<?php

namespace App\Jobs;

use App\Mail\WelcomeCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function handle()
    {
        Mail::to($this->customer->email)->send(new WelcomeCustomer($this->customer));
    }
}