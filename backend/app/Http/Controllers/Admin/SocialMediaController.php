<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaContentItem;
use App\Models\SocialMediaGeoStat;
use App\Models\SocialMediaPlatform;
use App\Models\SocialMediaSnapshot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class SocialMediaController extends Controller
{
    public function index(): Response
    {
        $platforms = SocialMediaPlatform::with('latestSnapshot')->get();

        $platformData = $platforms->map(fn ($p) => $this->buildPlatformSummary($p));

        $lastFetched = SocialMediaSnapshot::orderByDesc('fetched_at')->value('fetched_at');

        return Inertia::render('Admin/SocialMedia/Index', [
            'platforms'   => $platformData,
            'lastFetched' => $lastFetched,
        ]);
    }

    public function show(string $slug): Response
    {
        $platform = SocialMediaPlatform::where('platform', $slug)->firstOrFail();
        $latest   = $platform->latestSnapshot;

        $monthAgo = $platform->snapshots()
            ->where('fetched_at', '<=', Carbon::now()->subDays(30))
            ->orderByDesc('fetched_at')
            ->first();

        $delta    = ($latest && $monthAgo) ? $latest->followers - $monthAgo->followers : 0;
        $deltaPct = ($monthAgo && $monthAgo->followers > 0)
            ? round($delta / $monthAgo->followers * 100, 2)
            : 0;

        // 90-day follower trend
        $trend = $platform->snapshots()
            ->where('fetched_at', '>=', Carbon::now()->subDays(89))
            ->orderBy('fetched_at')
            ->get(['followers', 'fetched_at'])
            ->map(fn ($s) => [
                'date'      => $s->fetched_at->format('M d'),
                'followers' => $s->followers,
            ])->values();

        // Content items ordered by performance (views desc)
        $contentItems = SocialMediaContentItem::where('platform_id', $platform->id)
            ->orderByDesc('published_at')
            ->get()
            ->map(fn ($item) => [
                'id'           => $item->id,
                'content_id'   => $item->content_id,
                'type'         => $item->type,
                'title'        => $item->title,
                'published_at' => $item->published_at->toDateString(),
                'metrics'      => $item->metrics,
            ])->values();

        // Top countries
        $geoStats = SocialMediaGeoStat::where('platform_id', $platform->id)
            ->orderByDesc('value')
            ->get(['country_name', 'country_code', 'value', 'percentage'])
            ->values();

        // Platform-specific extras from latest snapshot extra_data
        $extras = $latest?->extra_data ?? [];

        return Inertia::render('Admin/SocialMedia/Show', [
            'platform' => [
                'id'           => $platform->id,
                'platform'     => $platform->platform,
                'display_name' => $platform->display_name,
                'handle'       => $platform->handle,
                'current'      => $latest ? [
                    'followers'  => $latest->followers,
                    'views'      => $latest->views,
                    'posts'      => $latest->posts,
                    'likes'      => $latest->likes,
                    'fetched_at' => $latest->fetched_at?->toDateTimeString(),
                ] : null,
                'delta' => ['followers' => $delta, 'followers_pct' => $deltaPct],
                'trend' => $trend,
            ],
            'contentItems' => $contentItems,
            'geoStats'     => $geoStats,
            'extras'       => $extras,
        ]);
    }

    public function sync(): RedirectResponse
    {
        Artisan::call('social-media:fetch');
        return back()->with('success', 'Social media stats synced successfully.');
    }

    private function buildPlatformSummary(SocialMediaPlatform $platform): array
    {
        $latest   = $platform->latestSnapshot;
        $monthAgo = $platform->snapshots()
            ->where('fetched_at', '<=', Carbon::now()->subDays(30))
            ->orderByDesc('fetched_at')
            ->first();

        $delta    = ($latest && $monthAgo) ? $latest->followers - $monthAgo->followers : 0;
        $deltaPct = ($monthAgo && $monthAgo->followers > 0)
            ? round($delta / $monthAgo->followers * 100, 2)
            : 0;

        $chart = $platform->snapshots()
            ->where('fetched_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('fetched_at')
            ->get(['followers', 'fetched_at'])
            ->map(fn ($s) => ['date' => $s->fetched_at->format('M d'), 'followers' => $s->followers])
            ->values();

        return [
            'id'           => $platform->id,
            'platform'     => $platform->platform,
            'display_name' => $platform->display_name,
            'handle'       => $platform->handle,
            'is_active'    => $platform->is_active,
            'current'      => $latest ? [
                'followers'  => $latest->followers,
                'views'      => $latest->views,
                'posts'      => $latest->posts,
                'likes'      => $latest->likes,
                'fetched_at' => $latest->fetched_at?->toDateTimeString(),
            ] : null,
            'delta' => ['followers' => $delta, 'followers_pct' => $deltaPct],
            'chart' => $chart,
        ];
    }
}
