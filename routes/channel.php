<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;

Broadcast::channel('orders.{id}', function ($customer, $id) {
    return $customer->id === Order::find($id)->customer_id;
});
