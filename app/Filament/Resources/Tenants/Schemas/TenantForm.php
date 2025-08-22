<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('brand'),
                    Grid::make(3)->schema([
                        TextInput::make('domain')
                            ->unique(),
                        TextInput::make('feature_flags'),
                        TextInput::make('retention_policy')
                            ->required()
                            ->default('default'),
                    ])->columnSpanFull()
                ])->columnSpanFull()->columns(2),
            ]);
    }
}
