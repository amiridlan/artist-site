<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanclubMember;
use App\Models\FanclubPendingRegistration;
use App\Models\FanclubSubscription;
use App\Services\BillplzService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public function __construct(private readonly BillplzService $billplz)
    {
    }

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

        $returnUrl   = rtrim(config('app.frontend_url', 'http://localhost:3000'), '/') . '/fanclub/payment/return';
        $callbackUrl = rtrim(config('app.url'), '/') . '/api/fan/payment/callback';

        $bill = $this->billplz->createBill([
            'description'      => self::LABELS[$tier],
            'amount'            => $amount,
            'redirect_url'      => $returnUrl,
            'callback_url'      => $callbackUrl,
            'reference_1_label' => 'Reference No',
            'reference_1'       => $refNo,
            'name'              => $data['name'],
            'email'             => $data['email'],
            'mobile'            => $data['phone'] ?? '',
        ]);

        if (! $bill) {
            return response()->json(['message' => 'Payment gateway error. Please try again.'], 502);
        }

        $billId = $bill['id'];

        FanclubPendingRegistration::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'] ?? null,
            'password'     => Hash::make($data['password']),
            'tier'         => $tier,
            'amount_cents' => $amount,
            'bill_code'    => $billId,
            'reference_no' => $refNo,
        ]);

        return response()->json([
            'billUrl'  => $bill['url'],
            'billCode' => $billId,
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

        $returnUrl   = rtrim(config('app.frontend_url', 'http://localhost:3000'), '/') . '/fanclub/payment/return';
        $callbackUrl = rtrim(config('app.url'), '/') . '/api/fan/payment/callback';

        $bill = $this->billplz->createBill([
            'description'       => self::LABELS[$tier],
            'amount'            => $amount,
            'redirect_url'      => $returnUrl,
            'callback_url'      => $callbackUrl,
            'reference_1_label' => 'Reference No',
            'reference_1'       => $refNo,
            'name'              => $member->name,
            'email'             => $member->email,
            'mobile'            => $member->phone ?? '',
        ]);

        if (! $bill) {
            return response()->json(['message' => 'Payment gateway error. Please try again.'], 502);
        }

        $billId = $bill['id'];

        FanclubSubscription::create([
            'fanclub_member_id' => $member->id,
            'tier'              => $tier,
            'amount_cents'      => $amount,
            'bill_code'         => $billId,
            'reference_no'      => $refNo,
            'status'            => 'pending',
        ]);

        return response()->json([
            'billUrl'  => $bill['url'],
            'billCode' => $billId,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // BILLPLZ CALLBACK  (server-to-server POST)
    // ─────────────────────────────────────────────────────────────────

    public function callback(Request $request): string
    {
        $billId = $request->input('id');

        // Defense in depth, two independent layers:
        //   1. Verify the `x_signature` field Billplz sends with every
        //      callback (HMAC-SHA256 of the payload using the X Signature
        //      Key configured in the Billplz dashboard). A missing/invalid
        //      signature means the request did not genuinely come from
        //      Billplz (or the X Signature Key isn't configured yet) and we
        //      must not trust the posted `paid`/`state` fields.
        //   2. Regardless of signature outcome, we NEVER activate anything
        //      from the posted payload alone — we always re-query Billplz's
        //      "Get a Bill" API server-side for the authoritative payment
        //      status before granting a membership. This mirrors the same
        //      anti-fraud pattern used previously with ToyyibPay and covers
        //      us even if the signature check has an implementation bug.
        $keyConfigured   = filled(config('services.billplz.x_signature_key'));
        $signatureValid  = $this->billplz->verifySignature($request->all());

        if ($keyConfigured && ! $signatureValid) {
            // The X Signature Key IS configured, so a failed verification
            // means this callback is either forged or bill_code confusion —
            // not merely "unconfigured". Do not fall through to reconcile;
            // reject early. Still respond 'OK' so Billplz doesn't retry-storm.
            Log::warning('Billplz callback: signature verification failed', ['id' => $billId]);
            return 'OK';
        }

        if (! $signatureValid) {
            Log::warning('Billplz callback: x_signature key not configured, skipping verification', ['id' => $billId]);
            // Do not return early — still fall through to the authoritative
            // live re-query below, since the X Signature Key isn't set up
            // yet and there is nothing to verify against. The live re-query
            // is what actually gates activation.
        }

        if (! $billId) {
            Log::warning('Billplz callback: missing bill id');
            return 'OK';
        }

        $this->reconcileBill($billId);

        return 'OK';
    }

    // ─────────────────────────────────────────────────────────────────
    // STATUS CHECK  (public — called from payment return page)
    // ─────────────────────────────────────────────────────────────────

    public function status(Request $request): JsonResponse
    {
        $request->validate(['bill_code' => ['required', 'string']]);
        $billId = $request->bill_code;

        // ── Registration flow ──
        $pending = FanclubPendingRegistration::where('bill_code', $billId)->first();

        if ($pending) {
            if (! $pending->isProcessed()) {
                $this->reconcileBill($billId);
                $pending->refresh();
            }

            return response()->json([
                'status' => $pending->isProcessed() ? 'paid' : 'pending',
                'type'   => 'registration',
                'tier'   => $pending->tier,
                'email'  => $pending->email,
            ]);
        }

        // ── Renewal flow ──
        $subscription = FanclubSubscription::where('bill_code', $billId)->first();

        if (! $subscription) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($subscription->status === 'pending') {
            $this->reconcileBill($billId);
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

    /**
     * Single source of truth for "was this bill actually paid". Re-queries
     * Billplz's "Get a Bill" API directly (never trusts posted callback
     * data) and activates the matching registration/subscription if paid.
     */
    private function reconcileBill(string $billId): void
    {
        // 1. Check pending registrations first.
        $pending = FanclubPendingRegistration::where('bill_code', $billId)
            ->whereNull('processed_at')
            ->first();

        if ($pending) {
            $bill = $this->billplz->getBill($billId);
            if (! $this->billplz->isPaid($bill)) {
                return;
            }

            // The webhook callback and the frontend's status-poll can both
            // reach here for the same bill concurrently. Re-check under a
            // row lock inside a transaction so only one request actually
            // activates — the HTTP call to Billplz above happens *before*
            // the lock so we're not holding it during network I/O.
            DB::transaction(function () use ($billId, $bill) {
                $pending = FanclubPendingRegistration::where('bill_code', $billId)
                    ->whereNull('processed_at')
                    ->lockForUpdate()
                    ->first();

                if ($pending) {
                    $this->activateRegistration($pending, $bill['id'] ?? null);
                }
            });
            return;
        }

        // 2. Fall back to renewal subscriptions.
        $subscription = FanclubSubscription::where('bill_code', $billId)->first();

        if (! $subscription) {
            Log::warning('Billplz reconcile: unknown bill id', ['id' => $billId]);
            return;
        }

        if ($subscription->status !== 'pending') {
            return;
        }

        $bill = $this->billplz->getBill($billId);

        if ($this->billplz->isPaid($bill)) {
            DB::transaction(function () use ($billId, $bill) {
                $subscription = FanclubSubscription::where('bill_code', $billId)
                    ->lockForUpdate()
                    ->first();

                if ($subscription && $subscription->status === 'pending') {
                    $subscription->update([
                        'status'         => 'paid',
                        'transaction_id' => $bill['id'] ?? null,
                        'paid_at'        => now(),
                    ]);
                    $this->activateMember($subscription->member, $subscription->tier);
                }
            });
        } elseif ($bill !== null && ($bill['state'] ?? null) === 'deleted') {
            // A deleted bill can never be paid — safe to mark failed based
            // on the live API response (not the unauthenticated payload).
            $subscription->update(['status' => 'failed']);
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
        // Renewing before expiry stacks the new year on top of the remaining
        // time instead of forfeiting it; renewing after expiry (or with no
        // prior expiry) starts fresh from today.
        $stillValid = $member->expires_at && $member->expires_at->copy()->endOfDay()->isFuture();
        $base       = $stillValid ? $member->expires_at->copy() : now();

        $member->update([
            'tier'       => $tier,
            'status'     => 'active',
            'expires_at' => $base->addYear()->toDateString(),
            'benefits'   => $this->defaultBenefits($tier),
        ]);
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
