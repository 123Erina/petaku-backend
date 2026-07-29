<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Category;
use App\Models\Place;

class PetakuImportCommand extends Command
{
    protected $signature = 'petaku:import';

    protected $description = 'Import data Petaku lama';

    public function handle()
    {
        $this->importPlaces();

        $this->importKantorDesa();

        $this->importSekolah(1, 5, "SD");
        $this->importSekolah(2, 6, "SMP");
        $this->importSekolah(3, 7, "SMA");
        $this->importSekolah(4, 8, "SMK");

        $this->info("Selesai.");
    }

    private function importPlaces()
    {
        $categories = Category::whereIn('id', [1, 2, 3])->get();

        $map = [
            1 => 15, // Instansi
            2 => 13, // Puskesmas
            3 => 14, // Rumah Sakit
        ];

        foreach ($categories as $category) {

            $this->info("Import {$category->nama}");

            $response = Http::withoutVerifying()->get(
                "https://petaku.sidoarjokab.go.id/api/guest/maps/getPlaces/" . $map[$category->id]
            );

            if (!$response->successful()) {
                $this->error("Gagal import {$category->nama}");
                continue;
            }

            foreach ($response->json() as $item) {

                Place::updateOrCreate(
                    [
                        'old_id' => $item['id_place']
                    ],
                    [
                        'nama' => $item['nama'],
                        'category_id' => $category->id,
                        'alamat' => $item['alamat'],
                        'latitude' => $item['lat'],
                        'longitude' => $item['lng'],
                    ]
                );
            }

            $this->info(count($response->json()) . " data berhasil");
        }
    }

    private function importKantorDesa()
    {
        $this->info("Import Kantor Desa");

        $response = Http::withoutVerifying()->get(
            "https://petaku.sidoarjokab.go.id/api/guest/maps/getKantorDesa/0"
        );

        if (!$response->successful()) {
            return;
        }

        $data = $response->json();

        foreach ($data as $item) {

            preg_match('/MULTIPOINT Z \(([\d\.\-]+) ([\d\.\-]+)/', $item['lokasi'], $hasil);

            if (!$hasil) {
                continue;
            }

            Place::updateOrCreate(
                [
                    'nama' => $item['nama_desa'],
                    'category_id' => 4,
                ],
                [
                    'alamat' => $item['nama_kec'],
                    'latitude' => $hasil[2],
                    'longitude' => $hasil[1],
                ]
            );
        }

        $this->info(count($data) . " Kantor Desa berhasil");
    }

    private function importSekolah($apiKategori, $categoryId, $nama)
    {
        $this->info("Import " . $nama);

        $response = Http::withoutVerifying()->get(
            "https://petaku.sidoarjokab.go.id/api/guest/maps/getSekolah/" . $apiKategori
        );

        if (!$response->successful()) {
            return;
        }

        $data = $response->json();

        foreach ($data as $item) {

            Place::updateOrCreate(
                [
                    'nama' => $item['nama'],
                    'category_id' => $categoryId,
                ],
                [
                    'alamat' => null,
                    'latitude' => $item['lat'],
                    'longitude' => $item['lng'],
                ]
            );
        }

        $this->info(count($data) . " " . $nama . " berhasil");
    }
}
