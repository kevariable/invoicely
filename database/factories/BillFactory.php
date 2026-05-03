<?php

namespace Database\Factories;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    public function definition(): array
    {
        $dueDate = Carbon::instance(fake()->dateTimeBetween('-6 months', '+1 month'));

        return [
            'vendor_name' => fake()->randomElement([
                'Hetzner', 'AWS', 'GitHub', 'Vercel', 'Cloudflare',
                'Stripe', 'Namecheap', 'Adobe', 'JetBrains', 'OpenAI',
            ]),
            'category' => fake()->randomElement(['hosting', 'software', 'domain', 'service', 'subscription']),
            'description' => fake()->randomElement([
                'Monthly server hosting',
                'CI/CD minutes',
                'Domain renewal',
                'Pro subscription',
                'API credits',
                'Cloud storage',
            ]),
            'amount' => fake()->randomFloat(2, 5, 250),
            'currency' => fake()->randomElement(['USD', 'GBP']),
            'status' => 'unpaid',
            'recurring' => fake()->boolean(70),
            'due_date' => $dueDate->toDateString(),
            'paid_date' => null,
            'notes' => null,
        ];
    }

    public function paid(): self
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'paid',
            'paid_date' => Carbon::parse($attrs['due_date'])->subDays(fake()->numberBetween(0, 5)),
        ]);
    }
}
