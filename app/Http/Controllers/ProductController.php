<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private $client;
    private $driveService;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->addScope(Drive::DRIVE_FILE);

        // Set the access token if available
        if (session('access_token')) {
            $this->client->setAccessToken(session('access_token'));
        }

        // Create Drive service
        $this->driveService = new Drive($this->client);
    }

    private function refreshAccessTokenIfNeeded()
    {
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                session(['access_token' => $this->client->getAccessToken()['access_token']]);
            } else {
                throw new \Exception('Access token is expired and no refresh token is available.');
            }
        }
    }

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
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max size as needed
            'category_id' => 'required|exists:categories,id',
        ]);

        // Handle the image upload and product creation
        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity;
        $product->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            // Example of uploading to storage
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path; // Save the path to the database
        }

        $product->save();

        return response()->json(['success' => true]);
    }


    public function getImage($id)
    {
        $product = Product::findOrFail($id);
        $imageId = $product->image;

        if (!$imageId) {
            return response()->json(['error' => 'Image not found.'], 404);
        }

        try {
            $this->refreshAccessTokenIfNeeded(); // Refresh token if needed

            // Get the image file from Google Drive
            $response = $this->driveService->files->get($imageId, ['alt' => 'media']);
            $contentType = $response->getHeader('Content-Type');

            return response($response->getBody()->getContents(), 200)
                ->header('Content-Type', $contentType); // Dynamic content type
        } catch (\Exception $e) {
            Log::error('Error fetching image from Google Drive: ' . $e->getMessage());
            return response()->json(['error' => 'Image retrieval failed.'], 500);
        }
    }
}
