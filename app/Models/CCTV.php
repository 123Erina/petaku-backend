<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CCTV extends Model
{
    protected $table = 'cctvs';
    protected $fillable = [
        'nama',
        'lokasi',
        'latitude',
        'longitude',
        'link'
    ];
}
