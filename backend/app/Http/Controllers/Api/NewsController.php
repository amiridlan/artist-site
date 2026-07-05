<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $lang = $request->query('lang', 'en');
        $category = $request->query('category', 'all');
        $featured = $request->boolean('featured') ? '1' : '0';

        $cacheKey = "news:{$lang}:{$category}:{$featured}";

        $articles = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = News::published()->orderByDesc('date');

            if ($request->has('category') && $request->category !== 'all') {
                $query->category($request->category);
            }

            if ($request->boolean('featured')) {
                $query->featured();
            }

            return $query->get();
        });

        return NewsResource::collection($articles);
    }

    public function show(Request $request, string $slug): NewsResource
    {
        $lang = $request->query('lang', 'en');
        $cacheKey = "news:{$slug}:{$lang}";

        $article = Cache::remember($cacheKey, 300, function () use ($slug) {
            return News::published()->where('slug', $slug)->firstOrFail();
        });

        return new NewsResource($article);
    }
}
