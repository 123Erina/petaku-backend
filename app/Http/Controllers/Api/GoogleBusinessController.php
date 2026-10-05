<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleBusiness;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class GoogleBusinessController extends Controller
{
    /**
     * GET /api/import-google-business (public, existing — tidak diubah)
     */
    public function import()
    {
        for ($kategori = 1; $kategori <= 4; $kategori++) {

            $response = Http::withOptions([
                'verify' => false,
            ])->get(
                "https://petaku.sidoarjokab.go.id/api/guest/maps/getgooglebussiness/$kategori"
            );

            if (!$response->successful()) {
                continue;
            }

            foreach ($response->json() as $item) {

                GoogleBusiness::create([
                    'nama' => $item['nama'],
                    'alamat' => $item['alamat'],
                    'latitude' => $item['lat'],
                    'longitude' => $item['lng'],
                    'range_harga' => $item['range_harga'],
                    'nomor_telp' => $item['nomor_telp'],
                    'kategori' => $item['kategori'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Import selesai'
        ]);
    }

    /**
     * GET /api/google-business/{kategori} (public, dipakai peta)
     *
     * DIUBAH: tidak dipaginasi lagi. Endpoint ini dipakai untuk menampilkan
     * semua marker satu kategori sekaligus di peta, jadi harus return
     * semua data dalam satu response (array), bukan dibagi per halaman.
     */
    public function getBusiness(Request $request, $kategori)
    {
        $data = GoogleBusiness::where('kategori', $kategori)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->nama,
                    'alamat' => $item->alamat,
                    'lat' => $item->latitude,
                    'lng' => $item->longitude,
                    'range_harga' => $item->range_harga,
                    'nomor_telp' => $item->nomor_telp,
                    'kategori' => $item->kategori,
                ];
            });

        return response()->json($data);
    }

    /**
     * GET /api/google-businesses (admin, baru) — TIDAK DIUBAH
     * Query param opsional: ?kategori=Kuliner&page=N
     */
    public function index(Request $request): JsonResponse
    {
        $query = GoogleBusiness::query()->orderBy('nama');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        $kategoriOptions = GoogleBusiness::query()
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $perPage = 100;
        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'total' => $paginated->total(),
            'kategori_options' => $kategoriOptions,
        ]);
    }

    /**
     * GET /api/google-businesses/{id} (admin, baru) — TIDAK DIUBAH
     */
    public function show(int $id): JsonResponse
    {
        $business = GoogleBusiness::find($id);

        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Data google business tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $business,
        ]);
    }

    /**
     * POST /api/google-businesses (admin, baru) — TIDAK DIUBAH
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'string', 'max:50'],
            'longitude' => ['required', 'string', 'max:50'],
            'range_harga' => ['nullable', 'string', 'max:100'],
            'nomor_telp' => ['nullable', 'string', 'max:50'],
            'kategori' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $business = GoogleBusiness::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data google business berhasil ditambahkan.',
            'data' => $business,
        ], 201);
    }

    /**
     * PUT /api/google-businesses/{id} (admin, baru) — TIDAK DIUBAH
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $business = GoogleBusiness::find($id);

        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Data google business tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'string', 'max:50'],
            'longitude' => ['required', 'string', 'max:50'],
            'range_harga' => ['nullable', 'string', 'max:100'],
            'nomor_telp' => ['nullable', 'string', 'max:50'],
            'kategori' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $business->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data google business berhasil diperbarui.',
            'data' => $business,
        ]);
    }

    /**
     * DELETE /api/google-businesses/{id} (admin, baru) — TIDAK DIUBAH
     */
    public function destroy(int $id): JsonResponse
    {
        $business = GoogleBusiness::find($id);

        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Data google business tidak ditemukan.',
            ], 404);
        }

        $business->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data google business berhasil dihapus.',
        ]);
    }
}