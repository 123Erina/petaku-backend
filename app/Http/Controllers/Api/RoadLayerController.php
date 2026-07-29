<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoadLayer;
use Illuminate\Support\Facades\Http;

class RoadLayerController extends Controller
{
    public function import()
    {
        $files = [
            'pemeliharaan' => 'https://petaku.sidoarjokab.go.id/assets/geojson/pemeliharaan-jalan.json',
            'masterplan'   => 'https://petaku.sidoarjokab.go.id/assets/geojson/masterplan-jalan.json',
        ];

        foreach ($files as $jenis => $url) {

            $response = Http::withoutVerifying()->get($url);

            if (!$response->successful()) {
                continue;
            }

            RoadLayer::updateOrCreate(
                [
                    'jenis' => $jenis
                ],
                [
                    'data' => $response->body()
                ]
            );
        }

        return response()->json([
            'message' => 'Import jalan berhasil'
        ]);
    }

    public function get($jenis)
    {
        $data = RoadLayer::where('jenis', $jenis)->first();

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ],404);
        }

        return response(
            $data->data,
            200,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }
}
