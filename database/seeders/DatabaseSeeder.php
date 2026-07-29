<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Jalankan seeder OPD
        $this->call([
            OpdSeeder::class,
        ]);

        // User default
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@petaku.id',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'opd_id' => 1,
        ]);
    }
}
