<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
    public function getProductsByCategory($categoryId){
        $category = Category::find($categoryId);

        if(!$category){
            return response()->json(['error' => 'Category not found!'], 404);
        }

        $products = $category->products;

        return response()->json($products);
    }
}