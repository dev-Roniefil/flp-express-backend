<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConvergeController extends Controller
{
    public function token(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email',
            'invoice_number' => 'nullable|string',
        ]);

        $demo = filter_var(env('CONVERGE_DEMO', true), FILTER_VALIDATE_BOOLEAN);
        $url = $demo
            ? 'https://api.demo.convergepay.com/hosted-payments/transaction_token'
            : 'https://api.convergepay.com/hosted-payments/transaction_token';

        $response = Http::asForm()->post($url, [
            'ssl_merchant_id'      => env('CONVERGE_MERCHANT_ID'),
            'ssl_user_id'          => env('CONVERGE_USER_ID'),
            'ssl_pin'              => env('CONVERGE_PIN'),
            'ssl_transaction_type' => 'ccsale',
            'ssl_amount'           => number_format((float) $request->amount, 2, '.', ''),
            'ssl_first_name'       => $request->first_name,
            'ssl_last_name'        => $request->last_name,
            'ssl_email'            => $request->email,
            'ssl_invoice_number'   => $request->invoice_number,
        ]);

        // Token is usually plain text body
        $token = trim($response->body());

        if (!$response->successful() || str_contains(strtolower($token), 'error')) {
            return response()->json([
                'message' => 'Failed to get Converge token',
                'detail' => $token,
            ], 422);
        }

        return response()->json(['token' => $token]);
    }

    public function complete(Request $request)
    {
        // Save order after approval — map to your Order model
        $data = $request->validate([
            'ssl_result' => 'nullable',
            'ssl_txn_id' => 'nullable|string',
            'ssl_approval_code' => 'nullable|string',
            'ssl_amount' => 'nullable|string',
            'order_payload' => 'nullable|array',
        ]);

        // Example: mark payment paid, store txn id
        // Order::create([...]);

        return response()->json([
            'success' => true,
            'order_number' => $data['order_payload']['order_number'] ?? null,
        ]);
    }
}