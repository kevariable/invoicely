<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('DatabaseSeeder skipped: only runs in local/testing. Use a dedicated production seeder for prod data.');

            return;
        }

        User::updateOrCreate(
            ['email' => 'kevariable@gmail.com'],
            [
                'name' => 'Kevin',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        CompanySetting::firstOrCreate([], [
            'company_name' => 'ByteHire Limited',
            'person_name' => 'Kevin Abrar Khansa',
            'address' => '123 Main Street',
            'city' => 'London',
            'state' => 'GL',
            'zip_code' => 'E1 6AN',
            'country' => 'United Kingdom',
            'phone' => '+44 20 1234 5678',
            'email' => 'kevariable@gmail.com',
            'website' => 'https://invoicely.test',
            'tax_id' => 'GB123456789',
            'bank_details' => "Bank: BANK CENTRAL\nA/C No.: 22011454321\nA/C Name: Kevin Abrar Khansa",
            'currency' => 'GBP',
            'tax_rate' => 0,
            'invoice_prefix' => 'INV-',
        ]);

        $customers = Customer::factory(8)->create();

        $customers->each(function (Customer $customer) {
            Invoice::factory(2)
                ->paid()
                ->for($customer)
                ->afterCreating(fn (Invoice $invoice) => $this->fillItems($invoice))
                ->create();

            Invoice::factory(1)
                ->sent()
                ->for($customer)
                ->afterCreating(fn (Invoice $invoice) => $this->fillItems($invoice))
                ->create();

            if (fake()->boolean(40)) {
                Invoice::factory(1)
                    ->overdue()
                    ->for($customer)
                    ->afterCreating(fn (Invoice $invoice) => $this->fillItems($invoice))
                    ->create();
            }

            if (fake()->boolean(30)) {
                Invoice::factory(1)
                    ->for($customer)
                    ->afterCreating(fn (Invoice $invoice) => $this->fillItems($invoice))
                    ->create();
            }
        });

        Invoice::factory(2)
            ->paid()
            ->for($customers->random())
            ->state(fn () => ['capped_total_amount' => 1500])
            ->afterCreating(fn (Invoice $invoice) => $this->fillItems($invoice))
            ->create();

        $this->seedBills();
    }

    private function seedBills(): void
    {
        $catalog = [
            ['Hetzner', 'hosting', 'CX22 server (monthly)', 12.50, 'GBP', true],
            ['AWS', 'hosting', 'S3 + CloudFront', 38.40, 'USD', true],
            ['GitHub', 'subscription', 'Copilot business seat', 19.00, 'USD', true],
            ['Vercel', 'hosting', 'Pro plan', 20.00, 'USD', true],
            ['Cloudflare', 'service', 'Workers + KV', 5.00, 'USD', true],
            ['Namecheap', 'domain', 'invoicely.app renewal', 14.99, 'USD', false],
            ['JetBrains', 'software', 'PhpStorm subscription', 9.90, 'USD', true],
            ['OpenAI', 'subscription', 'API credits', 50.00, 'USD', true],
            ['Adobe', 'subscription', 'Creative Cloud Photography', 11.99, 'USD', true],
            ['Stripe', 'service', 'Processing fees', 23.40, 'USD', false],
        ];

        foreach ($catalog as [$vendor, $category, $description, $amount, $currency, $recurring]) {
            for ($i = 0; $i < 3; $i++) {
                $dueDate = Carbon::now()->subMonths($i)->day(fake()->numberBetween(1, 28));
                $isPaid = $i > 0 || fake()->boolean(60);

                Bill::create([
                    'vendor_name' => $vendor,
                    'category' => $category,
                    'description' => $description,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => $isPaid ? 'paid' : 'unpaid',
                    'recurring' => $recurring,
                    'due_date' => $dueDate->toDateString(),
                    'paid_date' => $isPaid ? $dueDate->copy()->subDays(fake()->numberBetween(0, 3)) : null,
                ]);
            }
        }
    }

    private function fillItems(Invoice $invoice): void
    {
        InvoiceItem::factory(fake()->numberBetween(1, 4))
            ->for($invoice)
            ->create();

        $invoice->refresh()->updateAmounts();
    }
}
