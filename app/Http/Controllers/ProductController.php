<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use App\Enums\ProductStatus;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function searchProduct(Request $request)
    {

        $validated = $request->validate([
            'searchTerm' => 'nullable|string|max:255',
        ]);

        $searchTerm = $validated['searchTerm'] ?? '';

        $products = Product::when($searchTerm, function ($query) use ($searchTerm) {
            return $query->where('name', 'like', '%' . $searchTerm . '%');
        })
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found'], 404);
        }

        return response()->json($products);
    }






    public function index(Request $request)
    {
        $perPage = 10;

        $products = Product::with('category')
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        $productsWithImageUrl = $products->getCollection()->map(function ($product) {
            $product->image_url = $this->imageService->getTemporaryImageUrl($product->image);
            return $product;
        });

        return response()->json([
            'data' => $productsWithImageUrl,
            'pagination' => [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ]
        ], 200);
    }

    public function getProductsByCategory(Request $request)
    {
        $categoryName = strtolower($request->query('category'));
        $validCategories = ['meals', 'snacks', 'chips', 'candies', 'drinks', 'supplies'];

        if (in_array($categoryName, $validCategories)) {
            $perPage = $request->query('per_page', 5);
            $products = Product::whereHas('category', function ($query) use ($categoryName) {
                $query->whereRaw('LOWER(name) = ?', [$categoryName]);
            })->paginate($perPage);

            $productsWithImageUrl = $products->map(function ($product) {
                $product->image_url = $this->imageService->getTemporaryImageUrl($product->image);
                return $product;
            });

            return response()->json([
                'category' => ucfirst($categoryName),
                'products' => $productsWithImageUrl->isEmpty() ? [] : $productsWithImageUrl,
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
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

        // Set the status_id to 1 (Available) by default
        $product->status_id = ProductStatus::Available->value;

        $category = strtolower($product->category->name);
        $folder = "products/{$category}";

        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->storeImage($request->file('image'), $folder);
            $product->image = $imagePath;
        } else {
            $product->image = null;
        }

        $product->save();

        if ($product->stock_quantity <= 0) {
            $product->status_id = ProductStatus::SoldOut->value;
            $product->save();
        }

        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product->fresh(),
            'image_url' => $product->image ? Storage::url($product->image) : null
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'stock_quantity' => 'sometimes|required|integer',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image',
        ]);

        $product = Product::findOrFail($id);
        $product->fill($request->only(['name', 'price', 'stock_quantity', 'category_id']));

        $category = strtolower($product->category->name);
        $folder = "products/{$category}";

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $this->imageService->storeImage($request->file('image'), $folder);
            $product->image = $imagePath;
        }

        $product->save();

        if ($product->stock_quantity <= 0) {
            $product->status_id = ProductStatus::SoldOut->value;
            $product->save();
        }

        return response()->json(['message' => 'Product updated successfully!'], 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            $this->imageService->deleteImage($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully!'], 200);
    }
}