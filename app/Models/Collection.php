<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'status',
        'privacy',
        'description',
        'order',
        'items_order',
        'user_id',
        'is_suggested',
        'sharing_link',
        'set_suggested_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productCollections(): HasMany
    {
        return $this->hasMany(ProductCollection::class);
    }
}
