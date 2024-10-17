<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'price',
        'stock_quantity',
        'status_id',
        'category_id',
        'created_at',
        'updated_at'
    ];

    // A product belongs to one category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // A product has one status (e.g., available, out of stock)
    public function status()
    {
        return $this->belongsTo(ProductStatus::class, 'status_id');
    }

    // A product can be part of many cart items
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
