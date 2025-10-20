<?php

namespace App\Filament\Resources\WebhookLogs\Pages;

use App\Filament\Resources\WebhookLogs\WebhookLogResource;
use Filament\Resources\Pages\ListRecords;

class ListWebhookLogs extends ListRecords
{
    protected static string $resource = WebhookLogResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
