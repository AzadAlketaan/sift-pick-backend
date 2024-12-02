<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserGift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gift_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }
}
