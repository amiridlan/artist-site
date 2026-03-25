<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    protected array $supportedLocales = ['ms', 'ja'];

    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * Translate text from English to the target locale.
     * Tries LibreTranslate first, falls back to MyMemory.
     */
    public function translate(string $text, string $targetLocale): ?string
    {
        if (empty(trim($text))) {
            return $text;
        }

        // Try LibreTranslate first
        $result = $this->translateViaLibreTranslate($text, $targetLocale);

        // Fallback to MyMemory
        if ($result === null) {
            $result = $this->translateViaMyMemory($text, $targetLocale);
        }

        return $result;
    }

    /**
     * Translate using LibreTranslate (free, supports Malay "ms" distinct from Indonesian "id").
     */
    protected function translateViaLibreTranslate(string $text, string $targetLocale): ?string
    {
        $baseUrl = config('services.libretranslate.url', 'https://libretranslate.com');
        $apiKey = config('services.libretranslate.api_key', '');

        try {
            $payload = [
                'q' => $text,
                'source' => 'en',
                'target' => $targetLocale,
                'format' => 'text',
            ];

            if ($apiKey) {
                $payload['api_key'] = $apiKey;
            }

            $response = Http::timeout(15)
                ->post("{$baseUrl}/translate", $payload);

            if ($response->successful()) {
                $translated = $response->json('translatedText');
                if ($translated && $translated !== $text) {
                    return $translated;
                }
            }

            Log::warning('LibreTranslate failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'target' => $targetLocale,
            ]);
        } catch (\Exception $e) {
            Log::warning('LibreTranslate exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Translate using MyMemory API (free, 5000 chars/day, supports "ms" for Malay).
     */
    protected function translateViaMyMemory(string $text, string $targetLocale): ?string
    {
        try {
            $langPair = "en|{$targetLocale}";

            $response = Http::timeout(15)
                ->get('https://api.mymemory.translated.net/get', [
                    'q' => mb_substr($text, 0, 500), // MyMemory has a 500 char limit per request
                    'langpair' => $langPair,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $translated = $data['responseData']['translatedText'] ?? null;
                $match = $data['responseData']['match'] ?? 0;

                // Only use if match quality is reasonable
                if ($translated && $match >= 0.3 && strtolower($translated) !== strtolower($text)) {
                    return $translated;
                }
            }

            Log::warning('MyMemory failed', [
                'status' => $response->status(),
                'target' => $targetLocale,
            ]);
        } catch (\Exception $e) {
            Log::warning('MyMemory exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Translate all translatable fields for a model to all supported locales.
     * Only overwrites auto-translated fields (preserves manual edits).
     */
    public function translateModel($model): void
    {
        if (!method_exists($model, 'getTranslatableFields')) {
            return;
        }

        $fields = $model->getTranslatableFields();

        foreach ($this->supportedLocales as $locale) {
            foreach ($fields as $field) {
                $originalValue = $model->getAttribute($field);

                if (empty($originalValue)) {
                    continue;
                }

                // Check if a manual translation exists — don't overwrite it
                $existing = $model->translations()
                    ->where('locale', $locale)
                    ->where('field', $field)
                    ->first();

                if ($existing && !$existing->auto_translated) {
                    continue; // Staff manually edited this — skip
                }

                $translated = $this->translate($originalValue, $locale);

                if ($translated) {
                    $model->setTranslation($field, $locale, $translated, autoTranslated: true);
                }
            }
        }
    }
}
