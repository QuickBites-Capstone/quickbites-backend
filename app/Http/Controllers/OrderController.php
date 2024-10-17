<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with(['customer', 'items', 'paymentMethod', 'orderStatus'])->get();
    }
}
