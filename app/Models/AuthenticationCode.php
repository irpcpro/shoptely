<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuthenticationCode extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_authentication_code';

    protected $fillable = [
        'id_user',
        'code',
        'expired',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format(MODELS_CREATED_AT_FORMAT);
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->format(MODELS_CREATED_AT_FORMAT);
    }

    public function scopeNotExpired(Builder $query): void
    {
        $query->where('expired', false);
    }

    public function scopeGetUserLastActiveCode(Builder $query)
    {
        $query->where('created_at', '>', now()->subSecond(AUTH_CODE_EXPIRE_TIME));
        $query->notExpired();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id_user', 'id_user');
    }

}
