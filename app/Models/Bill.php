<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_name',
        'category',
        'description',
        'amount',
        'currency',
        'status',
        'recurring',
        'due_date',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'recurring' => 'boolean',
        'due_date' => 'date',
        'paid_date' => 'datetime',
    ];

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return ! $this->isPaid()
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => $this->paid_date ?? now(),
        ]);
    }

    public function getFormattedAmount(): string
    {
        return CurrencyHelper::format((float) $this->amount, $this->currency);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }
}
