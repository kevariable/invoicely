<?php

namespace Invoice\Invoice\Domain\Actions;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use RuntimeException;
use Spatie\Browsershot\Browsershot;

final readonly class GenerateInvoiceAction
{
    public function execute(Invoice $invoice): string
    {
        $companySettings = CompanySetting::getSettings();

        $invoice->load(['customer', 'items']);

        $html = View::make('invoice-pdf-browsershot', [
            'invoice' => $invoice,
            'companySettings' => $companySettings,
        ])->render();

        return match (config('pdf.driver')) {
            'cloudflare' => $this->renderWithCloudflare($html),
            default => $this->renderWithBrowsershot($html),
        };
    }

    private function renderWithBrowsershot(string $html): string
    {
        return Browsershot::html($html)
            ->setNodeModulePath(base_path('node_modules'))
            ->noSandbox()
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->pdf();
    }

    /**
     * Render via Cloudflare Browser Rendering REST API.
     *
     * @see https://developers.cloudflare.com/browser-rendering/rest-api/pdf-endpoint/
     */
    private function renderWithCloudflare(string $html): string
    {
        $accountId = config('pdf.cloudflare.account_id');
        $token = config('pdf.cloudflare.api_token');

        if (! $accountId || ! $token) {
            throw new RuntimeException(
                'Cloudflare PDF driver requires CLOUDFLARE_ACCOUNT_ID and CLOUDFLARE_API_TOKEN to be set.'
            );
        }

        $response = Http::withToken($token)
            ->timeout(config('pdf.cloudflare.timeout'))
            ->acceptJson()
            ->post(
                "https://api.cloudflare.com/client/v4/accounts/{$accountId}/browser-rendering/pdf",
                [
                    'html' => $html,
                    'viewport' => ['width' => 1240, 'height' => 1754],
                    'waitForTimeout' => 2000,
                ],
            );

        try {
            $response->throw();
        } catch (RequestException $e) {
            throw new RuntimeException(
                'Cloudflare PDF render failed: '.$response->body(),
                previous: $e,
            );
        }

        return $response->body();
    }
}
