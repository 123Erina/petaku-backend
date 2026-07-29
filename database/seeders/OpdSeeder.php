<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Opd;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            ['nama'=>'Diskominfo'],

            ['nama'=>'PU Bina Marga'],

            ['nama'=>'Dishub'],

            ['nama'=>'BPBD'],

            ['nama'=>'DPMPTSP'],

            ['nama'=>'Dinas Pendidikan'],

            ['nama'=>'Dinas Kesehatan'],

            ['nama'=>'Pemerintah Desa'],

        ];

        foreach($data as $item){

            Opd::create($item);

        }
    }
}
