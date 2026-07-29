<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleBusiness extends Model
{
    protected $fillable = [
    'nama',
    'alamat',
    'latitude',
    'longitude',
    'range_harga',
    'nomor_telp',
    'kategori'
];
}
