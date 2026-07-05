<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesMediaUploads;
use App\Http\Controllers\Admin\Concerns\SavesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VideoController extends Controller
{
    use SavesTranslations, HandlesMediaUploads;

    private array $translatableFields = ['title', 'description'];

    public function index(): Response
    {
        return Inertia::render('Admin/Videos/Index', [
            'videos' => Video::orderByDesc('date')->get([
                'id', 'slug', 'title', 'type', 'date', 'youtube_id', 'thumbnail',
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Videos/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'unique:videos,slug'],
            'type'        => ['required', 'in:music-video,performance,dance-practice,behind-the-scenes'],
            'date'        => ['required', 'date'],
            'youtube_id'  => ['nullable', 'string', 'max:255'],
            'duration'    => ['nullable', 'string', 'max:50'],
            'venue'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail'   => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->storeMedia($request->file('thumbnail'), 'videos');
        }

        $video = Video::create($data);
        $this->saveTranslations($video, $request->all(), $this->translatableFields);

        return redirect()->route('admin.videos.index')->with('success', 'Video created.');
    }

    public function edit(Video $video): Response
    {
        return Inertia::render('Admin/Videos/Edit', [
            'video'        => $video,
            'translations' => $this->loadTranslations($video, $this->translatableFields),
            'thumbnailUrl' => $this->mediaUrl($video->thumbnail),
        ]);
    }

    public function update(Request $request, Video $video): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', "unique:videos,slug,{$video->id}"],
            'type'        => ['required', 'in:music-video,performance,dance-practice,behind-the-scenes'],
            'date'        => ['required', 'date'],
            'youtube_id'  => ['nullable', 'string', 'max:255'],
            'duration'    => ['nullable', 'string', 'max:50'],
            'venue'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail'   => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $this->deleteMedia($video->thumbnail);
            $data['thumbnail'] = $this->storeMedia($request->file('thumbnail'), 'videos');
        } else {
            unset($data['thumbnail']);
        }

        $video->update($data);
        $this->saveTranslations($video, $request->all(), $this->translatableFields);

        return redirect()->route('admin.videos.index')->with('success', 'Video updated.');
    }

    public function destroy(Video $video): RedirectResponse
    {
        $this->deleteMedia($video->thumbnail);
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video deleted.');
    }
}
