<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images', 'seller')->get();
        return response()->json(['data' => $products]);
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
            'variants'    => 'nullable|array',
            'variants.*.variant' => 'required_with:variants|string|max:50',
            'variants.*.stock'   => 'required_with:variants|integer|min:0',
            'category'    => 'required|integer|min:0',
        ]);


        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'seller_id'   => $request->user()->id,
            'category_id'   => $request->category,
        ]);


        // jika produk memiliki variasi
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'variant'    => $variant['variant'],
                    'stock'      => $variant['stock'],
                    'product_id' => $product->id,
                ]);
            }
        } else {
            // jika produk tidak memiliki variasi (varian default)
            ProductVariant::create([
                'variant'    => 'default',
                'stock'      => $product->stock,
                'product_id' => $product->id,
            ]);
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

        return response()->json(['data' => $product->load(['images', 'variants', 'category', 'seller'])]);
    }

    public function show($id)
    {
        $product = Product::with('images', 'variants', 'category', 'seller')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product->load(['images', 'variants', 'category', 'seller'])]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('variants', 'images')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|integer|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            "deletedImagesId.*" => "nullable|exists:product_images,id",
            'variants'    => 'nullable|array',
            'variants.*.variant' => 'required_with:variants|string|max:50',
            'variants.*.stock'   => 'required_with:variants|integer|min:0',
            'category'    => 'sometimes|integer|min:0',
        ]);

        $product->update($request->only('name', 'description', 'price', 'stock', 'category'));

        if ($request->has('variants')) {
            $product->variants()->delete();

            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'variant'    => $variant['variant'],
                    'stock'      => $variant['stock'],
                    'product_id' => $product->id,
                ]);
            }
        } else {
            $defaultVariant = $product->variants()->where('variant', 'default')->first();
            if ($defaultVariant) {
                $defaultVariant->update(['stock' => $product->stock]);
            }
        }

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

        return response()->json(['data' => $product->load(['images', 'variants', 'category', 'seller'])]);
    }

    public function destroy($id)
    {
        $product = Product::with('variants', 'images')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete('productImages/' . $image->image);
            $image->delete();
        }

        $product->variants()->delete();

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
