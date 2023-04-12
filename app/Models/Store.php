<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_store';

    protected $fillable = [
        'id_user',
        'username',
        'expire_time',
        'token',
    ];

    protected $hidden = [
        'id_user',
        'expire_time',
        'token',
    ];

}
