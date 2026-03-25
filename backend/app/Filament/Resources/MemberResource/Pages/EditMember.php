<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    use HasTranslationFields;

    protected static string $resource = MemberResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillTranslationData($data);
    }

    protected function afterSave(): void
    {
        $this->saveTranslationData($this->data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
