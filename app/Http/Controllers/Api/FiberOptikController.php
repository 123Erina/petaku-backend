<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\FiberOptik;

class FiberOptikController extends Controller
{
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

public function getFiber($jalur)
{
    $data = FiberOptik::where('jalur',$jalur)->first();

    return response()->json(
        json_decode($data->geojson)
    );
}
}
