<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use App\Enums\ProductStatus;
use App\Http\Requests\Product\SearchProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService, protected ProductRepository $productRepository, protected ProductService $productService)
    {
        $this->productService = $productService;
        $this->imageService = $imageService;
        $this->productRepository = $productRepository;
    }

    public function searchProduct(SearchProductRequest $request)
    {
        $searchTerm = $request->validated()['searchTerm'] ?? '';
        $products = $this->productService->searchProducts($searchTerm);

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found'], 404);
        }

        return ProductResource::collection($products);
    }

    public function index(Request $request)
    {
        $response = $this->productRepository->getPaginatedProducts();

        return response()->json($response, 200);
    }

    public function getProductsByCategory(Request $request)
    {
     
        $result = $this->productRepository->getProductsByCategoryRequest($request);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json($result, 200);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request);

        return new ProductResource($product);
    }

    public function update(StoreProductRequest $request, $id)
    {
        $product = $this->productService->updateProduct($request, $id);

        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => new ProductResource($product)
        ]);
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