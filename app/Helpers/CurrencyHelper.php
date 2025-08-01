<?php

namespace App\Helpers;

class CurrencyHelper
{
    public const CURRENCIES = [
        'USD' => [
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'position' => 'before', // before or after the amount
        ],
        'GBP' => [
            'code' => 'GBP',
            'name' => 'British Pound',
            'symbol' => '£',
            'position' => 'before',
        ],
    ];

    /**
     * Get currency symbol by code.
     */
    public static function getSymbol(string $currencyCode): string
    {
        return self::CURRENCIES[$currencyCode]['symbol'] ?? '$';
    }

    /**
     * Get currency name by code.
     */
    public static function getName(string $currencyCode): string
    {
        return self::CURRENCIES[$currencyCode]['name'] ?? 'US Dollar';
    }

    /**
     * Get all available currencies for selection.
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (self::CURRENCIES as $code => $data) {
            $options[$code] = $data['symbol'] . ' ' . $data['name'] . ' (' . $code . ')';
        }
        return $options;
    }

    /**
     * Format amount with currency symbol.
     */
    public static function format(float $amount, string $currencyCode, int $decimals = 2): string
    {
        $currency = self::CURRENCIES[$currencyCode] ?? self::CURRENCIES['USD'];
        $formattedAmount = number_format($amount, $decimals);

        if ($currency['position'] === 'before') {
            return $currency['symbol'] . $formattedAmount;
        } else {
            return $formattedAmount . $currency['symbol'];
        }
    }

    /**
     * Get currency code from company settings or default to USD.
     */
    public static function getDefaultCurrency(): string
    {
        try {
            $companySettings = \App\Models\CompanySetting::getSettings();
            return $companySettings->currency ?? 'USD';
        } catch (\Exception $e) {
            return 'USD';
        }
    }

    /**
     * Check if currency code is valid.
     */
    public static function isValidCurrency(string $currencyCode): bool
    {
        return array_key_exists($currencyCode, self::CURRENCIES);
    }
}