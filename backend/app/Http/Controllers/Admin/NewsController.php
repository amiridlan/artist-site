<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesMediaUploads;
use App\Http\Controllers\Admin\Concerns\SavesTranslations;
use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    use SavesTranslations, HandlesMediaUploads;

    private array $translatableFields = ['title', 'excerpt', 'content'];

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

        $imageFile = $request->file('image');
        unset($data['image']);

        $article = News::create($data);
        $this->saveTranslations($article, $request->all(), $this->translatableFields);

        if ($imageFile) {
            $this->queueImageUpload($article, 'image', $imageFile, 'news');
        }

        return redirect()->route('admin.news.index')->with('success', 'Article created.');
    }

    public function edit(News $news): Response
    {
        return Inertia::render('Admin/News/Edit', [
            'article'      => $news,
            'translations' => $this->loadTranslations($news, $this->translatableFields),
            'imageUrl'     => $this->mediaUrl($news->image),
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

        $imageFile = $request->file('image');
        $previousImage = $news->image;
        unset($data['image']);

        $news->update($data);
        $this->saveTranslations($news, $request->all(), $this->translatableFields);

        if ($imageFile) {
            $this->queueImageUpload($news, 'image', $imageFile, 'news', $previousImage);
        }

        return redirect()->route('admin.news.index')->with('success', 'Article updated.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->deleteMedia($news->image);
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Article deleted.');
    }
}
