<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
public function opd()
{
    return $this->belongsTo(Opd::class);
}
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'opd_id'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

}
