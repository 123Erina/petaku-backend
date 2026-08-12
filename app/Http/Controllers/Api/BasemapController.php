<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Basemap;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use geoPHP;

class BasemapController extends Controller
{
    /**
     * Jenis basemap bersifat tetap (dipakai method get() & peta).
     */
    private const JENIS_LABELS = [
        'kabupaten' => 'Kabupaten',
        'kecamatan' => 'Kecamatan',
        'desa' => 'Desa',
    ];

    /**
     * GET /api/import-basemap (public, existing — tidak diubah)
     */
    public function import()
    {
        $files = [
            'kabupaten' => 'https://petaku.sidoarjokab.go.id/assets/geojson/35.15_kabupaten.geojson',
            'kecamatan' => 'https://petaku.sidoarjokab.go.id/assets/geojson/35.15_kecamatan.geojson',
            'desa' => 'https://petaku.sidoarjokab.go.id/assets/geojson/35.15_kelurahan.geojson',
        ];

        foreach ($files as $jenis => $url) {

            $response = Http::withoutVerifying()->get($url);

            Basemap::updateOrCreate(
                ['jenis' => $jenis],
                [
                    'geojson' => $response->body(),
                ]
            );
        }

        return response()->json([
            'message' => 'Import berhasil',
        ]);
    }

    /**
     * GET /api/basemap/{jenis} (public, existing — dipakai peta, tidak diubah)
     */
    public function get($jenis)
    {
        $url = '';

        switch ($jenis) {

            case 'kabupaten':
                $url = 'https://petaku.sidoarjokab.go.id/api/guest/maps/getPetaKab/5';
                break;

            case 'kecamatan':
                $url = 'https://petaku.sidoarjokab.go.id/api/guest/maps/getPetaKec/6';
                break;

            case 'desa':
                $url = 'https://petaku.sidoarjokab.go.id/api/guest/maps/getPetaDesa/7';
                break;

            default:
                return response()->json([
                    'message' => 'Jenis tidak ditemukan'
                ], 404);
        }

        $response = Http::withoutVerifying()->get($url);

        $items = $response->json();

        $features = [];

        foreach ($items as $item) {

            $wkt = str_replace(
                ['MULTIPOLYGON Z', 'POLYGON Z'],
                ['MULTIPOLYGON', 'POLYGON'],
                $item['lokasi']
            );

            $geometry = geoPHP::load($wkt, 'wkt');

            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($geometry->out('json')),
                'properties' => $item
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    /**
     * GET /api/basemaps (admin, baru)
     * Tidak mengembalikan isi geojson penuh (bisa sangat besar),
     * cukup ukurannya saja. Ambil geojson lengkap lewat show().
     */
    public function index(): JsonResponse
    {
        $data = Basemap::orderBy('jenis')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'jenis' => $item->jenis,
                'jenis_label' => self::JENIS_LABELS[$item->jenis] ?? $item->jenis,
                'geojson_size' => strlen($item->geojson ?? ''),
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'jenis_options' => self::JENIS_LABELS,
        ]);
    }

    /**
     * GET /api/basemaps/{id} (admin, baru)
     * Mengembalikan record lengkap termasuk isi geojson.
     */
    public function show(int $id): JsonResponse
    {
        $basemap = Basemap::find($id);

        if (!$basemap) {
            return response()->json([
                'success' => false,
                'message' => 'Data basemap tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $basemap,
        ]);
    }

    /**
     * POST /api/basemaps (admin, baru)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'jenis' => [
                'required',
                'string',
                Rule::in(array_keys(self::JENIS_LABELS)),
                Rule::unique('basemaps', 'jenis'),
            ],
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

        $basemap = Basemap::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data basemap berhasil ditambahkan.',
            'data' => $basemap,
        ], 201);
    }

    /**
     * PUT /api/basemaps/{id} (admin, baru)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $basemap = Basemap::find($id);

        if (!$basemap) {
            return response()->json([
                'success' => false,
                'message' => 'Data basemap tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'jenis' => [
                'required',
                'string',
                Rule::in(array_keys(self::JENIS_LABELS)),
                Rule::unique('basemaps', 'jenis')->ignore($basemap->id),
            ],
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

        $basemap->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data basemap berhasil diperbarui.',
            'data' => $basemap,
        ]);
    }

    /**
     * DELETE /api/basemaps/{id} (admin, baru)
     */
    public function destroy(int $id): JsonResponse
    {
        $basemap = Basemap::find($id);

        if (!$basemap) {
            return response()->json([
                'success' => false,
                'message' => 'Data basemap tidak ditemukan.',
            ], 404);
        }

        $basemap->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data basemap berhasil dihapus.',
        ]);
    }
}
