<?php

namespace App\Filament\Resources\WebhookLogs;

use App\Filament\Resources\WebhookLogs\Pages\CreateWebhookLog;
use App\Filament\Resources\WebhookLogs\Pages\EditWebhookLog;
use App\Filament\Resources\WebhookLogs\Pages\ListWebhookLogs;
use App\Filament\Resources\WebhookLogs\Schemas\WebhookLogForm;
use App\Filament\Resources\WebhookLogs\Tables\WebhookLogsTable;
use App\Models\WebhookLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WebhookLogResource extends Resource
{
    protected static ?string $model = WebhookLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) WebhookLog::where('status', 'failed')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return WebhookLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebhookLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebhookLogs::route('/'),
            'create' => CreateWebhookLog::route('/create'),
            'edit' => EditWebhookLog::route('/{record}/edit'),
        ];
    }
}
