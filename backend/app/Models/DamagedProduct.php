<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamagedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'cost_price',
        'total_loss',
        'damage_type',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost_price' => 'decimal:2',
        'total_loss' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
