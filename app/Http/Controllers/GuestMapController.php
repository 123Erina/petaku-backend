<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;

class GuestMapController extends Controller
{
    public function getPlaces($category)
    {
        return Place::where('category_id', $category)
            ->get()
            ->map(function ($place) {
                return [
                    'id_place' => $place->id,
                    'nama' => $place->nama,
                    'alamat' => $place->alamat,
                    'lat' => $place->latitude,
                    'lng' => $place->longitude,
                    'url_gambar' => $place->gambar,
                    'id_kategori' => $place->category_id,
                    'kategori' => optional($place->category)->nama,
                ];
            });
    }
}
