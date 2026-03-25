<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Member::ordered();

        if ($request->has('generation') && $request->generation !== 'all') {
            $query->generation($request->generation);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return MemberResource::collection($query->get());
    }

    public function show(string $slug): MemberResource
    {
        $member = Member::where('slug', $slug)->firstOrFail();
        return new MemberResource($member);
    }
}
