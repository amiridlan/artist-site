<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Release;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ReleaseController extends Controller
{
    use SavesTranslations;

    private array $translatableFields = ['title', 'description'];

    public function index(): Response
    {
        return Inertia::render('Admin/Releases/Index', [
            'releases' => Release::orderByDesc('release_date')->get([
                'id', 'slug', 'title', 'type', 'release_date', 'cover_image',
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Releases/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'slug'            => ['required', 'string', 'max:255', 'unique:releases,slug'],
            'type'            => ['required', 'in:single,album,ep,digital-single'],
            'release_date'    => ['required', 'date'],
            'description'     => ['nullable', 'string'],
            'tracks'          => ['required', 'array', 'min:1'],
            'tracks.*.number' => ['required', 'integer'],
            'tracks.*.title'  => ['required', 'string'],
            'streaming_links' => ['nullable', 'array'],
            'cover_image'     => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('releases', 'public');
        }

        $release = Release::create($data);
        $this->saveTranslations($release, $request->all(), $this->translatableFields);

        return redirect()->route('admin.releases.index')->with('success', 'Release created.');
    }

    public function edit(Release $release): Response
    {
        return Inertia::render('Admin/Releases/Edit', [
            'release'      => $release,
            'translations' => $this->loadTranslations($release, $this->translatableFields),
            'coverUrl'     => $release->cover_image ? Storage::url($release->cover_image) : null,
        ]);
    }

    public function update(Request $request, Release $release): RedirectResponse
    {
        $data = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'slug'            => ['required', 'string', 'max:255', "unique:releases,slug,{$release->id}"],
            'type'            => ['required', 'in:single,album,ep,digital-single'],
            'release_date'    => ['required', 'date'],
            'description'     => ['nullable', 'string'],
            'tracks'          => ['required', 'array', 'min:1'],
            'tracks.*.number' => ['required', 'integer'],
            'tracks.*.title'  => ['required', 'string'],
            'streaming_links' => ['nullable', 'array'],
            'cover_image'     => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($release->cover_image) Storage::disk('public')->delete($release->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('releases', 'public');
        } else {
            unset($data['cover_image']);
        }

        $release->update($data);
        $this->saveTranslations($release, $request->all(), $this->translatableFields);

        return redirect()->route('admin.releases.index')->with('success', 'Release updated.');
    }

    public function destroy(Release $release): RedirectResponse
    {
        if ($release->cover_image) Storage::disk('public')->delete($release->cover_image);
        $release->delete();

        return redirect()->route('admin.releases.index')->with('success', 'Release deleted.');
    }
}
