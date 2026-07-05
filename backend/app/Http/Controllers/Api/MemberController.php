<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class MemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $lang = $request->query('lang', 'en');
        $generation = $request->query('generation', 'all');
        $status = $request->query('status', 'all');

        $cacheKey = "members:{$lang}:{$generation}:{$status}";

        $members = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Member::ordered();

            if ($request->has('generation') && $request->generation !== 'all') {
                $query->generation($request->generation);
            }

            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return $query->get();
        });

        return MemberResource::collection($members);
    }

    public function show(Request $request, string $slug): MemberResource
    {
        $lang = $request->query('lang', 'en');
        $cacheKey = "member:{$slug}:{$lang}";

        $member = Cache::remember($cacheKey, 300, function () use ($slug) {
            return Member::where('slug', $slug)->firstOrFail();
        });

        return new MemberResource($member);
    }
}
