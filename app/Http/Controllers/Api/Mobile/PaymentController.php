<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function pay(Request $request)
{
    $validatedData = $request->validate([
        'amount' => 'required|in:20000,100000',
        'secret_code' => 'required|exists:secret_payments,secret_code'
    ]);

    $payment = new Payment();
    $payment->user_id = Auth::id();
    $payment->amount = $validatedData['amount'];
    $payment->secret_code = $validatedData['secret_code'];
    $payment->save();

    $user = Auth::user();
    
    if ($validatedData['amount'] == 100000) {
        $user->increment('product_number', 500);
    }
    $user->save();

    return response()->json([
        'message' => __('payment.payment_success'),
        'payment_id' => $payment->id,
        // 'secret_code' => $payment->secret_code,
    ]);
}

}
