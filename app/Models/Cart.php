<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_id');
    }
}
