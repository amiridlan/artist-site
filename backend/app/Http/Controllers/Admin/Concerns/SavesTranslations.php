<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;

trait SavesTranslations
{
    protected array $locales = ['ms', 'ja'];

    protected function saveTranslations(Model $record, array $data, array $fields): void
    {
        foreach ($this->locales as $locale) {
            foreach ($fields as $field) {
                $key   = "trans_{$locale}_{$field}";
                $value = trim($data[$key] ?? '');

                if ($value !== '') {
                    $record->setTranslation($field, $locale, $value);
                } else {
                    $record->translations()
                        ->where('locale', $locale)
                        ->where('field', $field)
                        ->delete();
                }
            }
        }
    }

    protected function loadTranslations(Model $record, array $fields): array
    {
        $record->load('translations');
        $trans = [];

        foreach ($this->locales as $locale) {
            foreach ($fields as $field) {
                $key         = "trans_{$locale}_{$field}";
                $translation = $record->translations
                    ->where('locale', $locale)
                    ->where('field', $field)
                    ->first();

                $trans[$key] = $translation?->value ?? '';
            }
        }

        return $trans;
    }
}
