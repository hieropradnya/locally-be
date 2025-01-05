<?php

namespace App\Http\Controllers\api;

use App\Models\Seller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SellerController extends Controller
{
    public function store(Request $request)
    {
        // jika sudah terdaftar sebagai penjual
        if ($request->user()->role == 'seller') {
            return response()->json([
                'error' => 'You are already registered as a seller.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|max:255',
            'description' => 'nullable',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // isi id dan status seller otomatis aktif
        $request['id'] = $request->user()->id;
        $request['status'] = 'aktif';

        // upload file logo jika ada
        $randomLogoName = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $fileExtension = $file->getClientOriginalExtension();
            $randomLogoName = Str::random(40) . '.' . $fileExtension;

            $file->storeAs('seller/logos', $randomLogoName, 'public');
        }

        // upload file banner jika ada
        $randomBannerName = null;
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');

            $fileExtension = $file->getClientOriginalExtension();
            $randomBannerName = Str::random(40) . '.' . $fileExtension;

            $file->storeAs('seller/banners', $randomBannerName, 'public');
        }


        $seller = Seller::create([
            'id'         => $request->id,
            'brand_name' => $request->brand_name,
            'logo'       => $randomLogoName,
            'banner'     => $randomBannerName,
            'status'     => $request->status,
        ]);

        // ubah role user menjadi 'seller'
        $user = $request->user();
        $user->role = 'seller';
        $user->save();

        return response()->json(['success' => true, 'seller' => $seller], 201);
    }

    public function index()
    {
        // $sellers = Seller::with('user')->get();
        $sellers = Seller::get();
        return response()->json(['sellers' => $sellers], 200);
    }

    public function show($id)
    {
        // cek apakah seller dengan id yang diberikan ada
        $seller = Seller::where('id', $id)->first();

        if (!$seller) {
            return response()->json([
                'error' => 'Seller not found.',
            ], 404);
        }

        // kembalikan detail seller
        return response()->json([
            'success' => true,
            'seller' => [
                'id' => $seller->id,
                'brand_name' => $seller->brand_name,
                'logo' => $seller->logo ? asset('storage/seller/logos/' . $seller->logo) : null,
                'banner' => $seller->banner ? asset('storage/seller/banners/' . $seller->banner) : null,
                'status' => $seller->status,
                'created_at' => $seller->created_at,
                'updated_at' => $seller->updated_at,
            ]
        ], 200);
    }


    public function update(Request $request, $id)
    {
        // cek apakah user sudah terdaftar sebagai penjual
        $seller = Seller::where('id', $id)->first();

        if (!$seller) {
            return response()->json([
                'error' => 'Seller not found.',
            ], 404);
        }

        // validasi input
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|max:255',
            'description' => 'nullable',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'     => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // upload file logo jika ada
        $randomLogoName = $seller->logo;
        if ($request->hasFile('logo')) {
            // hapus logo lama jika ada
            if ($seller->logo) {
                Storage::disk('public')->delete('seller/logos/' . $seller->logo);
            }

            $file = $request->file('logo');
            $fileExtension = $file->getClientOriginalExtension();
            $randomLogoName = Str::random(40) . '.' . $fileExtension;

            $file->storeAs('seller/logos', $randomLogoName, 'public');
        }

        // upload file banner jika ada
        $randomBannerName = $seller->banner;
        if ($request->hasFile('banner')) {
            // hapus banner lama jika ada
            if ($seller->banner) {
                Storage::disk('public')->delete('seller/banners/' . $seller->banner);
            }

            $file = $request->file('banner');
            $fileExtension = $file->getClientOriginalExtension();
            $randomBannerName = Str::random(40) . '.' . $fileExtension;

            $file->storeAs('seller/banners', $randomBannerName, 'public');
        }

        // update data seller
        $seller->update([
            'brand_name' => $request->brand_name,
            'logo'       => $randomLogoName,
            'banner'     => $randomBannerName,
            'status'     => $seller->status,
        ]);

        return response()->json(['success' => true, 'seller' => $seller], 200);
    }


    public function destroy($id)
    {
        // cek apakah seller dengan id yang diberikan ada
        $seller = Seller::where('id', $id)->first();

        if (!$seller) {
            return response()->json([
                'error' => 'Seller not found.',
            ], 404);
        }

        // hapus file logo jika ada
        if ($seller->logo) {
            Storage::disk('public')->delete('seller/logos/' . $seller->logo);
        }

        // hapus file banner jika ada
        if ($seller->banner) {
            Storage::disk('public')->delete('seller/banners/' . $seller->banner);
        }

        // hapus data seller dari database
        $seller->delete();

        return response()->json(['success' => true, 'message' => 'Seller deleted successfully'], 200);
    }
}
