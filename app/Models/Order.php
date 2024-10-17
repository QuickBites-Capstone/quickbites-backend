<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'cart_id',
        'order_status_id',
        'reason_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_id');
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }
}
