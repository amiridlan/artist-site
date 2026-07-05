<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $lang = $request->query('lang', 'en');
        $type = $request->query('type', 'all');
        $status = $request->query('status', 'all');

        $cacheKey = "events:{$lang}:{$type}:{$status}";

        $events = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Event::orderByDesc('date');

            if ($request->has('type') && $request->type !== 'all') {
                $query->type($request->type);
            }

            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return $query->get();
        });

        return EventResource::collection($events);
    }
}
