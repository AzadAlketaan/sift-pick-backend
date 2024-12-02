<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evaluator',
        'value'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    
}
