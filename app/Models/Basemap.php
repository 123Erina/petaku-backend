<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basemap extends Model
{
     protected $fillable = [
        'jenis',
        'geojson',
    ];
}
