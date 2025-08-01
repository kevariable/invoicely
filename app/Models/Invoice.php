<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'status',
        'view_state',
        'public_token',
        'viewed_at',
        'subtotal',
        'tax_amount',
        'total_amount',
        'currency',
        'issue_date',
        'due_date',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
        'view_state' => 'string',
        'currency' => 'string',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'datetime',
        'viewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for the invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Check if the invoice is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if the invoice is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->isPaid()) {
            return false;
        }

        return $this->due_date < now();
    }

    /**
     * Mark the invoice as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);
    }

    /**
     * Generate next invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = static::orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, 4) + 1 : 1;
        
        return 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a secure public token for sharing.
     */
    public function generatePublicToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
        } while (self::where('public_token', $token)->exists());

        $this->update(['public_token' => $token]);
        
        return $token;
    }

    /**
     * Get the public URL for this invoice.
     */
    public function getPublicUrl(): string
    {
        if (!$this->public_token) {
            $this->generatePublicToken();
        }
        
        return url("/invoice/preview/{$this->public_token}");
    }

    /**
     * Mark this invoice as viewed.
     */
    public function markAsViewed(): void
    {
        if ($this->view_state === 'unread') {
            $this->update([
                'view_state' => 'viewed',
                'viewed_at' => now(),
            ]);
        }
    }

    /**
     * Check if invoice has been viewed.
     */
    public function isViewed(): bool
    {
        return $this->view_state === 'viewed';
    }

    /**
     * Check if invoice is unread.
     */
    public function isUnread(): bool
    {
        return $this->view_state === 'unread';
    }

    /**
     * Scope for unread invoices.
     */
    public function scopeUnread($query)
    {
        return $query->where('view_state', 'unread');
    }

    /**
     * Scope for viewed invoices.
     */
    public function scopeViewed($query)
    {
        return $query->where('view_state', 'viewed');
    }

    /**
     * Get currency symbol.
     */
    public function getCurrencySymbol(): string
    {
        return \App\Helpers\CurrencyHelper::getSymbol($this->currency);
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmount(float $amount): string
    {
        return \App\Helpers\CurrencyHelper::format($amount, $this->currency);
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotal(): string
    {
        return $this->getFormattedAmount($this->subtotal);
    }

    /**
     * Get formatted tax amount.
     */
    public function getFormattedTaxAmount(): string
    {
        return $this->getFormattedAmount($this->tax_amount);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAmount(): string
    {
        return $this->getFormattedAmount($this->total_amount);
    }
}
