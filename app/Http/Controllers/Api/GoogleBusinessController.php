<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleBusiness;
use Illuminate\Support\Facades\Http;

class GoogleBusinessController extends Controller
{
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

    public function getBusiness($kategori)
    {
        return GoogleBusiness::where('kategori', $kategori)
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
    }
}
