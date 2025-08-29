<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Dom\Text;
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
                    TextInput::make('trade_name')
                        ->required(),
                    TextInput::make('business_id')
                        ->required(),
                    TextInput::make('service_id')
                        ->required(),
                    TextInput::make('email')
                        ->email()
                        ->required(),
                    TextInput::make('phone')
                        ->tel()
                        ->required(),
                    TextInput::make('street_name')
                        ->required(),
                    TextInput::make('city_name')
                        ->required(),
                    TextInput::make('postal_zone')
                        ->required(),
                    TextInput::make('country')
                        ->readOnly()
                        ->dehydrated(),
                    TextInput::make('tin')
                        ->required(),
                    TextInput::make('bank_details'),
                ])->columnSpanFull()->columns(3),
            ]);
    }
}
