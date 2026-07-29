<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;   // <-- TAMBAHKAN INI
use App\Models\Basemap;
use Illuminate\Support\Facades\Http;
use geoPHP;


class BasemapController extends Controller
{
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
            ],404);
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
}
