<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with('items.product')->firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $total = $cart->items->sum(fn ($item) => $item->price * $item->quantity);

        return response()->json([
            'total' => $total,
            'count' => $cart->items->sum('quantity'),
            'items' => $cart->items
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'is_package' => 'boolean',
            'options'    => 'nullable|array',
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Unique key so same product + different color = different cart line
        $options = $validated['options'] ?? null;
        $optionsHash = $options ? md5(json_encode($options)) : null;

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('options_hash', $optionsHash)
            ->first();

        if ($item) {
            $item->update([
                'quantity' => $item->quantity + $validated['quantity'],
            ]);
        } else {
            $cart->items()->create([
                'product_id'   => $product->id,
                'quantity'     => $validated['quantity'],
                'price'        => $product->price,
                'is_package'   => $validated['is_package'] ?? false,
                'options'      => $options,
                'options_hash' => $optionsHash,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Added to cart'
        ]);
    }

    public function destroy($id)
    {
        $item = CartItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Removed from cart']);
    }

    public function clear()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Cart cleared']);
    }
}