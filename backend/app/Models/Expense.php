<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_type_id',
        'amount',
        'expense_date',
        'recurrence_type',
        'remarks',
        'is_fixed',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_fixed' => 'boolean'
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
