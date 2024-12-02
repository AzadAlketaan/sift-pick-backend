<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'coupon_persentage',
        'start_date',
        'end_date',
        'deleted_at'
    ];

    public function userCoupons(): HasMany
    {
        return $this->hasMany(UserCoupon::class);
    }
}
