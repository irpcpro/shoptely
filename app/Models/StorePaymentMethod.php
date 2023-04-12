<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePaymentMethod extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_store_payment_method';

    protected $fillable = [
        'id_store',
        'method',
    ];

}
