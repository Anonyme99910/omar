<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'sku',
        'description',
        'photos',
        'price_قطاعي',
        'price_جملة',
        'price_صفحة',
        'quantity',
        'alert_quantity',
        'is_active'
    ];

    protected $casts = [
        'photos' => 'array',
        'price_قطاعي' => 'decimal:2',
        'price_جملة' => 'decimal:2',
        'price_صفحة' => 'decimal:2',
        'quantity' => 'integer',
        'alert_quantity' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationship: Package has many products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'package_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
