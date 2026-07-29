<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenter extends Model
{
    protected $fillable = [
        'no_laporan',
        'waktu_lapor',
        'id_kategori',
        'kategori',
        'deskripsi',
        'lokasi',
        'kecamatan',
        'kelurahan',
        'catatan',
        'latitude',
        'longitude',
    ];
}
