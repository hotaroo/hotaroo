<?php

namespace App\Filament\Resources\QuotaSummaryResource\Pages;

use App\Filament\Resources\QuotaSummaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuotaSummaries extends ListRecords
{
    protected static string $resource = QuotaSummaryResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
