<?php

namespace App\Filament\Resources\WebhookLogs\Pages;

use App\Filament\Resources\WebhookLogs\WebhookLogResource;
use Filament\Resources\Pages\EditRecord;

class EditWebhookLog extends EditRecord
{
    protected static string $resource = WebhookLogResource::class;
}
