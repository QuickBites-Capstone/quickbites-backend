<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::with('category')
            ->where('name', 'like', "%{$query}%")
            ->get()
            ->map(function ($product) {
                
                $inactiveCartCount = CartItem::where('product_id', $product->id)
                    ->whereHas('cart', function ($q) {
                        $q->whereNotNull('payment_id')
                          ->whereNotNull('schedule')
                          ->whereNotNull('total');
                    })
                    ->count();

                $product->label = null; 
                if ($inactiveCartCount > 10) {
                    $product->label = 'Hot';
                } elseif ($inactiveCartCount > 5) {
                    $product->label = 'Best Seller';
                }

                return $product;
            });


        $searchedProduct = $products->firstWhere('name', 'like', "%{$query}%");
        if ($searchedProduct) {
    
            $products = $products->filter(function ($product) use ($searchedProduct) {
                return $product->id !== $searchedProduct->id;
            })->prepend($searchedProduct); 
        }

        return response()->json($products);
    }
}

