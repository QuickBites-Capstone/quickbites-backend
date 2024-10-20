<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProductsByCategory(Request $request)
    {
        $categoryName = strtolower($request->query('category'));
        $validCategories = ['meals', 'snacks', 'chips', 'candies', 'drinks', 'supplies'];

        if (in_array($categoryName, $validCategories)) {
            $products = Product::whereHas('category', function ($query) use ($categoryName) {
                $query->whereRaw('LOWER(name) = ?', [$categoryName]);
            })->get();
            return response()->json([
                'category' => ucfirst($categoryName),
                'products' => $products->isEmpty() ? [] : $products
            ], 200);
        } else {
            return response()->json([
                'error' => 'Invalid category. Please use one of the following: Meals, Snacks, Chips, Candies, Drinks, Supplies.'
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $status_id = $validatedData['stock_quantity'] === 0 ? 2 : 1;

        $validatedData['status_id'] = $status_id;

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }
}
