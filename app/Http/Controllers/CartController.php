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
        'total' => 'nullable|integer',
        'schedule' => 'nullable|string',
        'payment_id' => 'nullable|exists:payment_methods,id',
    ]);

    $customer = Customer::findOrFail($customerId);

    $lastCart = $customer->carts()->latest()->first();

    if ($lastCart && $lastCart->payment_id) {
        $cart = $customer->carts()->create([
            'customer_id' => $customer->id,
            'payment_id' => null, 
            'total' => null, 
            'schedule' => null,
        ]);
    } else {
       
        $cart = $lastCart ?: $customer->carts()->create([
            'customer_id' => $customer->id,
            'payment_id' => null,
            'total' => null,
            'schedule' => null,
        ]);
    }

 
    if ($request->has('total')) {
        $cart->total = $request->total; 
    }

    if ($request->has('schedule')) {
        $cart->schedule = $request->schedule; 
    }

    if ($request->has('payment_id')) {
        $cart->payment_id = $request->payment_id; 
    }


    if ($request->payment_id == 1 && $request->has('total')) {
        $total = $request->total;
        if ($customer->wallet_balance < $total) {
            return response()->json(['message' => 'Insufficient wallet balance.'], 400);
        }

        $customer->wallet_balance -= $total;
        $customer->save();
    }

    $cart->save();

    
    if ($request->has('items')) {
        foreach ($request->items as $item) {
            $cartItem = $cart->cartItems()->where('product_id', $item['product_id'])->first();

            if ($cartItem) {
            
                $cartItem->increment('quantity', $item['quantity']);
                $cartItem->price = $this->getProductPrice($item['product_id']);
                $cartItem->save();
            } else {
            
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
        
       
        $cart = $customer->carts()->first();

        if (!$cart) {
            return response()->json(['message' => 'No cart found for the customer'], 404);
        }

        return response()->json($cart->load('cartItems')); 
    }

    public function getCartItems($cartId)
    {
        $cart = Cart::with('cartItems')->findOrFail($cartId);
        return response()->json($cart->cartItems);
    }

    public function updateCartItem(Request $request, $cartId, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        
        $cartItem = CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId) 
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found for the given cart.'], 404);
        }

       
        $cartItem->quantity = $request->quantity;
        $cartItem->price = $this->getProductPrice($cartItem->product_id); 
        $cartItem->save();

        return response()->json($cartItem);
    }

    public function removeCartItem($cartId, $productId)
    {
        
        $cartItem = CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();

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
