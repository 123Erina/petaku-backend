<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    protected $fillable = [
        'category_id',
        'old_id',
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'deskripsi',
        'gambar',
        'website',
        'telepon',
        'status'
    ];
     protected $appends = ['gambar_url'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function getGambarUrlAttribute()
{
    if (!$this->gambar) {
        return null;
    }

    return asset('storage/'.$this->gambar);
}
}
