<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Ambil data profil user yang sedang login.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
            'role'     => $user->role, // sesuaikan kalau role disimpan beda (misal via relasi/spatie-permission)
        ]);
    }

    /**
     * PUT /api/profile
     * Update nama & email user yang sedang login.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'name'    => $user->name,
            'email'   => $user->email,
        ]);
    }

    /**
     * PUT /api/profile/password
     * Ubah password user yang sedang login.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password'          => ['required', 'string'],
            'new_password'               => ['required', 'string', 'min:8', 'confirmed'],
            // Laravel otomatis cocokkan `new_password` dengan `new_password_confirmation`
            // karena pakai rule 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'message' => 'Password saat ini tidak sesuai.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
