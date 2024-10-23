<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
   
    public function store(Request $request, $customerId)
{
    $request->validate([
        'items' => 'sometimes|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);
    
    $customer = Customer::findOrFail($customerId);
    $cart = $customer->carts()->firstOrCreate([
        'customer_id' => $customer->id, 
        'payment_id' => $request->payment_id ?? null,
    ]);
    
    if ($request->has('items')) {
        foreach ($request->items as $item) {
            // Check if the cart item already exists
            $cartItem = $cart->cartItems()->where('product_id', $item['product_id'])->first();

            if ($cartItem) {
                // If the item exists, increment the quantity
                $cartItem->quantity += $item['quantity'];
                $cartItem->price = $this->getProductPrice($item['product_id']); // Ensure price is updated
                $cartItem->save();
            } else {
                // If it doesn't exist, create a new cart item
                $cart->cartItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $this->getProductPrice($item['product_id']),
                ]);
            }
        }
    }

    return response()->json($cart->load('cartItems'), 201);
}


    public function getCart($customerId)
    {
        $customer = Customer::with('carts.cartItems')->findOrFail($customerId);
        return response()->json($customer->carts); 
    }

    
    public function addCartItem(Request $request, $customerId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        
        $customer = Customer::findOrFail($customerId);
        $cart = $customer->carts()->firstOrCreate(['status' => 'active']); 
    
        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => $request->quantity,
                'price' => $this->getProductPrice($request->product_id),
            ]
        );
    
        return response()->json($cart->load('cartItems'), 201);
    }

    public function getCartItems($cartId)
    {
        $cart = Cart::with('cartItems')->findOrFail($cartId);
        return response()->json($cart->cartItems);
    }

    public function updateCartItem(Request $request, $cartId, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('cart_id', $cartId)->findOrFail($itemId);
        $cartItem->quantity = $request->quantity;

        $cartItem->price = $this->getProductPrice($cartItem->product_id); 
        $cartItem->save();

        return response()->json($cartItem);
    }

    public function removeCartItem($cartId, $itemId)
    {
        $cartItem = CartItem::where('cart_id', $cartId)->findOrFail($itemId);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed successfully']);
    }

    public function clearCart($cartId)
    {
        $cart = Cart::findOrFail($cartId);
        $cart->cartItems()->delete(); 

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    private function getProductPrice($productId)
    {
        
        return \App\Models\Product::findOrFail($productId)->price;
    }
}
