<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with([
            'cart.customer',
            'cart.cartItems.product',
            'cart.paymentMethod',
            'orderStatus',
            'reason'
        ])->get()->map(function ($order) {
            return $this->formatOrder($order);
        });
    }

    public function todayOrders()
    {
        $today = Carbon::today();

        return Order::with([
            'cart.customer',
            'cart.cartItems.product',
            'cart.paymentMethod',
            'orderStatus',
            'reason'
        ])
            ->whereDate('created_at', $today)
            ->get()
            ->map(function ($order) {
                return $this->formatOrder($order);
            });
    }

    private function formatOrder($order)
    {
        return [
            'id' => $order->id,
            'cart_id' => $order->cart_id,
            'order_status_id' => $order->order_status_id,
            'reason_id' => $order->reason_id,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'customer' => $this->formatCustomer($order->cart),
            'cart' => [
                'payment_method' => $this->formatPaymentMethod($order->cart->paymentMethod),
                'total' => $order->cart ? $order->cart->total : null,
                'cart_items' => $this->formatCartItems($order->cart)
            ],
            'order_status' => $this->formatOrderStatus($order->orderStatus),
            'reason' => $this->formatReason($order->reason)
        ];
    }

    private function formatCustomer($cart)
    {
        return $cart && $cart->customer ? [
            'id' => $cart->customer->id,
            'first_name' => $cart->customer->first_name,
            'last_name' => $cart->customer->last_name,
            'email' => $cart->customer->email,
            'balance' => $cart->customer->balance,
            'created_at' => $cart->customer->created_at,
            'updated_at' => $cart->customer->updated_at,
            'laravel_through_key' => $cart->customer->id
        ] : null;
    }

    private function formatCartItems($cart)
    {
        return $cart ? $cart->cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'cart_id' => $item->cart_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'product' => [
                    'name' => $item->product->name,
                ]
            ];
        }) : [];
    }

    private function formatPaymentMethod($paymentMethod)
    {
        return $paymentMethod ? [
            'id' => $paymentMethod->id,
            'name' => $paymentMethod->name,
        ] : null;
    }

    private function formatOrderStatus($orderStatus)
    {
        return $orderStatus ? [
            'id' => $orderStatus->id,
            'name' => $orderStatus->name
        ] : null;
    }

    private function formatReason($reason)
    {
        return $reason ? [
            'id' => $reason->id,
            'description' => $reason->description,
        ] : null;
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:complete,ready,accept,cancel',
            'reason_id' => 'nullable|exists:reasons,id',
        ]);

        $order = Order::findOrFail($id);

        $statusMap = [
            'complete' => 4, // ID for 'Complete' status
            'ready' => 3,    // ID for 'Ready for pick-up' status
            'accept' => 2,   // ID for 'In progress' status
            'cancel' => 5,   // ID for 'Cancelled' status
        ];

        $order->order_status_id = $statusMap[$request->input('status')];

        if ($request->input('status') === 'cancel') {
            $order->reason_id = $request->input('reason_id');
        }

        $order->save();

        return response()->json($this->formatOrder($order), 200);
    }
}
