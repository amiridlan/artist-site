<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConflictLog;
use App\Models\FanclubSubscription;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        $this->authorize('view-reports');

        return Inertia::render('Admin/Reports/Index', [
            'scheduleLoad' => $this->scheduleLoadPerMember(),
            'conflictTrend' => $this->conflictTrend(),
            'conflictsByType' => $this->conflictsByType(),
            'revenueTrend' => $this->revenueTrend(),
        ]);
    }

    /**
     * Confirmed schedule events per active member over the trailing 90 days.
     */
    private function scheduleLoadPerMember(): array
    {
        $since = Carbon::now()->subDays(90);

        return Member::query()
            ->where('status', 'active')
            ->withCount(['scheduleEvents' => fn ($q) => $q->confirmed()->where('start_datetime', '>=', $since)])
            ->orderByDesc('schedule_events_count')
            ->get(['id', 'name_english'])
            ->map(fn (Member $m) => ['member' => $m->name_english, 'count' => $m->schedule_events_count])
            ->all();
    }

    /**
     * Conflict logs per month over the trailing 12 months. Reflects conflicts
     * logged at kanban confirm-time only (see ConflictDetectionService::logConflicts()),
     * not every conflict flagged during scheduling.
     */
    private function conflictTrend(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(11);

        $byMonth = ConflictLog::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $start)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        return collect(range(0, 11))->map(function ($i) use ($start, $byMonth) {
            $key = $start->copy()->addMonths($i)->format('Y-m');
            return ['month' => $key, 'count' => (int) ($byMonth[$key] ?? 0)];
        })->all();
    }

    private function conflictsByType(): array
    {
        return ConflictLog::select('conflict_type', DB::raw('COUNT(*) as count'))
            ->groupBy('conflict_type')
            ->pluck('count', 'conflict_type')
            ->all();
    }

    /**
     * Fanclub subscription revenue (RM) per month by tier, trailing 36 months.
     * Uses paid_at (revenue-recognition date), not created_at, since a
     * subscription can be created pending and paid later.
     */
    private function revenueTrend(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(35);

        $rows = FanclubSubscription::where('status', 'paid')
            ->where('paid_at', '>=', $start)
            ->select(
                DB::raw("TO_CHAR(paid_at, 'YYYY-MM') as month"),
                'tier',
                DB::raw('SUM(amount_cents) as total_cents')
            )
            ->groupBy('month', 'tier')
            ->orderBy('month')
            ->get();

        return collect(range(0, 35))->map(function ($i) use ($start, $rows) {
            $key = $start->copy()->addMonths($i)->format('Y-m');
            $monthRows = $rows->where('month', $key);
            return [
                'month' => $key,
                'basic' => (int) ($monthRows->firstWhere('tier', 'basic')?->total_cents ?? 0) / 100,
                'gold' => (int) ($monthRows->firstWhere('tier', 'gold')?->total_cents ?? 0) / 100,
            ];
        })->all();
    }
}
