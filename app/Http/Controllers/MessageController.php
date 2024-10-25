<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendMessageJob;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        SendMessageJob::dispatch($message);

        return response()->json(['status' => 'Message sent!']);
    }
}