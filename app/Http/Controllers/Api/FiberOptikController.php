<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FiberOptik;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FiberOptikController extends Controller
{
    /**
     * Label jalur bersifat tetap (dipakai referensi di admin panel).
     * 1 = Backbone, 2 = FTTX Sidoarjo, 3 = FTTX Sukodono, 4 = FTTX Porong
     */
    private const JALUR_LABELS = [
        1 => 'Backbone',
        2 => 'FTTX Sidoarjo',
        3 => 'FTTX Sukodono',
        4 => 'FTTX Porong',
    ];

    /**
     * GET /api/import-fiber (public, existing — tidak diubah)
     */
    public function import()
    {
        $urls = [
            1 => "https://petaku.sidoarjokab.go.id/assets/geojson/jalurfo-backbone.json",
            2 => "https://petaku.sidoarjokab.go.id/assets/geojson/jalurfo-fttx-sidoarjo.json",
            3 => "https://petaku.sidoarjokab.go.id/assets/geojson/jalurfo-fttx-sukodono.json",
            4 => "https://petaku.sidoarjokab.go.id/assets/geojson/jalurfo-fttx-porong.json",
        ];

        foreach ($urls as $jalur => $url) {

            $response = Http::withoutVerifying()->get($url);

            FiberOptik::updateOrCreate(
                ['jalur' => $jalur],
                [
                    'nama' => basename($url),
                    'geojson' => $response->body(),
                ]
            );
        }

        return response()->json([
            'message' => 'Import berhasil'
        ]);
    }

    /**
     * GET /api/fiber-optik/{jalur} (public, existing — dipakai peta, tidak diubah)
     */
    public function getFiber($jalur)
    {
        $data = FiberOptik::where('jalur', $jalur)->first();

        return response()->json(
            json_decode($data->geojson)
        );
    }

    /**
     * GET /api/fiber-optiks (admin, baru)
     * Tidak mengembalikan isi geojson penuh (bisa sangat besar),
     * cukup ukurannya saja. Ambil geojson lengkap lewat show().
     */
    public function index(): JsonResponse
    {
        $data = FiberOptik::orderBy('jalur')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'jalur' => $item->jalur,
                'jalur_label' => self::JALUR_LABELS[$item->jalur] ?? '-',
                'geojson_size' => strlen($item->geojson ?? ''),
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'jalur_options' => self::JALUR_LABELS,
        ]);
    }

    /**
     * GET /api/fiber-optiks/{id} (admin, baru)
     * Mengembalikan record lengkap termasuk isi geojson.
     */
    public function show(int $id): JsonResponse
    {
        $fiber = FiberOptik::find($id);

        if (!$fiber) {
            return response()->json([
                'success' => false,
                'message' => 'Data fiber optik tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $fiber,
        ]);
    }

    /**
     * POST /api/fiber-optiks (admin, baru)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'jalur' => ['required', 'integer', Rule::unique('fiber_optiks', 'jalur')],
            'geojson' => ['required', 'string'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('geojson') && json_decode($request->input('geojson')) === null && json_last_error() !== JSON_ERROR_NONE) {
                $validator->errors()->add('geojson', 'Format geojson tidak valid (harus JSON valid).');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $fiber = FiberOptik::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data fiber optik berhasil ditambahkan.',
            'data' => $fiber,
        ], 201);
    }

    /**
     * PUT /api/fiber-optiks/{id} (admin, baru)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $fiber = FiberOptik::find($id);

        if (!$fiber) {
            return response()->json([
                'success' => false,
                'message' => 'Data fiber optik tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'jalur' => ['required', 'integer', Rule::unique('fiber_optiks', 'jalur')->ignore($fiber->id)],
            'geojson' => ['required', 'string'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('geojson') && json_decode($request->input('geojson')) === null && json_last_error() !== JSON_ERROR_NONE) {
                $validator->errors()->add('geojson', 'Format geojson tidak valid (harus JSON valid).');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $fiber->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data fiber optik berhasil diperbarui.',
            'data' => $fiber,
        ]);
    }

    /**
     * DELETE /api/fiber-optiks/{id} (admin, baru)
     */
    public function destroy(int $id): JsonResponse
    {
        $fiber = FiberOptik::find($id);

        if (!$fiber) {
            return response()->json([
                'success' => false,
                'message' => 'Data fiber optik tidak ditemukan.',
            ], 404);
        }

        $fiber->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data fiber optik berhasil dihapus.',
        ]);
    }
}
