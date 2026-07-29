<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OpdController extends Controller
{
    public function index()
    {
        return response()->json(Opd::all());
    }

    public function show($id)
    {
        $opd = Opd::findOrFail($id);
        return response()->json($opd);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'alamat'    => 'nullable|string|max:255',
            'logo'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('opds', 'public');
        }

        $opd = Opd::create($validated);

        return response()->json($opd, 201);
    }

    public function update(Request $request, $id)
    {
        $opd = Opd::findOrFail($id);

        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'alamat'    => 'nullable|string|max:255',
            'logo'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($opd->logo) {
                Storage::disk('public')->delete($opd->logo);
            }
            $validated['logo'] = $request->file('logo')->store('opds', 'public');
        }

        $opd->update($validated);

        return response()->json($opd);
    }

    public function destroy($id)
    {
        $opd = Opd::findOrFail($id);

        if ($opd->logo) {
            Storage::disk('public')->delete($opd->logo);
        }

        $opd->delete();

        return response()->json(['message' => 'OPD berhasil dihapus']);
    }
}
