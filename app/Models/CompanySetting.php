<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'person_name',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'phone',
        'email',
        'website',
        'tax_id',
        'bank_details',
        'currency',
        'tax_rate',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the singleton company settings instance
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'company_name' => 'Your Company Name',
            'person_name' => 'Kevin Abrar Khansa',
            'country' => 'United States',
            'currency' => 'USD',
            'tax_rate' => 0,
        ]);
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city . ($this->city && $this->state ? ', ' : '') . $this->state . ' ' . $this->zip_code,
            $this->country,
        ]);

        return implode("\n", $parts);
    }

    /**
     * Get formatted tax rate as percentage
     */
    public function getFormattedTaxRateAttribute(): string
    {
        return number_format($this->tax_rate, 2) . '%';
    }
}
