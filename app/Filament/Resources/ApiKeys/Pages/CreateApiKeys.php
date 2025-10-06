<?php

namespace App\Filament\Resources\ApiKeys\Pages;

use App\Filament\Resources\ApiKeys\ApiKeysResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateApiKeys extends CreateRecord
{
    protected static string $resource = ApiKeysResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['key'] = hash_hmac(
            'sha256',
            Str::random(40),
            config('app.key')
        );

        return $data;
    }
}
