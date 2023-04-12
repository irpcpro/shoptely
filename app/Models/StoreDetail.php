<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StoreDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_store_detail';

    protected $fillable = [
        'id_store',
        'name',
        'value',
    ];

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'id_store', 'id_store');
    }

}
