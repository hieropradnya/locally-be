<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images', 'seller')->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
            'images'      => 'required|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'seller_id'   => $request->user()->id,
        ]);


        if ($request->hasFile('images')) {
            foreach ($request->images as $image) {
                $extention = $image->getClientOriginalExtension();
                $filename = Str::random(40) . '.' . $extention;
                $image->storeAs('productImages', $filename, 'public');

                ProductImage::create([
                    'image'      => $filename,
                    'product_id' => $product->id,
                ]);
            }
        }


        return response()->json($product->load('images'), 201);
    }

    public function show($id)
    {
        $product = Product::with('images', 'seller')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|integer|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            "deletedImagesId.*" => "nullable|exists:product_images,id", // validasi ID gambar yang ada di database
        ]);

        $product->update($request->only('name', 'description', 'price', 'stock'));

        if ($request->has('deletedImagesId')) {
            foreach ($request->deletedImagesId as $imageId) {
                $productImage = ProductImage::find($imageId);

                if ($productImage && $productImage->product_id == $product->id) {
                    Storage::disk('public')->delete('productImages/' . $productImage->image);

                    $productImage->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->images as $image) {
                $extention = $image->getClientOriginalExtension();
                $filename = Str::random(40) . '.' . $extention;
                $image->storeAs('productImages', $filename, 'public');

                ProductImage::create([
                    'image'      => $filename,
                    'product_id' => $product->id,
                ]);
            }
        }

        return response()->json($product->load('images'));
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // hapus gambar
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
            $image->delete();
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
