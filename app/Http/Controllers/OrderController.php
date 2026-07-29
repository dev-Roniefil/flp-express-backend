<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'billing_first_name'     => 'required|string|max:255',
            'billing_last_name'      => 'required|string|max:255',
            'billing_email'          => 'required|email',
            'billing_phone'          => 'required|string',
            'shipping_address_1'     => 'nullable|string',
            'preferred_install_date' => 'nullable|date',
            'removal_date'           => 'nullable|date',
            'subtotal'               => 'required|numeric',
            'tax_total'              => 'nullable|numeric',
            'total'                  => 'required|numeric',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|integer',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.price'          => 'required|numeric',
            'items.*.options'        => 'nullable|array',
        ]);

        $order = Order::create([
            'order_number'           => 'FLP-' . strtoupper(Str::random(8)),
            'billing_first_name'     => $validated['billing_first_name'],
            'billing_last_name'      => $validated['billing_last_name'],
            'billing_email'          => $validated['billing_email'],
            'billing_phone'          => $validated['billing_phone'],
            'shipping_address_1'     => $validated['shipping_address_1'] ?? null,
            'preferred_install_date' => $validated['preferred_install_date'] ?? null,
            'removal_date'           => $validated['removal_date'] ?? null,
            'status'                 => 'pending',
            'subtotal'               => $validated['subtotal'],
            'tax_total'              => $validated['tax_total'] ?? 0,
            'total'                  => $validated['total'],
            'payment_method'         => 'card',
        ]);

        // inside store()
        foreach ($validated['items'] as $item) {
            $productName = $item['product_name'] ?? null;

            // Fallback: get name from products table
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
            'status' => 'sometimes|in:pending,processing,on-hold,completed,cancelled,refunded,failed',
            'preferred_install_date' => 'nullable|date',
            'confirmed_install_date' => 'nullable|date',
            'removal_date' => 'nullable|date',
            'technician_id' => 'nullable|exists:users,id',
            'admin_note' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'customer_note'  => 'nullable|string|max:1000',
        ]);

        $order->update($validated);

        return response()->json($order->load(['user', 'items', 'technician']));
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order = Order::findOrFail($id);
        $order->items()->delete(); // if you have order_items
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }

    public function showByNumber(string $orderNumber)
    {
        $order = \App\Models\Order::with('items')
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }
}