<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Jobs\BroadcastNewOrder;
use App\Events\NewOrderCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Enums\ProductStatus;

class CartController extends Controller
{
    public function store(Request $request, $customerId)
    {
        $this->validateRequest($request);

        $customer = Customer::findOrFail($customerId);
        $cart = $this->getOrCreateCart($customer);

        $this->updateCartAttributes($cart, $request);

        if ($this->processWalletPayment($request, $customer, $cart) === false) {
            return response()->json(['message' => 'Insufficient wallet balance.'], 400);
        }

        try {
            $this->updateCartItems($cart, $request->input('items', []));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        if ($this->shouldCreateOrder($cart)) {
            $order = $this->createOrder($cart, $request->input('reason_id'));
            broadcast(new NewOrderCreated($order))->toOthers();

            return response()->json([
                'cart' => $cart->load('cartItems'),
                'order' => $order,
            ]);
        }

        return response()->json([
            'cart' => $cart->load('cartItems'),
            'message' => 'Cart updated. Please finalize the cart to create an order.',
        ]);
    }


    private function validateRequest(Request $request)
    {
        $request->validate([
            'items' => 'sometimes|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'nullable|integer',
            'schedule' => 'nullable|string',
            'payment_id' => 'nullable|exists:payment_methods,id',
        ]);
    }

    private function getOrCreateCart(Customer $customer)
    {
        $lastCart = $customer->carts()->latest()->first();

        if (!$lastCart || $lastCart->payment_id) {
            return $customer->carts()->create([
                'customer_id' => $customer->id,
                'payment_id' => null,
                'total' => null,
                'schedule' => null,
            ]);
        }

        return $lastCart;
    }

    private function updateCartAttributes($cart, Request $request)
    {
        if ($request->has('total')) {
            $cart->total = $request->total;
        }

        if ($request->has('schedule')) {
            $cart->schedule = $request->schedule;
        }

        if ($request->has('payment_id')) {
            $cart->payment_id = $request->payment_id;
        }

        $cart->save();
    }

    private function processWalletPayment(Request $request, Customer $customer, $cart)
    {
        if ($request->payment_id == 1 && $request->has('total')) {
            $total = (float) $request->total;
            $balance = (float) $customer->balance;

            Log::info('Processing wallet payment', [
                'customer_id' => $customer->id,
                'balance' => $balance,
                'total' => $total,
            ]);

            if ($balance < $total) {
                return false;
            }

            $customer->balance -= $total;
            $customer->save();
        }

        return true;
    }

    private function updateCartItems($cart, array $items)
    {
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);


            if ($item['quantity'] > $product->stock_quantity) {

                throw new \Exception("Insufficient stock for product ID {$item['product_id']}. Available: {$product->stock_quantity}");
            }

            $cartItem = $cart->cartItems()->where('product_id', $item['product_id'])->first();

            if ($cartItem) {
                if (!$cart->payment_id) {

                    $cartItem->increment('quantity', $item['quantity']);
                    $cartItem->price = $this->getProductPrice($item['product_id']);
                    $cartItem->save();
                }
            } else {

                $cart->cartItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $this->getProductPrice($item['product_id']),
                ]);
            }
        }
    }



    private function shouldCreateOrder($cart)
    {
        return $cart->total !== null && $cart->payment_id !== null && $cart->schedule !== null;
    }

    private function createOrder($cart, $reasonId)
    {

        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(5),
            'cart_id' => $cart->id,
            'order_status_id' => 1,
            'reason_id' => $reasonId,
        ]);

        foreach ($cart->cartItems as $cartItem) {
            $product = Product::findOrFail($cartItem->product_id);
            $product->stock_quantity -= $cartItem->quantity;

            if ($product->stock_quantity <= 0) {
                $product->status_id = ProductStatus::SoldOut->value;
            }
            $product->save();
        }

        return $order;
    }


    public function getCart($customerId)
    {
        $customer = Customer::with('carts.cartItems')->findOrFail($customerId);

        $cart = $customer->carts()
            ->whereNull('total')
            ->whereNull('schedule')
            ->whereNull('payment_id')
            ->first();

        if (!$cart) {
            return response()->json(['message' => 'No active cart found for the customer'], 404);
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

        $cart = Cart::with('cartItems')->findOrFail($cartId);

        if ($cart->total === null && $cart->schedule === null && $cart->payment_id === null) {
            Log::info('Clearing cart', ['cart' => $cart]);

            $cart->cartItems()->delete();
            $cart->delete();

            return response()->json(['message' => 'Cart cleared and removed successfully']);
        } else {

            return response()->json(['message' => 'Cannot clear an inactive cart.'], 400);
        }
    }

    private function getProductPrice($productId)
    {
        return \App\Models\Product::findOrFail($productId)->price;
    }
}
