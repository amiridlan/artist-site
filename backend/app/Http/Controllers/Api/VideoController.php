<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class VideoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $lang = $request->query('lang', 'en');
        $type = $request->query('type', 'all');

        $cacheKey = "videos:{$lang}:{$type}";

        $videos = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Video::orderByDesc('date');

            if ($request->has('type') && $request->type !== 'all') {
                $query->type($request->type);
            }

            return $query->get();
        });

        return VideoResource::collection($videos);
    }
}
