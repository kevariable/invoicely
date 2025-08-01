<?php

namespace App\Filament\Resources\InvoiceResource\Actions;

use App\Filament\Actions\Concerns\HasCopyable;
use Filament\Actions\Action;

final class CopyShareLinkAction extends Action
{
    use HasCopyable {
        HasCopyable::getCopyable as getDefaultCopyable;
    }

    public function getCopyable(): ?string
    {
        if ($this->copyable === null) {
            return $this->evaluate(fn ($component) => '$wire.'.$component->getStatePath());
        }

        return $this->getDefaultCopyable();
    }
}