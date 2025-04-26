<?php

namespace App\Repositories;

use App\Enums\ProductStatus;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Services\ImageService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductRepository 
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function getPaginatedProducts($perPage = 10): array
    {
        $products = Product::with('category')
            ->orderBy('id', 'asc')
            ->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->image_url = $this->imageService->getTemporaryImageUrl($product->image);
            return $product;
        });

        return [
            'products' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ],
        ];
    }

    public function getProductsByCategoryRequest(Request $request): array
    {
        $categoryName = strtolower($request->query('category'));
        $perPage = (int) $request->query('per_page', 5);

        $validCategories = ['meals', 'snacks', 'chips', 'candies', 'drinks', 'supplies'];

        if (!in_array($categoryName, $validCategories)) {
            return [
                'error' => 'Invalid category. Please use one of the following: Meals, Snacks, Chips, Candies, Drinks, Supplies.',
            ];
        }

        $products = Product::whereHas('category', function ($query) use ($categoryName) {
            $query->whereRaw('LOWER(name) = ?', [$categoryName]);
        })->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->image_url = app(ImageService::class)->getTemporaryImageUrl($product->image);
            return $product;
        });

        return [
            'category' => ucfirst($categoryName),
            'products' => $products->isEmpty() ? [] : $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ];
    }

    public function searchByName(string $term)
    {
        return Product::when($term, function ($query) use ($term) {
            return $query->where('name', 'like', '%' . $term . '%');
        })->get();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function findById(int $id): Product
    {
        return Product::findOrFail($id);
    }
}
