<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\OrderDetail;
use App\Models\PriceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderDetailResource;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|array',
            'message' => 'nullable',
            'voucher_id' => 'nullable',
            'shipping_cost' => 'required|integer',


        ]);

        // Validasi bahwa user hanya checkout untuk seller yang sama
        $cartIds = $request->input('cart_id'); // Expecting an array of cart_ids from the request
        $userId = $request->user()->id;



        // Ambil item cart yang sesuai dengan cart_id yang diberikan
        $cartItems = Cart::with('productVariant.product')
            ->where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->get();


        // Jika cart kosong atau tidak ditemukan
        if ($cartItems->isEmpty()) {
            return response()->json([
                'error' => 'Cart items not found or invalid cart IDs.'
            ], 404);
        }

        $sellerId = $cartItems->first()->productVariant->product->seller_id;


        // Validasi bahwa semua item di dalam cart berasal dari seller yang sama
        foreach ($cartItems as $cartItem) {
            if ($cartItem->productVariant->product->seller_id != $sellerId) {
                return response()->json([
                    'error' => 'All items in the cart must be from the same seller.'
                ], 422);
            }
        }

        // Proses pengecekan stok
        foreach ($cartItems as $cartItem) {
            if ($cartItem->productVariant->stock < $cartItem->quantity) {
                return response()->json([
                    'error' => 'Insufficient stock for product: ' . $cartItem->productVariant->product->name
                ], 422);
            }
        }

        // Pengecekan alamat (1 user hanya punya 1 address)
        $address = $request->user()->address;
        if (!$address) {
            return response()->json([
                'error' => 'User does not have a valid address.'
            ], 422);
        }

        // Transaction Wrapping
        DB::beginTransaction();
        try {
            // Step 1: Buat Order
            $order = Order::create([
                'status_order_id' => 1,
                'message' => $request->message ?? null,
                'total_price' => 0, //belum diisi
                'is_reviewed' => 0,
                'seller_id' => $sellerId,
                'user_id' => $userId,
                'address_id' => $address->id,
                'price_detail_id' => null, //belum diisi
                'voucher_id' => $request->voucher_id ?? null,
            ]);

            // Step 2: Buat OrderDetails dari Cart
            $orderDetails = [];
            $productSubtotal = 0;

            foreach ($cartItems as $cartItem) {

                // Ambil product_variant terkait dengan item cart
                $productVariant = $cartItem->productVariant;

                // Kurangi stok produk variant sesuai jumlah yang dibeli
                $productVariant->decrement('stock', $cartItem->quantity);

                // Kurangi stok produk utama yang terkait dengan product_variant
                $product = $productVariant->product;
                $product->decrement('stock', $cartItem->quantity);

                // Buat OrderDetail untuk setiap item
                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'quantity' => $cartItem->quantity,
                    'product_price' => $cartItem->productVariant->product->price,
                    'product_variant_id' => $cartItem->product_variant_id
                ]);

                // Menyimpan order detail dalam array dan menghitung subtotal produk
                $orderDetails[] = $orderDetail;
                $productSubtotal += $orderDetail->product_price * $orderDetail->quantity;
            }

            $order->update([
                'total_price' => $productSubtotal,
            ]);

            // Step 3: Hitung PriceDetail
            $shippingCost = (int) $request->shipping_cost;
            $serviceFee = 2000;

            // Step 4: Pengecekan validitas voucher
            $discount = 0;
            if ($request->has('voucher_id')) {
                $voucher = Voucher::find($request->voucher_id);
                if ($voucher && $voucher->expiry_date > now()) {
                    $discount = $productSubtotal * ($voucher->discount_percentage / 100);
                } else {
                    return response()->json(['error' => 'Voucher is invalid or expired'], 422);
                }
            }

            // Step 5: Simpan PriceDetail
            $priceDetail = PriceDetail::create([
                'product_subtotal' => $productSubtotal,
                'shipping_cost' => $shippingCost,
                'service_fee' => $serviceFee,
                'discount' => $discount
            ]);

            // Step 6: Update Order dengan total_price dan price_detail_id
            $totalPrice = ($productSubtotal + $shippingCost + $serviceFee) - $discount;
            $order->update([
                'total_price' => $totalPrice,
                'price_detail_id' => $priceDetail->id
            ]);

            // Step 7: Hapus Record dari Cart setelah Order berhasil dibuat
            Cart::whereIn('id', $cartIds)->delete();

            DB::commit();

            $orderNew = Order::with([
                'statusOrder',
                'address',
                'seller',
                'priceDetail',
                'orderDetail.productVariant.product'
            ])->find($order->id);

            return new OrderDetailResource($orderNew);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create order.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        // Mengambil semua order yang dimiliki oleh pengguna yang sedang login
        $orders = Order::with([
            'statusOrder',
            'seller',
            'priceDetail',
            'orderDetail.productVariant.product'
        ])->where('user_id', auth('api')->id())
            ->get();

        // Cek jika tidak ada order yang ditemukan
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
                'data' => null
            ], 404);
        }

        // Mengembalikan koleksi order dalam format yang sesuai dengan OrderDetailResource
        return OrderResource::collection($orders);
    }


    public function show($id)
    {
        $order = Order::with([
            'statusOrder',
            'address',
            'seller',
            'priceDetail',
            'orderDetail.productVariant.product'
        ])->find($id);

        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found.',
                'data' => null
            ], 404);
        }

        return new OrderDetailResource($order);
    }
}
