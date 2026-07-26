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
        // Build query
        $query = News::published()->orderByDesc('date');

        if ($request->has('category') && $request->category !== 'all') {
            $query->category($request->category);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $articles = $query->get();

        return NewsResource::collection($articles);
    }

    public function show(Request $request, string $slug): NewsResource
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();

        return new NewsResource($article);
    }
}
