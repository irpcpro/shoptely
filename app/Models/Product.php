<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_product';

    protected $fillable = [
        'id_store',
        'id_category',
        'title',
        'description',
        'image',
    ];

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'id_store', 'id_store');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class, 'id_product', 'id_product');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id_category', 'id_category');
    }

}
