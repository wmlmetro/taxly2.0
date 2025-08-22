<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    Select::make('tenant_id')
                        ->label('Tenant')
                        ->relationship('tenant', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('tin')
                        ->required(),
                    TextInput::make('legal_name')
                        ->required(),
                    TextInput::make('bank_details'),
                    Textarea::make('address')
                        ->columnSpanFull(),
                ])->columnSpanFull()->columns(2),
            ]);
    }
}
