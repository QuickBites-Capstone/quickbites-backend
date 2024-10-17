<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'balance',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'password',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
