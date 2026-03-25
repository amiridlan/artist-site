<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\MemberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMember extends CreateRecord
{
    use HasTranslationFields;

    protected static string $resource = MemberResource::class;

    protected function afterCreate(): void
    {
        $this->saveTranslationData($this->data);
    }
}
