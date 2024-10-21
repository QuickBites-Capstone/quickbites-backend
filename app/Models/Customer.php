<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'profile_picture',
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