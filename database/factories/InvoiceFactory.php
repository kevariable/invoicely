<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $issueDate = Carbon::instance(fake()->dateTimeBetween('-12 months', 'now'));
        $dueDate = $issueDate->copy()->addDays(fake()->randomElement([14, 21, 30]));

        return [
            'invoice_number' => 'INV-'.str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'status' => 'draft',
            'view_state' => 'unread',
            'currency' => fake()->randomElement(['USD', 'GBP']),
            'subtotal' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
            'issue_date' => $issueDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'notes' => fake()->boolean(40) ? fake()->sentence(8) : null,
        ];
    }

    public function paid(): self
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'paid',
            'view_state' => 'viewed',
            'viewed_at' => Carbon::parse($attrs['issue_date'])->addDays(1),
            'email_sent_at' => Carbon::parse($attrs['issue_date']),
            'paid_date' => Carbon::parse($attrs['due_date'])->subDays(fake()->numberBetween(0, 10)),
        ]);
    }

    public function sent(): self
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'sent',
            'view_state' => fake()->randomElement(['unread', 'viewed']),
            'email_sent_at' => Carbon::parse($attrs['issue_date']),
        ]);
    }

    public function overdue(): self
    {
        return $this->state(function (array $attrs) {
            $issue = Carbon::parse($attrs['issue_date'])->subDays(60);

            return [
                'status' => 'overdue',
                'view_state' => 'viewed',
                'email_sent_at' => $issue,
                'issue_date' => $issue->toDateString(),
                'due_date' => $issue->copy()->addDays(14)->toDateString(),
            ];
        });
    }
}
