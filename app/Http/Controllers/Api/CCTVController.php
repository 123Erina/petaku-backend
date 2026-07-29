<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CCTV;
use Illuminate\Support\Facades\Http;

class CCTVController extends Controller
{
    public function import()
    {
        $response = Http::withoutVerifying()
            ->get('https://petaku.sidoarjokab.go.id/api/guest/maps/getcctv');

        foreach ($response->json() as $item) {

            CCTV::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'lokasi' => $item['lokasi'],
                    'latitude' => $item['lat'],
                    'longitude' => $item['lng'],
                    'link' => $item['link'],
                ]
            );
        }

        return response()->json([
            'message' => 'Import CCTV berhasil'
        ]);
    }

    public function index()
    {
        return CCTV::all()->map(function ($item){

            return [

                'id'=>$item->id,
                'nama'=>$item->nama,
                'lokasi'=>$item->lokasi,
                'lat'=>$item->latitude,
                'lng'=>$item->longitude,
                'link'=>$item->link

            ];

        });
    }
}
