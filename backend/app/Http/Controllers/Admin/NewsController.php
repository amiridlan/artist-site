<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesTranslations;
use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    use SavesTranslations;

    private array $translatableFields = ['title', 'excerpt', 'content'];

    private function mediaDisk(): string
    {
        return config('filesystems.media_disk');
    }

    public function index(): Response
    {
        return Inertia::render('Admin/News/Index', [
            'articles' => News::orderByDesc('date')->get([
                'id', 'slug', 'title', 'category', 'date', 'featured', 'published',
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/News/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:255', 'unique:news,slug'],
            'category'  => ['required', 'in:news,fanclub,store,event,release'],
            'date'      => ['required', 'date'],
            'featured'  => ['boolean'],
            'published' => ['boolean'],
            'excerpt'   => ['required', 'string'],
            'content'   => ['nullable', 'string'],
            'image'     => ['nullable', 'image', 'max:4096'],
        ]);

        $disk = $this->mediaDisk();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', $disk);
        }

        $article = News::create($data);
        $this->saveTranslations($article, $request->all(), $this->translatableFields);

        return redirect()->route('admin.news.index')->with('success', 'Article created.');
    }

    public function edit(News $news): Response
    {
        $disk = $this->mediaDisk();

        return Inertia::render('Admin/News/Edit', [
            'article'      => $news,
            'translations' => $this->loadTranslations($news, $this->translatableFields),
            'imageUrl'     => $news->image ? Storage::disk($disk)->url($news->image) : null,
        ]);
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $data = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:255', "unique:news,slug,{$news->id}"],
            'category'  => ['required', 'in:news,fanclub,store,event,release'],
            'date'      => ['required', 'date'],
            'featured'  => ['boolean'],
            'published' => ['boolean'],
            'excerpt'   => ['required', 'string'],
            'content'   => ['nullable', 'string'],
            'image'     => ['nullable', 'image', 'max:4096'],
        ]);

        $disk = $this->mediaDisk();

        if ($request->hasFile('image')) {
            if ($news->image) Storage::disk($disk)->delete($news->image);
            $data['image'] = $request->file('image')->store('news', $disk);
        } else {
            unset($data['image']);
        }

        $news->update($data);
        $this->saveTranslations($news, $request->all(), $this->translatableFields);

        return redirect()->route('admin.news.index')->with('success', 'Article updated.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $disk = $this->mediaDisk();
        if ($news->image) Storage::disk($disk)->delete($news->image);
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Article deleted.');
    }
}
