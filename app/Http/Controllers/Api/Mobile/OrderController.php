<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $user = auth()->user();

        // Calculate order total based on product price and quantity
        $orderTotal = $product->price * $request->quantity;

        // Create the order
        $order = new Order();
        $order->user_id = $user->id;
        $order->product_id = $product->id;
        $order->quantity = $validatedData['quantity'];
        $order->total = $orderTotal;
        $order->save();

        return response()->json(['message' => 'Order placed successfully'], 201);
    }

    public function index()
    {
        $orders = Order::with(['product', 'user'])->orderBy('created_at', 'desc')->get();
        return OrderResource::collection($orders);
    }
}
