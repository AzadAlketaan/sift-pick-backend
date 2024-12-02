<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'price',
        'stock_quantity',
        'sharing_link',
        'created_by',
        'order'
    ];
    
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function gifts(): HasMany
    {
        return $this->hasMany(Gift::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function productCategories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function productCollections(): HasMany
    {
        return $this->hasMany(ProductCollection::class);
    }

    public function productDiscounts(): HasMany
    {
        return $this->hasMany(ProductDiscount::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function userCheckoutProducts(): HasMany
    {
        return $this->hasMany(UserCheckoutProduct::class);
    }

    public function UserProductLog(): HasMany
    {
        return $this->hasMany(UserProductLog::class);
    }
    
}
