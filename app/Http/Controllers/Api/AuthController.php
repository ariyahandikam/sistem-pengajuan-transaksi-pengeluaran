<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login dan dapatkan token akses.
     *
     * POST /api/login
     * Body: { "email": "...", "password": "..." }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password yang Anda masukkan salah.'],
            ]);
        }

        // Hapus token lama jika ada (opsional, untuk single-session)
        // $user->tokens()->delete();

        $token = $user->createToken(
            $request->device_name ?? 'api-token'
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role?->name,
                    'role_slug' => $user->roleSlug,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout dan hapus token saat ini.
     *
     * POST /api/logout
     * Header: Authorization: Bearer {token}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dihapus.',
        ]);
    }

    /**
     * Mendapatkan profil user yang sedang login.
     *
     * GET /api/me
     * Header: Authorization: Bearer {token}
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json([
            'success' => true,
            'data'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role?->name,
                'role_slug'  => $user->roleSlug,
                'status'     => $user->status,
                'created_at' => $user->created_at?->toISOString(),
            ],
        ]);
    }
}
