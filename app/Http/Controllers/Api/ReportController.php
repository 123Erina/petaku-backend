<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\Report;
    use Illuminate\Http\Request;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Validator;

    class ReportController extends Controller
    {
        /**
         * Kategori laporan bersifat tetap (dipakai popup Call Center di peta).
         * 1 = Banjir, 2 = Jalan Rusak, 3 = Kemacetan
         */
        private const KATEGORI_LABELS = [
            1 => 'Banjir',
            2 => 'Jalan Rusak',
            3 => 'Kemacetan',
        ];

        /**
         * GET /api/import-report (public, existing — tidak diubah)
         */
        public function import()
        {
            for ($kategori = 1; $kategori <= 3; $kategori++) {

                $response = Http::withoutVerifying()->get(
                    "https://petaku.sidoarjokab.go.id/api/guest/maps/getlaporcc/$kategori"
                );

                if (!$response->successful()) {
                    continue;
                }

                foreach ($response->json() as $item) {

                    Report::updateOrCreate(
                        [
                            'old_id' => $item['id']
                        ],
                        [
                            'no_laporan' => $item['no_laporan'],
                            'waktu_lapor' => $item['waktu_lapor'],
                            'kategori' => $item['kategori'],
                            'id_kategori' => $item['id_kategori'],
                            'deskripsi' => $item['deskripsi'],
                            'lokasi' => $item['lokasi'],
                            'kecamatan' => $item['kecamatan'],
                            'kelurahan' => $item['kelurahan'],
                            'catatan' => $item['catatan'],
                            'latitude' => $item['lat'],
                            'longitude' => $item['lng'],
                        ]
                    );
                }
            }

            return response()->json([
                'message' => 'Import laporan selesai'
            ]);
        }

        /**
         * GET /api/report/{kategori} (public, existing — dipakai peta, tidak diubah)
         */
        public function getReport($kategori)
        {
            return Report::where('id_kategori', $kategori)
                ->get()
                ->map(function ($item) {

                    return [
                        'id' => $item->old_id,
                        'no_laporan' => $item->no_laporan,
                        'waktu_lapor' => $item->waktu_lapor,
                        'kategori' => $item->kategori,
                        'deskripsi' => $item->deskripsi,
                        'lokasi' => $item->lokasi,
                        'kecamatan' => $item->kecamatan,
                        'kelurahan' => $item->kelurahan,
                        'catatan' => $item->catatan,
                        'lat' => $item->latitude,
                        'lng' => $item->longitude,
                        'id_kategori' => $item->id_kategori,
                    ];
                });
        }

        /**
         * GET /api/reports (admin, baru)
         * Query param opsional: ?id_kategori=1
         */
        public function index(Request $request): JsonResponse
        {
            $query = Report::query()->orderByDesc('waktu_lapor');

            if ($request->filled('id_kategori')) {
                $query->where('id_kategori', $request->input('id_kategori'));
            }

            return response()->json([
                'success' => true,
                'data' => $query->get(),
                'kategori_options' => self::KATEGORI_LABELS,
            ]);
        }

        /**
         * GET /api/reports/{id} (admin, baru)
         */
        public function show(int $id): JsonResponse
        {
            $report = Report::find($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);
        }

        /**
         * POST /api/reports (admin, baru)
         */
        public function store(Request $request): JsonResponse
        {
            $validator = Validator::make($request->all(), [
                'no_laporan' => ['required', 'string', 'max:100'],
                'waktu_lapor' => ['required', 'string', 'max:100'],
                'id_kategori' => ['required', 'integer', 'in:1,2,3'],
                'deskripsi' => ['required', 'string'],
                'lokasi' => ['required', 'string', 'max:255'],
                'kecamatan' => ['nullable', 'string', 'max:100'],
                'kelurahan' => ['nullable', 'string', 'max:100'],
                'catatan' => ['nullable', 'string'],
                'latitude' => ['required', 'string', 'max:50'],
                'longitude' => ['required', 'string', 'max:50'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $data['kategori'] = self::KATEGORI_LABELS[$data['id_kategori']];

            $report = Report::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil ditambahkan.',
                'data' => $report,
            ], 201);
        }

        /**
         * PUT /api/reports/{id} (admin, baru)
         */
        public function update(Request $request, int $id): JsonResponse
        {
            $report = Report::find($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'no_laporan' => ['required', 'string', 'max:100'],
                'waktu_lapor' => ['required', 'string', 'max:100'],
                'id_kategori' => ['required', 'integer', 'in:1,2,3'],
                'deskripsi' => ['required', 'string'],
                'lokasi' => ['required', 'string', 'max:255'],
                'kecamatan' => ['nullable', 'string', 'max:100'],
                'kelurahan' => ['nullable', 'string', 'max:100'],
                'catatan' => ['nullable', 'string'],
                'latitude' => ['required', 'string', 'max:50'],
                'longitude' => ['required', 'string', 'max:50'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $data['kategori'] = self::KATEGORI_LABELS[$data['id_kategori']];

            $report->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diperbarui.',
                'data' => $report,
            ]);
        }

        /**
         * DELETE /api/reports/{id} (admin, baru)
         */
        public function destroy(int $id): JsonResponse
        {
            $report = Report::find($id);

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan.',
                ], 404);
            }

            $report->delete();

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dihapus.',
            ]);
        }
    }
