<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanclubMember;
use App\Models\FanclubPendingRegistration;
use App\Models\FanclubSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FanPaymentController extends Controller
{
    private const PRICES = [
        'basic' => 3000,  // RM 30 in cents
        'gold'  => 6000,  // RM 60 in cents
    ];

    private const LABELS = [
        'basic' => 'KLP48 Fanclub — Basic (1 Year)',
        'gold'  => 'KLP48 Fanclub — Gold (1 Year)',
    ];

    // ─────────────────────────────────────────────────────────────────
    // NEW REGISTRATION FLOW  (public — no auth required)
    // ─────────────────────────────────────────────────────────────────

    public function preRegister(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => [
                'required', 'email',
                // Must not already have a full account
                'unique:fanclub_members,email',
                // Must not already have an unprocessed pending registration
                function ($attr, $value, $fail) {
                    $exists = FanclubPendingRegistration::where('email', $value)
                        ->whereNull('processed_at')
                        ->exists();
                    if ($exists) {
                        $fail('A registration is already pending for this email. Complete payment or contact support.');
                    }
                },
            ],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'tier'                  => ['required', 'in:basic,gold'],
        ]);

        $tier   = $data['tier'];
        $amount = self::PRICES[$tier];
        $refNo  = 'FCREG-' . strtoupper(substr(md5($data['email'] . time()), 0, 8));

        $returnUrl   = rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/fanclub/payment/return';
        $callbackUrl = rtrim(config('app.url'), '/') . '/api/fan/payment/callback';

        $response = $this->callToyyibPay([
            'billName'                => self::LABELS[$tier],
            'billDescription'         => self::LABELS[$tier],
            'billAmount'              => $amount,
            'billReturnUrl'           => $returnUrl,
            'billCallbackUrl'         => $callbackUrl,
            'billExternalReferenceNo' => $refNo,
            'billTo'                  => $data['name'],
            'billEmail'               => $data['email'],
            'billPhone'               => $data['phone'] ?? '',
        ]);

        if (! $response) {
            return response()->json(['message' => 'Payment gateway error. Please try again.'], 502);
        }

        $billCode = $response['BillCode'];

        FanclubPendingRegistration::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'] ?? null,
            'password'     => Hash::make($data['password']),
            'tier'         => $tier,
            'amount_cents' => $amount,
            'bill_code'    => $billCode,
            'reference_no' => $refNo,
        ]);

        return response()->json([
            'billUrl'  => rtrim(config('services.toyyibpay.url'), '/') . '/' . $billCode,
            'billCode' => $billCode,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // RENEWAL FLOW  (requires auth)
    // ─────────────────────────────────────────────────────────────────

    public function createBill(Request $request): JsonResponse
    {
        $request->validate(['tier' => ['required', 'in:basic,gold']]);

        /** @var FanclubMember $member */
        $member = $request->user();
        $tier   = $request->tier;
        $amount = self::PRICES[$tier];
        $refNo  = 'FCREN-' . $member->id . '-' . time();

        $returnUrl   = rtrim(config('app.frontend_url', 'http://localhost:5173'), '/') . '/fanclub/payment/return';
        $callbackUrl = rtrim(config('app.url'), '/') . '/api/fan/payment/callback';

        $response = $this->callToyyibPay([
            'billName'                => self::LABELS[$tier],
            'billDescription'         => self::LABELS[$tier],
            'billAmount'              => $amount,
            'billReturnUrl'           => $returnUrl,
            'billCallbackUrl'         => $callbackUrl,
            'billExternalReferenceNo' => $refNo,
            'billTo'                  => $member->name,
            'billEmail'               => $member->email,
            'billPhone'               => $member->phone ?? '',
        ]);

        if (! $response) {
            return response()->json(['message' => 'Payment gateway error. Please try again.'], 502);
        }

        $billCode = $response['BillCode'];

        FanclubSubscription::create([
            'fanclub_member_id' => $member->id,
            'tier'              => $tier,
            'amount_cents'      => $amount,
            'bill_code'         => $billCode,
            'reference_no'      => $refNo,
            'status'            => 'pending',
        ]);

        return response()->json([
            'billUrl'  => rtrim(config('services.toyyibpay.url'), '/') . '/' . $billCode,
            'billCode' => $billCode,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // TOYYIBPAY CALLBACK  (server-to-server POST)
    // ─────────────────────────────────────────────────────────────────

    public function callback(Request $request): string
    {
        $billCode      = $request->input('billcode');
        $status        = (int) $request->input('status');
        $transactionId = $request->input('transaction_id');

        // 1. Check pending registrations first
        $pending = FanclubPendingRegistration::where('bill_code', $billCode)
            ->whereNull('processed_at')
            ->first();

        if ($pending) {
            if ($status === 1) {
                $this->activateRegistration($pending, $transactionId);
            }
            // status 2 = pending payment, status 3 = failed — do nothing, user can retry
            return 'OK';
        }

        // 2. Fall back to renewal subscriptions
        $subscription = FanclubSubscription::where('bill_code', $billCode)->first();

        if (! $subscription) {
            Log::warning('ToyyibPay callback: unknown bill_code', ['billcode' => $billCode]);
            return 'OK';
        }

        if ($status === 1) {
            $subscription->update([
                'status'         => 'paid',
                'transaction_id' => $transactionId,
                'paid_at'        => now(),
            ]);
            $this->activateMember($subscription->member, $subscription->tier);
        } elseif ($status === 3) {
            $subscription->update(['status' => 'failed']);
        }

        return 'OK';
    }

    // ─────────────────────────────────────────────────────────────────
    // STATUS CHECK  (public — called from payment return page)
    // ─────────────────────────────────────────────────────────────────

    public function status(Request $request): JsonResponse
    {
        $request->validate(['bill_code' => ['required', 'string']]);
        $billCode = $request->bill_code;

        // ── Registration flow ──
        $pending = FanclubPendingRegistration::where('bill_code', $billCode)->first();

        if ($pending) {
            if ($pending->isProcessed()) {
                return response()->json([
                    'status' => 'paid',
                    'type'   => 'registration',
                    'tier'   => $pending->tier,
                    'email'  => $pending->email,
                ]);
            }

            // Not yet processed — poll ToyyibPay
            $paid = $this->pollToyyibPayForRegistration($pending);

            if ($paid) {
                return response()->json([
                    'status' => 'paid',
                    'type'   => 'registration',
                    'tier'   => $pending->tier,
                    'email'  => $pending->email,
                ]);
            }

            return response()->json([
                'status' => 'pending',
                'type'   => 'registration',
                'tier'   => $pending->tier,
                'email'  => $pending->email,
            ]);
        }

        // ── Renewal flow ──
        $subscription = FanclubSubscription::where('bill_code', $billCode)->first();

        if (! $subscription) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($subscription->status === 'pending') {
            $this->pollToyyibPayForRenewal($subscription);
            $subscription->refresh();
        }

        return response()->json([
            'status' => $subscription->status,
            'type'   => 'renewal',
            'tier'   => $subscription->tier,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────

    private function callToyyibPay(array $params): ?array
    {
        try {
            $response = Http::asForm()->post(
                rtrim(config('services.toyyibpay.url'), '/') . '/index.php/api/createBill',
                array_merge([
                    'userSecretKey'        => config('services.toyyibpay.secret_key'),
                    'categoryCode'         => config('services.toyyibpay.category_code'),
                    'billPriceSetting'     => 1,
                    'billPayorInfo'        => 1,
                    'billSplitPayment'     => 0,
                    'billSplitPaymentArgs' => '',
                    'billPaymentChannel'   => 2,
                    'billContentEmail'     => 'Thank you for joining KLP48 Fanclub! Your membership will be activated upon payment confirmation.',
                    'billChargeToCustomer' => 1,
                ], $params)
            );

            if (! $response->successful()) {
                Log::error('ToyyibPay createBill failed', ['response' => $response->body()]);
                return null;
            }

            $result = $response->json();

            if (empty($result[0]['BillCode'])) {
                Log::error('ToyyibPay no BillCode', ['response' => $result]);
                return null;
            }

            return $result[0];
        } catch (\Throwable $e) {
            Log::error('ToyyibPay request exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function activateRegistration(FanclubPendingRegistration $pending, ?string $transactionId): void
    {
        $member = FanclubMember::create([
            'name'       => $pending->name,
            'email'      => $pending->email,
            'phone'      => $pending->phone,
            'password'   => $pending->password, // already hashed
            'tier'       => $pending->tier,
            'status'     => 'active',
            'joined_at'  => now()->toDateString(),
            'expires_at' => now()->addYear()->toDateString(),
            'benefits'   => $this->defaultBenefits($pending->tier),
        ]);

        // Record the subscription
        FanclubSubscription::create([
            'fanclub_member_id' => $member->id,
            'tier'              => $pending->tier,
            'amount_cents'      => $pending->amount_cents,
            'bill_code'         => $pending->bill_code,
            'reference_no'      => $pending->reference_no,
            'transaction_id'    => $transactionId,
            'status'            => 'paid',
            'paid_at'           => now(),
        ]);

        $pending->update(['processed_at' => now()]);
    }

    private function activateMember(FanclubMember $member, string $tier): void
    {
        $member->update([
            'tier'       => $tier,
            'status'     => 'active',
            'expires_at' => now()->addYear()->toDateString(),
            'benefits'   => $this->defaultBenefits($tier),
        ]);
    }

    private function pollToyyibPayForRegistration(FanclubPendingRegistration $pending): bool
    {
        try {
            $response = Http::asForm()->post(
                rtrim(config('services.toyyibpay.url'), '/') . '/index.php/api/getBillTransactions',
                [
                    'userSecretKey' => config('services.toyyibpay.secret_key'),
                    'billCode'      => $pending->bill_code,
                ]
            );

            if (! $response->successful()) return false;

            $transactions = $response->json();
            if (empty($transactions)) return false;

            foreach ($transactions as $tx) {
                if (($tx['billpaymentStatus'] ?? '') === '1') {
                    $this->activateRegistration($pending, $tx['billpaymentInvoiceNo'] ?? null);
                    return true;
                }
            }
        } catch (\Throwable $e) {
            Log::error('ToyyibPay poll (registration) failed', ['error' => $e->getMessage()]);
        }

        return false;
    }

    private function pollToyyibPayForRenewal(FanclubSubscription $subscription): void
    {
        try {
            $response = Http::asForm()->post(
                rtrim(config('services.toyyibpay.url'), '/') . '/index.php/api/getBillTransactions',
                [
                    'userSecretKey' => config('services.toyyibpay.secret_key'),
                    'billCode'      => $subscription->bill_code,
                ]
            );

            if (! $response->successful()) return;

            $transactions = $response->json();
            if (empty($transactions)) return;

            foreach ($transactions as $tx) {
                if (($tx['billpaymentStatus'] ?? '') === '1') {
                    $subscription->update([
                        'status'         => 'paid',
                        'transaction_id' => $tx['billpaymentInvoiceNo'] ?? null,
                        'paid_at'        => now(),
                    ]);
                    $this->activateMember($subscription->member, $subscription->tier);
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::error('ToyyibPay poll (renewal) failed', ['error' => $e->getMessage()]);
        }
    }

    private function defaultBenefits(string $tier): array
    {
        $base = ['Newsletter', 'Digital wallpaper'];
        if ($tier === 'gold') {
            $base = array_merge($base, ['Priority ticketing', 'Exclusive merch discount', 'Discord Radio Talk']);
        }
        return $base;
    }
}
