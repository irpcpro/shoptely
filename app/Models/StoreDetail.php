<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_store_detail';

    protected $fillable = [
        'id_store',
        'name',
        'value',
    ];

}
