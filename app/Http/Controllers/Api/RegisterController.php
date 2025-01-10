<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use function Termwind\ask;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:255',
            'email'    => 'required|email|unique:users|max:255',
            'password'  => 'required|min:8',
            'phone'    => 'required|unique:users|max:15',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender'    => 'required|in:male,female',
        ], [
            'gender.in' => 'The gender must be either male or female.',
        ]);

        //jika validasi tidak berhasil
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // upload file foto jika ada
        $randomFileName = 'default.png';
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            $fileExtension = $file->getClientOriginalExtension();
            $randomFileName = Str::random(40) . '.' . $fileExtension;

            $file->storeAs('profilePictures', $randomFileName, 'public');
        }

        //jika valid, maka simpan data user
        $user = User::create([
            'username' => $request->username,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'phone'    => $request->phone,
            'profile_picture' => $randomFileName,
            'gender'   => $request->gender,
            'role'     => 'buyer',
        ]);

        //jika berhasil tersimpan, return response
        if ($user) {
            $user->profile_picture_url = url('/storage/profilePictures/' . $user->profile_picture);

            return response()->json([
                'success' => true,
                'data'    => $user,
            ], 201);
        }

        // jika gagal
        return response()->json([
            'success' => false,
        ], 409);
    }
}
