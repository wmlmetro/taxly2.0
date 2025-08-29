<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name'),
                TextEntry::make('trade_name'),
                TextEntry::make('business_id'),
                TextEntry::make('service_id'),
                TextEntry::make('email'),
                TextEntry::make('phone'),
                TextEntry::make('street_name'),
                TextEntry::make('city_name'),
                TextEntry::make('postal_zone'),
                TextEntry::make('country'),
                TextEntry::make('tin'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
