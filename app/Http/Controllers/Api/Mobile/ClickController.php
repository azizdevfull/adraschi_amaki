<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClickController extends Controller
{
    public function prepare(Request $request)
    {
        // $secretKey = ''; // Replace with your actual secret key

        // Log the request data
        // \Log::info('Click Prepare Request:', $request->all());

        // Extract parameters from the request
        $clickTransId = $request->input('click_trans_id');
        $serviceId = $request->input('service_id');
        $clickPaydocId = $request->input('click_paydoc_id');
        $merchantTransId = $request->input('merchant_trans_id');
        $amount = $request->input('amount');
        $action = $request->input('action');
        $error = $request->input('error');
        $errorNote = $request->input('error_note');
        $signTime = $request->input('sign_time');
        $signString = $request->input('sign_string');
        // \Log::info('Click Prepare signString reski:', [$signString]);
        $secretKey = 'GFwdmEQpHp5';

        $generatedSignString = md5($clickTransId . $serviceId . $secretKey . $merchantTransId . $amount . $action . $signTime);

        // \Log::info('Click Prepare signString:', [$signString]);
        // \Log::info('Click Prepare generatedSignString:', [$generatedSignString]);
        if ($signString !== $generatedSignString) {
            return response()->json(['error' => -1, 'error_note' => 'Invalid sign_string']);
        }

        $response = [
            'click_trans_id' => $clickTransId,
            'merchant_trans_id' => $merchantTransId,
            'merchant_prepare_id' => 123,
            'error' => 0,
            'error_note' => 'Payment prepared successfully',
        ];

        // Log the response data
        // \Log::info('Click Prepare Response:', $response);

        return response()->json($response);
    }
    public function complete(Request $request)
    {
        // Extract the parameters from the request
        $clickTransId = $request->input('click_trans_id');
        $serviceId = $request->input('service_id');
        $clickPaydocId = $request->input('click_paydoc_id');
        $merchantTransId = $request->input('merchant_trans_id');
        $merchantPrepareId = $request->input('merchant_prepare_id');
        $amount = $request->input('amount');
        $action = $request->input('action');
        $error = $request->input('error');
        // \Log::info('Click Prepare signString:', [$error]);
        $errorNote = $request->input('error_note');
        $signTime = $request->input('sign_time');
        $signString = $request->input('sign_string');
        $secretKey = 'GFwdmEQpHp5'; // Replace with your actual SECRET_KEY

        // \Log::info('Click Prepare signString:', [$signString]);
        // Validate the sign_string using MD5 hash
        $generatedSignString = md5($clickTransId . $serviceId . $secretKey . $merchantTransId . $merchantPrepareId . $amount . $action . $signTime);
        // \Log::info('Click Prepare generatedSignString:', [$generatedSignString]);

        if ($signString !== $generatedSignString) {
            return response()->json(['error' => -1, 'error_note' => 'Invalid sign_string']);
        }

        // Perform necessary validation and processing here

        // Check if the payment was successful or not
        if ($error == 0) {
            // Payment was successful, update your database accordingly

            // Return a successful response
            return response()->json([
                'click_trans_id' => $clickTransId,
                'merchant_trans_id' => $merchantTransId,
                'merchant_confirm_id' => $merchantTransId,
                'error' => 0,
                'error_note' => 'Payment Success'
            ]);
        } else {
            // Payment was not successful, handle the error case

            // Return an error response
            return response()->json(['error' => -1, 'error_note' => 'Payment error']);
        }
    }
}
