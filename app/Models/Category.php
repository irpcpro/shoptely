<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_category';

    protected $fillable = [
        'id_store',
        'name',
    ];

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'id_store', 'id_store');
    }

}
