<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanclubMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class FanAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $member = FanclubMember::where('email', $request->email)->first();

        if (! $member || ! Hash::check($request->password, $member->password)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        // Revoke previous tokens to avoid token bloat
        $member->tokens()->delete();

        $token = $member->createToken('fan-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->memberData($member),
        ]);
    }

    public function cancel(Request $request): JsonResponse
    {
        /** @var \App\Models\FanclubMember $member */
        $member = $request->user();

        if ($member->status !== 'active') {
            return response()->json(['message' => 'Membership is not currently active.'], 422);
        }

        $member->update(['status' => 'cancelled']);

        return response()->json(['user' => $this->memberData($member->fresh())]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->memberData($request->user())]);
    }

    private function memberData(FanclubMember $member): array
    {
        return [
            'id'         => $member->id,
            'name'       => $member->name,
            'email'      => $member->email,
            'phone'      => $member->phone,
            'tier'       => $member->tier,
            'status'     => $member->status,
            'benefits'   => $member->benefits ?? [],
            'joined_at'  => $member->joined_at?->toDateString(),
            'expires_at' => $member->expires_at?->toDateString(),
        ];
    }
}
