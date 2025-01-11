<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostProduct;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::paginate(10);

        return PostResource::collection($posts)->additional([
            'pagination' => [
                'total' => $posts->total(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'caption' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'product_id' => 'nullable|array',
            'product_id.*' => 'integer|exists:products,id',
        ]);

        // Upload file gambar jika ada
        $randomFileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileExtension = $file->getClientOriginalExtension();
            $randomFileName = Str::random(40) . '.' . $fileExtension;
            $file->storeAs('postImages', $randomFileName, 'public');
        }

        // Buat data post
        $post = Post::create([
            'caption' => $request->caption,
            'image' => $randomFileName,
            'user_id' => $request->user()->id,
            'viewed' => 0
        ]);

        // Jika ada product_id, simpan ke tabel post_products
        if ($request->has('product_id') && is_array($request->product_id)) {
            foreach ($request->product_id as $productId) {
                PostProduct::create([
                    'post_id' => $post->id,
                    'product_id' => $productId,
                ]);
            }
        }

        // Return response JSON
        return response()->json([
            'message' => 'Post created successfully!',
            'data' => new PostDetailResource($post)
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::with(['user', 'products'])->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found.'
            ], 404);
        }

        // tambah 1 ke kolom viewed
        $post->viewed += 1;
        $post->save();

        return new PostDetailResource($post);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found.'
            ], 404);
        }

        // Validasi input
        $request->validate([
            'caption' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'product_id' => 'nullable|array',
            'product_id.*' => 'integer|exists:products,id', // Pastikan setiap product_id valid
        ]);

        // Update file gambar jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($post->image && Storage::disk('public')->exists('postImages/' . $post->image)) {
                Storage::disk('public')->delete('postImages/' . $post->image);
            }

            // Upload gambar baru
            $file = $request->file('image');
            $fileExtension = $file->getClientOriginalExtension();
            $randomFileName = Str::random(40) . '.' . $fileExtension;
            $file->storeAs('postImages', $randomFileName, 'public');

            $post->image = $randomFileName;
        }

        // Update caption jika ada
        if ($request->has('caption')) {
            $post->caption = $request->caption;
        }

        // Simpan data post
        $post->save();

        // Update relasi produk jika ada
        if ($request->has('product_id')) {
            // Hapus semua relasi produk lama
            PostProduct::where('post_id', $post->id)->delete();

            // Tambahkan relasi produk baru
            foreach ($request->product_id as $productId) {
                PostProduct::create([
                    'post_id' => $post->id,
                    'product_id' => $productId,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post updated successfully!',
            'data' => new PostDetailResource($post)
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found.'
            ], 404);
        }

        // Hapus file gambar dari storage
        if ($post->image && Storage::disk('public')->exists('postImages/' . $post->image)) {
            Storage::disk('public')->delete('postImages/' . $post->image);
        }

        // Hapus relasi di tabel post_products
        PostProduct::where('post_id', $post->id)->delete();

        // Hapus post
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.'
        ], 200);
    }
}
