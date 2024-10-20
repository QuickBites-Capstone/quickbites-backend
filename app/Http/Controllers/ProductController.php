<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

            $productsWithImageUrl = $products->map(function ($product) {
                $product->image_url = $product->image ? Storage::url($product->image) : null;
                return $product;
            });

            return response()->json([
                'category' => ucfirst($categoryName),
                'products' => $productsWithImageUrl->isEmpty() ? [] : $productsWithImageUrl
            ], 200);
        } else {
            return response()->json([
                'error' => 'Invalid category. Please use one of the following: Meals, Snacks, Chips, Candies, Drinks, Supplies.'
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity;
        $product->category_id = $request->category_id;

        if ($request->hasFile('image')) {

            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        } else {
            $product->image = null;
        }

        $product->save();

        return response()->json(['message' => 'Product created successfully!'], 201);
    }
}
