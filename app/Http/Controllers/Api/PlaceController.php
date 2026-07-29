<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index(Request $request)
{
    $query = Place::with('category');

    if ($request->category) {
        $query->where('category_id', $request->category);
    }

    $data = $query->get()->map(function ($item) {
        return [
            'id_place' => $item->id,
            'nama' => $item->nama,
            'alamat' => $item->alamat,
            'lat' => $item->latitude,
            'lng' => $item->longitude,
            'url_gambar' => $item->gambar_url,
            'id_kategori' => $item->category_id,
            'kategori' => $item->category?->nama,
        ];
    });

    return response()->json($data);
}

   public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'nama' => 'required',
        'latitude' => 'required',
        'longitude' => 'required',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $data = $request->all();

    if ($request->hasFile('gambar')) {

        $path = $request->file('gambar')->store('places','public');

        $data['gambar'] = $path;
    }

    $place = Place::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Data berhasil ditambahkan',
        'data' => $place
    ]);
}

    public function show($id)
    {
    $place = Place::with('category')->findOrFail($id);

    return response()->json([
        'success' => true,
        'data' => $place
    ]);
    }

    public function update(Request $request, $id)
{
    $place = Place::findOrFail($id);

    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'nama' => 'required',
        'latitude' => 'required',
        'longitude' => 'required',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $data = $request->all();

    if ($request->hasFile('gambar')) {

        if ($place->gambar) {
            Storage::disk('public')->delete($place->gambar);
        }

        $data['gambar'] = $request
            ->file('gambar')
            ->store('places','public');
    }

    $place->update($data);

    return response()->json([
        'success'=>true,
        'message'=>'Data berhasil diupdate',
        'data'=>$place
    ]);
}


    public function destroy($id)
{
    $place = Place::findOrFail($id);

    if ($place->gambar) {
        Storage::disk('public')->delete($place->gambar);
    }

    $place->delete();

    return response()->json([
        'success'=>true,
        'message'=>'Data berhasil dihapus'
    ]);
}
    public function getPlaces($category)
{
    return Place::where('category_id', $category)
        ->get()
        ->map(function ($place) {

            return [

                'id_place' => $place->old_id,
                'nama' => $place->nama,
                'alamat' => $place->alamat,
                'lat' => $place->latitude,
                'lng' => $place->longitude,
                'url_gambar' => $place->gambar,
                'id_kategori' => $place->category_id,

            ];

        });
}


}


