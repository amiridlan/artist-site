<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReleaseResource;
use App\Models\Release;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReleaseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Release::orderByDesc('release_date');

        if ($request->has('type') && $request->type !== 'all') {
            $query->type($request->type);
        }

        return ReleaseResource::collection($query->get());
    }
}
