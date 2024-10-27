<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get all notifications for a specific user
    public function index(Request $request)
    {
        $userId = $request->query('customer_id');

        $notifications = Notification::with('order')
            ->whereHas('order.cart', function ($query) use ($userId) {
                $query->where('customer_id', $userId);
            })
            ->get();

        $notifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'is_read' => $notification->is_read,
                'order_number' => $notification->order ? $notification->order->order_number : null,
                'created_at' => $notification->created_at,
            ];
        });

        return response()->json($notifications);
    }

    // Store a new notification
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string',
        ]);

        $notification = Notification::create([
            'order_id' => $request->order_id,
            'message' => $request->message,
        ]);

        return response()->json($notification, 201);
    }

    // Get a specific notification
    public function show($id)
    {
        $notification = Notification::with('order')->findOrFail($id); // Eager load the order relationship
        return response()->json([
            'id' => $notification->id,
            'message' => $notification->message,
            'is_read' => $notification->is_read,
            'order_number' => $notification->order->order_number, // Assuming the relationship is set
        ]);
    }


    // Update a notification
    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        $request->validate([
            'is_read' => 'required|boolean',
        ]);

        $notification->is_read = $request->is_read;
        $notification->save();

        return response()->json($notification);
    }

    // Delete a notification
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(null, 204);
    }
}