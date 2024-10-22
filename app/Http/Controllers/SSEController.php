<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SSEController extends Controller
{
    public function stream(Request $request)
    {
        // CORS headers
        header('Access-Control-Allow-Origin: http://localhost:3000'); // Specify the origin you want to allow
        header('Access-Control-Allow-Headers: Content-Type'); // Specify the allowed headers
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Allow specific methods

        // Handle preflight requests
        if ($request->getMethod() === "OPTIONS") {
            http_response_code(200); // Respond with a success status code
            exit; // End the request
        }

        // Set content type and other headers for SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        while (true) {
            if (connection_aborted()) {
                break; // Exit the loop if the connection is aborted
            }

            // Query the database for recent order updates
            $orderUpdates = DB::table('orders')
                ->join('carts', 'orders.cart_id', '=', 'carts.id')
                ->join('customers', 'carts.customer_id', '=', 'customers.id')
                ->where('orders.updated_at', '>', now()->subSeconds(5))
                ->select('orders.id', 'orders.order_status_id', 'customers.id as customer_id')
                ->get();

            if ($orderUpdates->count() > 0) {
                foreach ($orderUpdates as $order) {
                    $data = [
                        'order_id' => $order->id,
                        'status' => $order->order_status_id,
                        'customer_id' => $order->customer_id,
                    ];

                    // Send the event data to the client
                    echo "data: " . json_encode($data) . "\n\n";
                    ob_flush();
                    flush();
                }
            }

            sleep(5); // Adjust sleep interval as needed
        }
    }
}
