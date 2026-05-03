<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 80);
        $rate = fake()->randomFloat(2, 25, 200);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->randomElement([
                'Senior backend engineering',
                'Frontend implementation',
                'Code review and refactoring',
                'Discovery / scoping session',
                'On-call support hours',
                'API integration work',
                'Database performance tuning',
                'Production incident response',
                'Architecture consulting',
            ]),
            'quantity' => $quantity,
            'unit_rate' => $rate,
            'total_amount' => round($quantity * $rate, 2),
        ];
    }
}
