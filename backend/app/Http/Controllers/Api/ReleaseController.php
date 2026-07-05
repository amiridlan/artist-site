<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ReleaseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $lang = $request->query('lang', 'en');
        $type = $request->query('type', 'all');

        $cacheKey = "releases:{$lang}:{$type}";

        $releases = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Release::orderByDesc('release_date');

            if ($request->has('type') && $request->type !== 'all') {
                $query->type($request->type);
            }

            return $query->get();
        });

        return ReleaseResource::collection($releases);
    }
}
