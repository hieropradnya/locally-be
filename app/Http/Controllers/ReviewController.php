<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product', 'order'])->get();
        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'product_id' => 'required|exists:products,id',
            'order_id'  => 'required|exists:orders,id',
        ]);

        $request['user_id'] = $request->user()->id;


        // upload file image jika ada
        $randomImageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $fileExtension = $file->getClientOriginalExtension();
            $randomImageName = Str::random(40) . '.' . $fileExtension;

            $file->storeAs('seller/images', $randomImageName, 'public');
        }


        $review = Review::create([
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'image'      => $randomImageName,
            'user_id'    => $request->user_id,
            'product_id' => $request->product_id,
            'order_id'   => $request->order_id,
        ]);

        return response()->json($review, 201);
    }

    public function show($id)
    {
        $review = Review::with(['user', 'product', 'order'])->find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        return response()->json($review);
    }

    public function update(Request $request, $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        $request->validate([
            'rating'    => 'sometimes|integer|min:1|max:5',
            'comment'   => 'nullable|string',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($review->image) {
                Storage::disk('public')->delete($review->image);
            }
            $image = $request->file('image');
            $review->image = $image->store('reviewImages', 'public');
        }

        $review->update($request->only('rating', 'comment'));

        return response()->json($review);
    }

    public function destroy($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }

        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
