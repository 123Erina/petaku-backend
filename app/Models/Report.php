<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'old_id',
        'no_laporan',
        'waktu_lapor',
        'kategori',
        'id_kategori',
        'deskripsi',
        'lokasi',
        'kecamatan',
        'kelurahan',
        'catatan',
        'latitude',
        'longitude',
    ];
}
