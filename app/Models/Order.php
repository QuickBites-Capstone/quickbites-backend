<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Define the table if the table name is not the plural of the model name
    protected $table = 'orders';

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'cart_id',
        'order_status_id',
        'reason_id',
        'created_at',
        'updated_at'
    ];

    // Define relationships

    // An order belongs to a cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // An order has one status (e.g., pending, accepted, canceled)
    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    // An order may have a reason (e.g., cancellation reason)
    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    // An order has many cart items through the cart relationship
    public function items()
    {
        return $this->hasManyThrough(CartItem::class, Cart::class, 'id', 'cart_id', 'cart_id', 'id');
    }

    // An order has one customer (through the cart)
    public function customer()
    {
        return $this->hasOneThrough(Customer::class, Cart::class, 'id', 'id', 'cart_id', 'customer_id');
    }

    // An order uses one payment method (through the cart)
    public function paymentMethod()
    {
        return $this->hasOneThrough(PaymentMethod::class, Cart::class, 'id', 'id', 'cart_id', 'payment_id');
    }
}
