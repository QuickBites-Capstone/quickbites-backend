<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use App\Http\Services\ImageService;

class SearchController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::with('category')
            ->where('name', 'like', "%{$query}%")
            ->get()
            ->map(function ($product) {
                
                $totalQuantitySold = CartItem::where('product_id', $product->id)
                    ->whereHas('cart', function ($q) {
                        $q->whereNotNull('payment_id')
                          ->whereNotNull('schedule')
                          ->whereNotNull('total');
                    })
                    ->sum('quantity'); 

               
                $product->label = null;
                if ($totalQuantitySold > 20) { 
                    $product->label = 'Hot';
                } elseif ($totalQuantitySold > 5) {
                    $product->label = 'Best Seller';
                }

                $product->image_url = $this->imageService->getTemporaryImageUrl($product->image);

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
