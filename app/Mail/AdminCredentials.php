<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminCredentials extends Mailable
{
    use Queueable, SerializesModels;
    public $admin;
    public $password;
    public function __construct($admin, $password)
    {
        $this->admin = $admin;
        $this->password = $password;
    }
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('AdminCMS Account Credentials')
            ->view('emails.admin-credentials')
            ->with([
                'admin' => $this->admin,
                'password' => $this->password,
            ]);
    }
}
