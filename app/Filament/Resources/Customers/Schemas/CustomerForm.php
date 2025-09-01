<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('tin')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    TextInput::make('phone')
                        ->tel(),
                    TextInput::make('street_name'),
                    TextInput::make('city_name'),
                    TextInput::make('postal_zone'),
                    TextInput::make('state'),
                    TextInput::make('country')
                        ->label('Country Code')
                        ->required()
                        ->default('NG'),
                    Textarea::make('business_description')
                        ->columnSpanFull(),
                ])->columnSpanFull()->columns(3),
            ]);
    }
}
