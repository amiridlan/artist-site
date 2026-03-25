<?php

namespace App\Filament\Resources\ReleaseResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\ReleaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRelease extends EditRecord
{
    use HasTranslationFields;

    protected static string $resource = ReleaseResource::class;

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
