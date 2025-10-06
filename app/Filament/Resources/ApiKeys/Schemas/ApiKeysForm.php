<?php

namespace App\Filament\Resources\ApiKeys\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApiKeysForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->required(),
                    Toggle::make('active')
                        ->default(true),
                ])->columnSpanFull()->columns(2),
            ]);
    }
}
