<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VideoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Video::orderByDesc('date');

        if ($request->has('type') && $request->type !== 'all') {
            $query->type($request->type);
        }

        return VideoResource::collection($query->get());
    }
}
