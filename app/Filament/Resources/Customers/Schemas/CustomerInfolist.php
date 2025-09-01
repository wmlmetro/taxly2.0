<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextEntry::make('name'),
                    TextEntry::make('tin'),
                    TextEntry::make('email')
                        ->label('Email address'),
                    TextEntry::make('phone'),
                    TextEntry::make('street_name'),
                    TextEntry::make('city_name'),
                    TextEntry::make('postal_zone'),
                    TextEntry::make('state'),
                    TextEntry::make('country'),
                    TextEntry::make('business_description')
                        ->columnSpanFull(),
                    TextEntry::make('created_at')
                        ->dateTime(),
                    TextEntry::make('updated_at')
                        ->dateTime(),
                ])->columnSpanFull()->columns(2),
            ]);
    }
}
