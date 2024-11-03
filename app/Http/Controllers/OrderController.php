<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use App\Jobs\SendMessageJob;
use App\Jobs\SendNotificationJob;
use App\Http\Services\ImageService;
use App\Models\Notification;
use App\Events\NotificationCreated;
use App\Events\MessageSent;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::with([
                'cart.customer',
                'cart.cartItems.product',
                'cart.paymentMethod',
                'orderStatus',
                'reason'
            ])->get();

            // Check if orders are empty
            if ($orders->isEmpty()) {
                return response()->json(['message' => 'No orders found.'], 404);
            }

            // Map orders to the desired format
            return response()->json($orders->map(function ($order) {
                return $this->formatOrder($order);
            }));
        } catch (\Exception $e) {
            // Return the error message with a 500 status code
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
            'order_number' => $order->order_number,
            'cart_id' => $order->cart_id,
            'order_status_id' => $order->order_status_id,
            'reason_id' => $order->reason_id,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'customer' => $this->formatCustomer($order->cart),
            'cart' => [
                'payment_method' => $this->formatPaymentMethod($order->cart->paymentMethod),
                'total' => $order->cart ? $order->cart->total : null,
                'cart_items' => $this->formatCartItems($order->cart),
                'schedule' => $order->cart->schedule
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
        $customerId = $order->cart->customer->id;

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

        $this->handleOrderStatusMessage($order, $request->input('status'), $customerId);

        $order->save();

        return response()->json($this->formatOrder($order), 200);
    }

    private function handleOrderStatusMessage(Order $order, string $status, int $customerId)
    {
        switch ($status) {
            case 'ready':
                $this->dispatchOrderReadyMessage($order, $customerId);
                break;
            case 'complete':
                $this->dispatchOrderCompleteMessage($order, $customerId);
                break;
        }
    }

    private function dispatchOrderReadyMessage(Order $order, int $customerId)
    {
        $icon = "mdi-alarm";
        $header = "Your order is ready!";
        $message = "Your order is now prepared and ready for pickup. Please collect it at your earliest convenience.";

        // Create notification
        $notification = Notification::create([
            'order_id' => $order->id,
            'message' => 'Your order is ready for pick-up!',
            'is_read' => false,
        ]);

        event(new NotificationCreated($notification));
        event(new MessageSent($icon, $header, $message, $customerId));
    }

    private function dispatchOrderCompleteMessage(Order $order, int $customerId)
    {
        $icon = "mdi-party-popper";
        $header = "Thank You!";
        $message = "You've got your order in hand! We hope you enjoy it and can't wait to see you again soon!";

        // Create notification
        $notification = Notification::create([
            'order_id' => $order->id,
            'message' => 'Thank you for ordering!',
            'is_read' => false,
        ]);

        event(new NotificationCreated($notification));
        event(new MessageSent($icon, $header, $message, $customerId));
    }

    public function getOrdersWithInactiveCart($customerId)
    {
        $orders = Order::with('cart', 'cart.cartItems.product')
            ->whereHas('cart', function ($query) use ($customerId) {
                $query->where('customer_id', $customerId)
                    ->whereNotNull('total')
                    ->whereNotNull('schedule')
                    ->whereNotNull('payment_id');
            })
            ->get()
            ->map(function ($order) {
                $order->status_label = OrderStatus::from($order->order_status_id)->name;

                $order->cart->cartItems->map(function ($cartItem) {
                    $product = $cartItem->product;

                    $product->image_url = $product->image
                        ? $this->imageService->getTemporaryImageUrl($product->image)
                        : null;

                    return $cartItem;
                });

                return $order;
            });

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for this customer'], 404);
        }

        return response()->json($orders);
    }
    public function getCompleteOrders($customerId)
    {
        $orders = Order::with('cart', 'cart.cartItems')
            ->where('order_status_id', OrderStatus::Completed->value)
            ->whereHas('cart', function ($query) use ($customerId) {
                $query->where('customer_id', $customerId)
                    ->whereNotNull('total')
                    ->whereNotNull('schedule')
                    ->whereNotNull('payment_id');
            })
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for this customer'], 404);
        }

        return response()->json($orders);
    }

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
}
