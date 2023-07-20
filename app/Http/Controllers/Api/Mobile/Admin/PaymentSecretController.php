<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecretPayment;
use Illuminate\Http\Request;

class PaymentSecretController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentSecret = SecretPayment::all();

        return response()->json([
            'status' => true,
            'data' => $paymentSecret,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'secret_code' => 'required|unique:secret_payments',
        ]);

        $paymentSecret = SecretPayment::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => __('pay_secret.create_success'),
            'secret_code' => $paymentSecret,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentSecret = SecretPayment::find($id);
        if(!$paymentSecret){
            return response()->json([
               'status' => false,
               'message' => __('pay_secret.not_found'),
            ]);
        }

        $validatedData = $request->validate([
            'secret_code' => 'required|unique:secret_payments,secret_code,' . $id,
        ]);

        $paymentSecret->update($validatedData);

        return response()->json([
            'status' => true,
            'message' => __('pay_secret.update_success'),
            'secret_code' => $paymentSecret,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentSecret = SecretPayment::find($id);

        if (!$paymentSecret) {
            return response()->json([
                'status' => false,
                'message' => __('pay_secret.not_found')
            ], 404);
        }

        $paymentSecret->delete();

        return response()->json([
            'status' => true,
            'message' => __('pay_secret.destroy_success')
        ]);
    }
}
