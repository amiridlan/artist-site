<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FanClubMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles protected fan club content access
 * Requires active subscription to view content
 */
class FanClubContentController extends Controller
{
    public function __construct(
        protected FanClubMediaService $fanClubMedia
    ) {
        // Require authentication for all routes
        $this->middleware('auth:sanctum');
    }

    /**
     * Get a temporary URL for protected content
     * Only users with active subscriptions can access
     *
     * GET /api/fan/content/{id}/access
     */
    public function getContentAccess(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        // Check if user has active subscription
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'error' => 'Active subscription required',
                'message' => 'Please subscribe to the fan club to access this content'
            ], 403);
        }

        // Fetch content record from database
        // Assuming you have a FanClubContent model
        // $content = FanClubContent::findOrFail($id);

        // Example: Get file path from content record
        $filePath = 'exclusive-photos/sample.jpg'; // Replace with actual path from $content->file_path

        // Check if file exists
        if (!$this->fanClubMedia->exists($filePath)) {
            return response()->json([
                'error' => 'Content not found'
            ], 404);
        }

        // Generate temporary signed URL (valid for 60 minutes)
        $signedUrl = $this->fanClubMedia->getTemporaryUrl($filePath, expirationMinutes: 60);

        return response()->json([
            'url' => $signedUrl,
            'expiresAt' => now()->addMinutes(60)->toIso8601String(),
            'contentType' => 'image', // Adjust based on actual content
        ]);
    }

    /**
     * List available fan club content (metadata only, no URLs)
     *
     * GET /api/fan/content
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if user has active subscription
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'error' => 'Active subscription required'
            ], 403);
        }

        // Example: Fetch fan club content
        // $content = FanClubContent::latest()->paginate(20);

        return response()->json([
            'data' => [
                // Your content items here
                // Don't include direct URLs - client must call /content/{id}/access
            ]
        ]);
    }
}
