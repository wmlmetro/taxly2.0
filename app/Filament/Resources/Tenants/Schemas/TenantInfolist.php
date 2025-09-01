<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email'),
                    TextEntry::make('entity_id'),
                    TextEntry::make('brand'),
                    TextEntry::make('domain'),
                    TextEntry::make('retention_policy'),
                    TextEntry::make('created_at')
                        ->dateTime(),
                    TextEntry::make('updated_at')
                        ->dateTime(),
                ])->columnSpanFull()->columns(2),
            ]);
    }
}
