<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        // 2. Gunakan DB Transaction untuk keamanan data
        return DB::transaction(function () use ($request) {
            $totalPrice = 0;
            $orderItems = [];

            // 3. Hitung Total & Cek Stok
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'message' => "Stok {$product->name} tidak cukup!"
                    ], 422);
                }

                $subtotal = $product->price * $item['quantity'];
                $totalPrice += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $product->price,
                ];

                // Kurangi stok produk
                $product->decrement('stock', $item['quantity']);
            }

            // 4. Simpan Header Order
            $order = Order::create([
                'user_id' => auth()->id() ?? 1, // Default ke user ID 1 jika belum ada auth
                'order_number' => 'INV-' . strtoupper(Str::random(10)),
                'total_price' => $totalPrice,
                'shipping_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            // 5. Simpan Detail Order
            $order->items()->createMany($orderItems);

            return response()->json([
                'message' => 'Pesanan berhasil dibuat',
                'order' => $order->load('items.product')
            ], 201);
        });
    }
}
