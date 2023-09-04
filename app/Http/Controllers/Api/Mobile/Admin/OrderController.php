<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // $orders = Order::where('status', 'yakunlandi')->with(['product', 'user'])->orderBy('created_at', 'desc')->get();
        $orders = Order::with('products')->get();
        return response()->json(['data' => OrderResource::collection($orders)]);
    }
    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Buyurtma topilmadi!']);
        }

        return response()->json([new OrderResource($order)]);
    }
}
