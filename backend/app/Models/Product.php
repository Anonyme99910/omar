<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'sku',
        'selling_price',
        'price_جملة',
        'price_قطاعي',
        'price_صفحة',
        'volume_ml',
        'quantity',
        'alert_quantity',
        'photos',
        'is_active'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'price_جملة' => 'decimal:2',
        'price_قطاعي' => 'decimal:2',
        'price_صفحة' => 'decimal:2',
        'quantity' => 'integer',
        'alert_quantity' => 'integer',
        'photos' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['is_low_stock'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->alert_quantity;
    }

    public function getPriceForSegment($segment)
    {
        $priceField = "price_{$segment}";
        return $this->$priceField ?? $this->selling_price;
    }
}
