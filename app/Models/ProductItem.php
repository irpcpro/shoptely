<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_product_item';

    protected $fillable = [
        'id_product',
        'title',
        'price',
        'quantity',
        'in_stock',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id_product', 'id_product');
    }

}
