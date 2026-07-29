<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama' => 'Instansi', 'icon' => 'fa-building', 'warna' => '#2563eb'],
            ['nama' => 'Puskesmas', 'icon' => 'fa-hospital', 'warna' => '#22c55e'],
            ['nama' => 'Rumah Sakit', 'icon' => 'fa-hospital', 'warna' => '#ef4444'],
            ['nama' => 'Kantor Desa', 'icon' => 'fa-landmark', 'warna' => '#10b981'],
            ['nama' => 'SD', 'icon' => 'fa-school', 'warna' => '#3b82f6'],
            ['nama' => 'SMP', 'icon' => 'fa-school', 'warna' => '#f59e0b'],
            ['nama' => 'SMA', 'icon' => 'fa-school', 'warna' => '#8b5cf6'],
            ['nama' => 'SMK', 'icon' => 'fa-school', 'warna' => '#14b8a6'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
