<?php

namespace App\Mcp\Tools;

use App\Models\Invoice;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Issue (or return the existing) public share link for an invoice: a styled preview page and a PDF download URL, both protected by a secret token.')]
class GenerateShareLinkTool extends Tool
{
    protected string $name = 'generate_share_link';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $invoice = Invoice::find($validated['id']);

        if (! $invoice) {
            return Response::error("No invoice found with id {$validated['id']}.");
        }

        $previewUrl = $invoice->getPublicUrl();

        return Response::json([
            'message' => "Share link ready for invoice {$invoice->invoice_number}.",
            'invoice_number' => $invoice->invoice_number,
            'preview_url' => $previewUrl,
            'download_url' => $previewUrl.'/download',
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('Invoice id to generate a share link for.')
                ->required(),
        ];
    }
}
