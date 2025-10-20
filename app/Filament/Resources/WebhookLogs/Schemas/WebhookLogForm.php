<?php

namespace App\Filament\Resources\WebhookLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WebhookLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('webhook_url')->label('Webhook URL')->disabled(),
                    TextInput::make('irn')->label('IRN')->disabled(),
                    TextInput::make('status')->label('Status')->disabled(),
                    Textarea::make('error_message')->label('Error Message')->disabled(),
                    Textarea::make('response_body')->label('Response')->rows(5)->disabled(),
                    Textarea::make('payload')->label('Payload')->rows(8)->disabled(),
                    TextInput::make('status_code')->label('HTTP Code')->disabled(),
                    DateTimePicker::make('sent_at')->label('Sent At')->disabled(),
                ])->columnSpanFull()->columns(2)
            ]);
    }
}
