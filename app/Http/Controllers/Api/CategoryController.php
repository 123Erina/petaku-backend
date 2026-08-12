<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * ID kategori yang dipakai hardcode di filter peta (PetaView.vue).
     * Jangan diubah urutannya tanpa update kode frontend juga.
     */
    private const PROTECTED_CATEGORY_IDS = [1, 2, 3, 4, 5, 6, 7, 8];

    /**
     * GET /api/categories
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Category::orderBy('nama')->get(),
        ]);
    }

    /**
     * GET /api/categories/{id}
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * POST /api/categories
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama'  => ['required', 'string', 'max:255'],
            'icon'  => ['nullable', 'string', 'max:255'],
            'warna' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $category = Category::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    /**
     * PUT /api/categories/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama'  => ['required', 'string', 'max:255'],
            'icon'  => ['nullable', 'string', 'max:255'],
            'warna' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }


        $category->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    /**
     * DELETE /api/categories/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 404);
        }

        if (in_array($id, self::PROTECTED_CATEGORY_IDS, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori ini masih digunakan oleh sistem filter peta dan tidak bisa dihapus.',
            ], 422);
        }

        // Jaga-jaga: cegah hapus kategori yang masih dipakai tempat (places)
        if ($category->places()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori ini masih digunakan oleh data tempat dan tidak bisa dihapus.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
