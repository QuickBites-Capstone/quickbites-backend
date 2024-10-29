<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Services\ImageService;
use App\Models\Product;

class CategoryController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
    public function getProductsByCategory($categoryId)
{
    $category = Category::find($categoryId);
    if (!$category) {
        return response()->json(['error' => 'Category not found!'], 404);
    }

    $perPage = 5;
    $products = Product::where('category_id', $categoryId) 
        ->orderBy('id', 'asc')
        ->paginate($perPage);

    
    $products->getCollection()->transform(function ($product) {
        $product->image_url = $this->imageService->getTemporaryImageUrl($product->image);
        return $product;
    });

    return response()->json($products);
}

}
