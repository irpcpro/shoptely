<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id_user', 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StoreDetail::class, 'id_store', 'id_store');
    }

}
