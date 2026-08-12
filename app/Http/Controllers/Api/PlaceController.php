<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Public listing (dipakai guest map). Tidak difilter opd.
     */
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

    /**
     * Listing khusus dashboard admin.
     * Super Admin: lihat semua tempat.
     * Admin OPD: cuma lihat tempat milik opd_id-nya sendiri.
     *
     * Dipaginasi 50 data per halaman biar loading lebih cepat
     * (sebelumnya query gabungan 3 kategori bikin data yang di-load kebanyakan sekaligus).
     */
    public function adminIndex(Request $request)
    {
        $user = $request->user();

        $query = Place::with(['category', 'opd']);

        if ($user->role !== 'super_admin') {
            $query->where('opd_id', $user->opd_id);
        }

        $perPage = 50;

        return response()->json(
            $query->latest()->paginate($perPage)
        );
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'category_id' => 'required|exists:categories,id',
            'nama' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        // Super admin wajib pilih OPD tujuan; admin OPD otomatis pakai opd_id dia sendiri
        if ($user->role === 'super_admin') {
            $rules['opd_id'] = 'required|exists:opds,id';
        }

        $request->validate($rules);

        $data = $request->except('gambar');
        $data['opd_id'] = $user->role === 'super_admin' ? $request->opd_id : $user->opd_id;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('places', 'public');
        }

        $place = Place::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan',
            'data' => $place,
        ]);
    }

    public function show($id)
    {
        $place = Place::with(['category', 'opd'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $place,
        ]);
    }

    public function update(Request $request, $id)
    {
        $place = Place::findOrFail($id);
        $user = $request->user();

        // Admin OPD cuma boleh edit tempat milik OPD-nya sendiri
        if ($user->role !== 'super_admin' && $place->opd_id !== $user->opd_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses untuk mengubah data ini',
            ], 403);
        }

        $rules = [
            'category_id' => 'required|exists:categories,id',
            'nama' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        if ($user->role === 'super_admin') {
            $rules['opd_id'] = 'required|exists:opds,id';
        }

        $request->validate($rules);

        $data = $request->except('gambar');

        // Admin OPD tidak boleh pindahkan tempat ke OPD lain
        if ($user->role !== 'super_admin') {
            $data['opd_id'] = $user->opd_id;
        }

        if ($request->hasFile('gambar')) {
            if ($place->gambar) {
                Storage::disk('public')->delete($place->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('places', 'public');
        }

        $place->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $place,
        ]);
    }

    public function destroy($id, Request $request)
    {
        $place = Place::findOrFail($id);
        $user = $request->user();

        if ($user->role !== 'super_admin' && $place->opd_id !== $user->opd_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak punya akses untuk menghapus data ini',
            ], 403);
        }

        if ($place->gambar) {
            Storage::disk('public')->delete($place->gambar);
        }

        $place->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }

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
                    'url_gambar' => $place->gambar_url,
                    'id_kategori' => $place->category_id,
                ];
            });
    }
}
