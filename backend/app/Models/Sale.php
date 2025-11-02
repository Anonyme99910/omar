<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'subtotal',
        'tax',
        'discount',
        'shipping',
        'total',
        'paid_sum',
        'balance_due',
        'payment_method',
        'status',
        'issue_date',
        'due_date',
        'notes',
        'pdf_path',
        'created_by',
        'paid_amount',
        'remaining_amount'
    ];
    
    protected $attributes = [
        'paid_sum' => 0,
        'balance_due' => 0,
        'paid_amount' => 0,
        'remaining_amount' => 0
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_sum' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date'
    ];

    protected $appends = ['profit', 'items_count'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (!$sale->invoice_number) {
                $sale->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where(function($q) {
            $q->where('paid_sum', 0)
              ->orWhereNull('paid_sum');
        })->where('status', '!=', 'void');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->whereColumn('paid_sum', '<', 'total')
                     ->where('paid_sum', '>', 0)
                     ->whereNotNull('paid_sum')
                     ->where('status', '!=', 'void');
    }

    public function scopePaid($query)
    {
        return $query->whereColumn('paid_sum', '>=', 'total')
                     ->whereNotNull('paid_sum')
                     ->where('status', '!=', 'void');
    }

    public function scopeVoid($query)
    {
        return $query->where('status', 'void');
    }

    // Methods
    public function recordPayment($amount, $method, $reference = null, $notes = null)
    {
        return \DB::transaction(function () use ($amount, $method, $reference, $notes) {
            // Create payment record
            $payment = $this->payments()->create([
                'amount' => $amount,
                'method' => $method,
                'reference' => $reference,
                'notes' => $notes,
                'paid_at' => now(),
                'created_by' => auth()->id() ?? null
            ]);

            // Update totals
            $this->paid_sum = ($this->paid_sum ?? 0) + $amount;
            $this->balance_due = $this->total - $this->paid_sum;

            // Update status
            if ($this->paid_sum <= 0) {
                $this->status = 'issued';
            } elseif ($this->paid_sum >= $this->total) {
                $this->status = 'paid';
            } else {
                $this->status = 'partially_paid';
            }

            $this->save();
            
            return $payment;
        });
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->total = $this->subtotal - $this->discount + $this->tax + $this->shipping;
        $this->balance_due = $this->total - $this->paid_sum;
        $this->save();
    }

    // Accessors
    public function getProfitAttribute()
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items.product');
        }
        
        $totalProfit = 0;
        foreach ($this->items as $item) {
            $costPrice = $item->product->cost_price ?? 0;
            $sellingPrice = $item->price;
            $profit = ($sellingPrice - $costPrice) * $item->quantity;
            $totalProfit += $profit;
        }
        
        return round($totalProfit, 2);
    }

    public function getItemsCountAttribute()
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        
        return $this->items->sum('quantity');
    }
}
