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

    const LINK = 'https://checkout.paycom.uz';
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'payment_type' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Assuming you are using authentication, get the authenticated user
        $user = auth()->user();

        // Create the order
        $order = new Order();
        $order->user_id = $user->id;
        $order->save();

        $totalAmount = 0; // Initialize the total amount

        foreach ($validatedData['products'] as $productData) {
            $product = Product::findOrFail($productData['id']);
            $quantity = $productData['quantity'];
            $total = $product->price * $quantity;

            // Attach the product to the order with quantity and total
            $order->products()->attach($product->id, ['quantity' => $quantity, 'total' => $total]);

            // Update the total amount
            $totalAmount += $total;
        }

        // Update the order's total with the calculated total amount
        $order->total = $totalAmount;
        $order->save();

        // Generate Click URL for payment
        if ($request->payment_type == 'click') {
            $url = $this->generateClickUrl($order->id, $totalAmount);
        }else if ($request->payment_type == 'payme'){
            $url = $this->generatePaymeUrl($order->id, $totalAmount);
        }

        return response()->json([
            'message' => 'Order placed successfully',
            'click_url' => $url,
        ], 201);
    }



    public function generateClickUrl($order_id, $orderTotal)
    {
        $serviceId = '29507';
        $merchantId = '21817';
        $transactionParam = $order_id;
        $clickUrl = "https://my.click.uz/services/pay";
        $clickUrl .= "?service_id=$serviceId&merchant_id=$merchantId";
        $clickUrl .= "&amount=$orderTotal&transaction_param=$transactionParam";

        return $clickUrl;
    }
    public static function generatePaymeUrl($order_id,$amount)
    {
       
        $merchant_id = '64ef2f2d9f1e571160d52f76';
        $amount = $amount . '00';
        $params = "m={$merchant_id};ac.order_id={$order_id};a={$amount};"; // Use $amount_decimal
        $encode_params = base64_encode(utf8_encode($params));
        $url = self::LINK . '/' . $encode_params;
        return $url;
    }
}
