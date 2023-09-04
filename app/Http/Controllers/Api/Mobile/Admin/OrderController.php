<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use mrmuminov\eskizuz\Eskiz;

class OrderController extends Controller
{
    public function index()
    {
        // $orders = Order::where('status', 'yakunlandi')->with(['product', 'user'])->orderBy('created_at', 'desc')->get();
        $orders = Order::where('status', 'yakunlandi')->with('products')->get();
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

    public function qabul(Request $request, $id)
    {
        $request->validate([
            'qabul' => 'required|boolean'
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => __('order.not_found')
            ]);
        }else if($order->qabul == 'true'){
            return response()->json([
                'status' => false,
                'message' => __('order.accepted')
            ], 200);
        }

        $order->qabul = $request->qabul;
        $order->update();
        if ($order->qabul == 'true') {

            $eskiz = new Eskiz("dostonjontangirov412@gmail.com", "SMl9YuMJxTAw3ZFqvNziN7dYimT46f8BKIu7TjyY");

            $eskiz->requestAuthLogin();

            $result = $eskiz->requestSmsSend(
                '4546',
                'Adraschi Amaki' . PHP_EOL . 'Buyurtmangiz Qabul Qilindi!',
                $order->user->phone,
                '1',
                ''
            );
            
            if ($result->getResponse()->isSuccess == true) {
                return response()->json([
                    'status' => true,
                    'message' => __('order.success')
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('order.failure')
                ], 500);
            }
        }
    }
}
