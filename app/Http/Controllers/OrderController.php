<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Support\ServiceZips;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['user', 'items', 'package', 'technician'])
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    public function show($id): JsonResponse
    {
        $order = Order::with(['user', 'items.product', 'package', 'technician'])
            ->findOrFail($id);

        return response()->json($order);
    }

    public function store(Request $request): JsonResponse
    {
        $billingZip = substr(preg_replace('/\D/', '', $request->billing_postcode ?? ''), 0, 5);
        $shippingZip = substr(preg_replace('/\D/', '', $request->shipping_postcode ?? $request->billing_postcode ?? ''), 0, 5);

        if (
            strlen($billingZip) !== 5 ||
            strlen($shippingZip) !== 5 ||
            !ServiceZips::isAllowed($billingZip) ||
            !ServiceZips::isAllowed($shippingZip)
        ) {
            return response()->json([
                'message' => 'We only serve the Sarasota / Bradenton / Lakewood Ranch area. Please use a valid ZIP code.',
            ], 422);
        }

        $validated = $request->validate([
            'billing_first_name'     => 'required|string|max:255',
            'billing_last_name'      => 'required|string|max:255',
            'billing_email'          => 'required|email',
            'billing_phone'          => 'required|string',
            'billing_postcode'       => 'required|string|max:10',
            'shipping_address_1'     => 'nullable|string',
            'shipping_postcode'      => 'nullable|string|max:10',
            'preferred_install_date' => 'nullable|date',
            'removal_date'           => 'nullable|date',
            'subtotal'               => 'required|numeric',
            'tax_total'              => 'nullable|numeric',
            'total'                  => 'required|numeric',
            'status'                 => 'nullable|string',
            'payment_method'         => 'nullable|string',
            'payment_status'         => 'nullable|string',
            'customer_note'          => 'nullable|string',
            'ssl_txn_id'             => 'nullable|string',
            'ssl_approval_code'      => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer',
            'items.*.product_name'   => 'nullable|string',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.price'          => 'required|numeric',
            'items.*.options'        => 'nullable|array',
            'items.*.is_package'     => 'nullable|boolean',
        ]);

        $order = Order::create([
            'order_number'           => 'FLP-' . strtoupper(Str::random(8)),
            'billing_first_name'     => $validated['billing_first_name'],
            'billing_last_name'      => $validated['billing_last_name'],
            'billing_email'          => $validated['billing_email'],
            'billing_phone'          => $validated['billing_phone'],
            'billing_postcode'       => $billingZip,
            'shipping_address_1'     => $validated['shipping_address_1'] ?? null,
            'shipping_postcode'      => $shippingZip,
            'preferred_install_date' => $validated['preferred_install_date'] ?? null,
            'removal_date'           => $validated['removal_date'] ?? null,
            'status'                 => $validated['status'] ?? 'pending',
            'payment_method'         => $validated['payment_method'] ?? 'card',
            'payment_status'         => $validated['payment_status'] ?? 'pending',
            'subtotal'               => $validated['subtotal'],
            'tax_total'              => $validated['tax_total'] ?? 0,
            'total'                  => $validated['total'],
            'customer_note'          => $validated['customer_note'] ?? null,
            'ssl_txn_id'             => $validated['ssl_txn_id'] ?? null,
            'ssl_approval_code'      => $validated['ssl_approval_code'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $productName = $item['product_name'] ?? null;

            if (!$productName) {
                $product = Product::find($item['product_id']);
                $productName = $product?->name ?? 'Unknown Product';
            }

            $order->items()->create([
                'product_id'   => $item['product_id'],
                'product_name' => $productName,
                'quantity'     => $item['quantity'],
                'price'        => $item['price'],
                'total'        => $item['quantity'] * $item['price'],
                'options'      => $item['options'] ?? null,
                'is_package'   => $item['is_package'] ?? false,
            ]);
        }

        return response()->json([
            'id'           => $order->id,
            'order_number' => $order->order_number,
            'message'      => 'Order placed successfully',
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status'                 => 'sometimes|in:pending,processing,on-hold,completed,cancelled,refunded,failed',
            'preferred_install_date' => 'nullable|date',
            'confirmed_install_date' => 'nullable|date',
            'removal_date'           => 'nullable|date',
            'technician_id'          => 'nullable|exists:users,id',
            'admin_note'             => 'nullable|string|max:1000',
            'payment_method'         => 'nullable|string',
            'transaction_id'         => 'nullable|string',
            'customer_note'          => 'nullable|string|max:1000',
        ]);

        $order->update($validated);

        return response()->json($order->load(['user', 'items', 'technician']));
    }

    public function destroy($id): JsonResponse
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }

    public function showByNumber(string $orderNumber): JsonResponse
    {
        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }
}