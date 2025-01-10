<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with([
            'productVariant.product.seller',
        ])
            ->where('user_id', auth('api')->id())
            ->get();

        // Mengembalikan data carts menggunakan CartResource
        return CartResource::collection($carts);
    }


    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'quantity'           => 'required|integer|min:1',
            'product_variant_id' => 'required|exists:product_variants,id',
        ]);

        // cek apakah produk sudah ada di cart user
        $existingCart = Cart::where('user_id', auth('api')->id())
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($existingCart) {
            $existingCart->update([
                'quantity' => $existingCart->quantity + $request->quantity,
            ]);

            return response()->json([
                'message' => 'Quantity updated successfully',
                'data'    => $existingCart
            ], 200);
        }

        $cart = Cart::create([
            'quantity'           => $request->quantity,
            'user_id'            => auth('api')->id(),
            'product_variant_id' => $request->product_variant_id,
        ]);

        return response()->json(['data' => $cart], 201);
    }

    public function show($id)
    {
        // Mengambil detail cart item berdasarkan id
        $cart = Cart::with(['user', 'productVariant'])
            ->where('user_id', auth('api')->id())
            ->find($id);

        if (!$cart) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        return response()->json(['data' => $cart], 200);
    }

    public function update(Request $request, $id)
    {
        // Mengambil item cart
        $cart = Cart::where('user_id', auth('api')->id())->find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        // Validasi update quantity
        $request->validate([
            'quantity' => 'sometimes|integer|min:1',
        ]);

        // Update quantity
        $cart->update($request->only('quantity'));

        return response()->json(['data' => $cart], 200);
    }

    public function destroy($id)
    {
        // Mengambil item cart
        $cart = Cart::where('user_id', auth('api')->id())->find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        // Menghapus item cart
        $cart->delete();

        return response()->json(['message' => 'Cart item deleted successfully'], 200);
    }
}
