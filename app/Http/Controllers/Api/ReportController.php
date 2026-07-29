<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
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
}
