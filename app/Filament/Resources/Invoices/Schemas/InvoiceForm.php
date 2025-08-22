<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    Select::make('organization_id')
                        ->label('Organization')
                        ->relationship('organization', 'legal_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('buyer_organization_ref'),
                    TextInput::make('total_amount')
                        ->required()
                        ->numeric(),
                    TextInput::make('tax_breakdown'),
                    Grid::make(3)->schema([
                        Select::make('vat_treatment')
                            ->options(['standard' => 'Standard', 'zero-rated' => 'Zero rated', 'exempt' => 'Exempt'])
                            ->default('standard')
                            ->required(),
                        TextInput::make('wht_amount')
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'validated' => 'Validated',
                                'submitted' => 'Submitted',
                                'reported' => 'Reported',
                                'closed' => 'Closed',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columnSpanFull()
                ])->columnSpanFull()->columns(2),
            ]);
    }
}
