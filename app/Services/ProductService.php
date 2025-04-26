<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\Product\StoreProductRequest;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected ProductRepository $productRepository, protected ImageService $imageService)
    {
        $this->productRepository = $productRepository;
        $this->imageService = $imageService;
    }

    public function searchProducts(string $searchTerm)
    {
        return $this->productRepository->searchByName($searchTerm);
    }
    
    public function createProduct(Request $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity;
        $product->category_id = $request->category_id;

        $product->status_id = ProductStatus::Available->value;

        $category = Category::find($product->category_id);
        $categoryName = strtolower($category->name);
        $folder = "products/{$categoryName}";

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

        return $product->fresh();
    }

    public function updateProduct(Request $request, $id): Product
    {
        $product = $this->productRepository->findById($id);

        $data = $request->only(['name', 'price', 'stock_quantity', 'category_id']);
        $this->productRepository->update($product, $data);

        $category = strtolower($product->category->name ?? 'uncategorized');
        $folder = "products/{$category}";

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $this->imageService->storeImage($request->file('image'), $folder);
            $this->productRepository->update($product, ['image' => $imagePath]);
        }

        if ($product->stock_quantity <= 0) {
            $this->productRepository->update($product, [
                'status_id' => ProductStatus::SoldOut->value,
            ]);
        }

        return $product->fresh();
    }

}
