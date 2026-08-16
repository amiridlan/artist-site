<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the Billplz Bills API (v3).
 *
 * Docs verified live on 2026-08-16 against:
 *   - https://support.billplz.com/api (redirects from https://www.billplz.com/api)
 *   - https://billplz.github.io/api_slate/
 *
 * Auth: HTTP Basic Auth, API Secret Key as username, empty password.
 * Sandbox base URL:    https://www.billplz-sandbox.com
 * Production base URL: https://www.billplz.com
 */
class BillplzService
{
    private readonly string $baseUrl;
    private readonly string $secretKey;
    private readonly string $collectionId;
    private readonly string $xSignatureKey;

    public function __construct(
        ?string $baseUrl = null,
        ?string $secretKey = null,
        ?string $collectionId = null,
        ?string $xSignatureKey = null,
    ) {
        $this->baseUrl       = rtrim($baseUrl ?? config('services.billplz.url'), '/');
        $this->secretKey     = $secretKey ?? config('services.billplz.secret_key');
        $this->collectionId  = $collectionId ?? config('services.billplz.collection_id');
        $this->xSignatureKey = $xSignatureKey ?? config('services.billplz.x_signature_key');
    }

    /**
     * Create a bill. Returns the decoded Billplz bill object (includes `id`
     * and `url` for redirecting the payer) or null on failure.
     */
    public function createBill(array $params): ?array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->asForm()
                ->post($this->baseUrl . '/api/v3/bills', array_merge([
                    'collection_id' => $this->collectionId,
                    'deliver'       => false,
                ], $params));

            if (! $response->successful()) {
                Log::error('Billplz createBill failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $result = $response->json();

            if (empty($result['id'])) {
                Log::error('Billplz createBill: no id in response', ['response' => $result]);
                return null;
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Billplz createBill exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get a bill directly from Billplz — the single source of truth for
     * "was this bill actually paid". Callers must never trust a client- or
     * callback-supplied `paid`/`state` value instead of calling this.
     */
    public function getBill(string $billId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->baseUrl . '/api/v3/bills/' . $billId);

            if (! $response->successful()) {
                Log::error('Billplz getBill failed', ['billId' => $billId, 'status' => $response->status()]);
                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Billplz getBill exception', ['billId' => $billId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function isPaid(?array $bill): bool
    {
        return $bill !== null
            && (bool) ($bill['paid'] ?? false) === true
            && ($bill['state'] ?? null) === 'paid';
    }

    /**
     * Verify the `x_signature` field on a callback or redirect payload.
     *
     * Algorithm (per Billplz docs): take every field except `x_signature`,
     * sort keys ascending case-insensitively, concatenate each key directly
     * with its value (no separator), join the pairs with `|`, then HMAC-SHA256
     * the resulting string with the X Signature Key from the Billplz
     * dashboard (Settings > Keys & Integration).
     */
    public function verifySignature(array $payload): bool
    {
        if (empty($this->xSignatureKey)) {
            // No X Signature Key configured — cannot verify. Caller must
            // fall back entirely to the live re-query (getBill) instead.
            return false;
        }

        $providedSignature = $payload['x_signature'] ?? null;
        if (! is_string($providedSignature) || $providedSignature === '') {
            return false;
        }

        $expected = $this->computeSignature($payload);

        return hash_equals($expected, $providedSignature);
    }

    private function computeSignature(array $payload): string
    {
        $fields = $payload;
        unset($fields['x_signature']);

        $keys = array_keys($fields);
        usort($keys, fn ($a, $b) => strcasecmp($a, $b));

        $parts = [];
        foreach ($keys as $key) {
            $value = $fields[$key];
            $parts[] = $key . (is_bool($value) ? ($value ? 'true' : 'false') : (string) $value);
        }

        $source = implode('|', $parts);

        return hash_hmac('sha256', $source, $this->xSignatureKey);
    }
}
