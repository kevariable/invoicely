<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_rate',
        'total_amount',
        'sort',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sort' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->sort)) {
                $item->sort = (static::where('invoice_id', $item->invoice_id)->max('sort') ?? 0) + 1;
            }
        });

        // Update invoice amounts when item is created
        static::created(function ($item) {
            $item->invoice->updateAmounts();
        });

        // Update invoice amounts when item is updated
        static::updated(function ($item) {
            $item->invoice->updateAmounts();
        });

        // Update invoice amounts when item is deleted
        static::deleted(function ($item) {
            $item->invoice->updateAmounts();
        });
    }

    /**
     * Get the invoice that owns the item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Calculate the total amount based on quantity and unit rate.
     */
    public function calculateTotal(): float
    {
        return $this->quantity * $this->unit_rate;
    }

    /**
     * Get formatted quantity.
     */
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity, 2);
    }

    /**
     * Get formatted unit rate.
     */
    public function getFormattedRateAttribute(): string
    {
        return '$'.number_format($this->unit_rate, 2);
    }
}
