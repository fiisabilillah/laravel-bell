<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Fungsi untuk login
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validasi input yang dikirimkan
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari pengguna berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Jika pengguna tidak ditemukan, kirimkan respons error
        if (!$user) {
            return response()->json([
                'message' => 'Email not found',
            ], 404);
        }

        // Cek apakah password cocok
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password is incorrect',
            ], 401);
        }

        // Jika login berhasil, buat token
        $token = $user->createToken('LaravelApp')->plainTextToken;

        // Kembalikan respons dengan data pengguna dan token
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Fungsi untuk logout
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Hapus token autentikasi pengguna yang sedang login
        $request->user()->currentAccessToken()->delete();

        // Kembalikan respons sukses logout
        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
