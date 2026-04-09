<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FanclubMember;
use App\Models\Member;
use App\Models\News;
use App\Models\Release;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Build cumulative fanclub signups by month going back 36 months
        $startDate = Carbon::now()->startOfMonth()->subMonths(35);

        $signupsByMonth = FanclubMember::select(
                DB::raw("TO_CHAR(joined_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('joined_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Build a full 36-month array with cumulative totals
        $totalBefore = FanclubMember::where('joined_at', '<', $startDate)->count();
        $months      = [];
        $cumulative  = $totalBefore;

        for ($i = 0; $i < 36; $i++) {
            $key        = $startDate->copy()->addMonths($i)->format('Y-m');
            $cumulative += (int) ($signupsByMonth[$key] ?? 0);
            $months[]   = ['month' => $key, 'total' => $cumulative];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'members'  => Member::count(),
                'news'     => News::count(),
                'releases' => Release::count(),
                'videos'   => Video::count(),
                'events'   => Event::count(),
                'fanclub'  => FanclubMember::where('status', 'active')->count(),
            ],
            'upcomingEvents' => Event::where('status', 'upcoming')
                ->orderBy('date')
                ->limit(5)
                ->get(['id', 'title', 'date', 'venue', 'status']),
            'fanclubChart' => $months,
        ]);
    }
}
