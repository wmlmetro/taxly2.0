<?php

namespace App\Filament\Resources\ApiKeys;

use App\Filament\Resources\ApiKeys\Pages\CreateApiKeys;
use App\Filament\Resources\ApiKeys\Pages\EditApiKeys;
use App\Filament\Resources\ApiKeys\Pages\ListApiKeys;
use App\Filament\Resources\ApiKeys\Schemas\ApiKeysForm;
use App\Filament\Resources\ApiKeys\Tables\ApiKeysTable;
use App\Models\ApiKey;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ApiKeysResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $recordTitleAttribute = 'ApiKey';

    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return ApiKeysForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiKeysTable::configure($table);
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
            'index' => ListApiKeys::route('/'),
            'create' => CreateApiKeys::route('/create'),
            'edit' => EditApiKeys::route('/{record}/edit'),
        ];
    }
}
