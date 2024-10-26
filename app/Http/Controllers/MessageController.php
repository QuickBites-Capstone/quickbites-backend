<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendMessageJob;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validate request inputs
        $request->validate([
            'icon' => 'required|string',
            'header' => 'required|string',
            'message' => 'required|string',
            'customer_id' => 'required|integer|exists:customers,id', // Assuming you have a customers table
        ]);

        // Retrieve inputs
        $icon = $request->input('icon');
        $header = $request->input('header');
        $message = $request->input('message');
        $customerId = $request->input('customer_id');

        // Dispatch the message job with customer ID
        SendMessageJob::dispatch($icon, $header, $message, $customerId);

        return response()->json(['status' => 'Message sent!']);
    }
}
