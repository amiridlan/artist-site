<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesMediaUploads;
use App\Http\Controllers\Admin\Concerns\SavesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    use SavesTranslations, HandlesMediaUploads;

    private array $translatableFields = ['bio', 'hometown'];

    public function index(): Response
    {
        return Inertia::render('Admin/Members/Index', [
            'members' => Member::orderBy('sort_order')->orderBy('name_english')
                ->get(['id', 'slug', 'name_english', 'name_native', 'photo', 'generation', 'status', 'sort_order'])
                ->map(fn ($m) => array_merge($m->toArray(), [
                    'photo_url' => $this->mediaUrl($m->photo),
                ])),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Members/Create', [
            'generationOptions' => $this->generationOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name_english' => ['required', 'string', 'max:255'],
            'name_native'  => ['nullable', 'string', 'max:255'],
            'nickname'     => ['nullable', 'string', 'max:255'],
            'slug'         => ['required', 'string', 'max:255', 'unique:members,slug'],
            'generation'   => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9 ]+$/'],
            'status'       => ['required', 'in:active,graduated,concluded'],
            'birthdate'    => ['nullable', 'string', 'max:100'],
            'age'          => ['nullable', 'integer'],
            'height'       => ['nullable', 'integer'],
            'blood_type'   => ['nullable', 'string', 'max:10'],
            'hometown'     => ['nullable', 'string', 'max:255'],
            'color'        => ['nullable', 'string', 'max:20'],
            'sort_order'   => ['nullable', 'integer'],
            'join_date'    => ['nullable', 'date'],
            'bio'          => ['nullable', 'string'],
            'hobbies'      => ['nullable', 'array'],
            'social'       => ['nullable', 'array'],
            'photo'        => ['nullable', 'image', 'max:4096'],
            'cover_image'  => ['nullable', 'image', 'max:4096'],
        ]);

        $photoFile = $request->file('photo');
        $coverFile = $request->file('cover_image');
        unset($data['photo'], $data['cover_image']);

        $member = Member::create($data);
        $this->saveTranslations($member, $request->all(), $this->translatableFields);
        Cache::flush();

        if ($photoFile) {
            $this->queueImageUpload($member, 'photo', $photoFile, 'members', sizes: ['thumbnail', 'small', 'medium']);
        }
        if ($coverFile) {
            $this->queueImageUpload($member, 'cover_image', $coverFile, 'members/covers');
        }

        return redirect()->route('admin.members.index')->with('success', 'Member created.');
    }

    public function show(Member $member): Response
    {
        $member->load('documents.uploadedBy', 'contracts');

        return Inertia::render('Admin/Members/Show', [
            'member'       => $member,
            'translations' => $this->loadTranslations($member, $this->translatableFields),
            'photoUrl'     => $this->mediaUrl($member->photo),
            'coverUrl'     => $this->mediaUrl($member->cover_image),
        ]);
    }

    public function edit(Member $member): Response
    {
        return Inertia::render('Admin/Members/Edit', [
            'member'            => $member,
            'translations'      => $this->loadTranslations($member, $this->translatableFields),
            'photoUrl'          => $this->mediaUrl($member->photo),
            'coverUrl'          => $this->mediaUrl($member->cover_image),
            'generationOptions' => $this->generationOptions(),
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $request->validate([
            'name_english' => ['required', 'string', 'max:255'],
            'name_native'  => ['nullable', 'string', 'max:255'],
            'nickname'     => ['nullable', 'string', 'max:255'],
            'slug'         => ['required', 'string', 'max:255', "unique:members,slug,{$member->id}"],
            'generation'   => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9 ]+$/'],
            'status'       => ['required', 'in:active,graduated,concluded'],
            'birthdate'    => ['nullable', 'string', 'max:100'],
            'age'          => ['nullable', 'integer'],
            'height'       => ['nullable', 'integer'],
            'blood_type'   => ['nullable', 'string', 'max:10'],
            'hometown'     => ['nullable', 'string', 'max:255'],
            'color'        => ['nullable', 'string', 'max:20'],
            'sort_order'   => ['nullable', 'integer'],
            'join_date'    => ['nullable', 'date'],
            'bio'          => ['nullable', 'string'],
            'hobbies'      => ['nullable', 'array'],
            'social'       => ['nullable', 'array'],
            'photo'        => ['nullable', 'image', 'max:4096'],
            'cover_image'  => ['nullable', 'image', 'max:4096'],
        ]);

        $photoFile = $request->file('photo');
        $coverFile = $request->file('cover_image');
        $previousPhoto = $member->photo;
        $previousCover = $member->cover_image;
        unset($data['photo'], $data['cover_image']);

        $member->update($data);
        $this->saveTranslations($member, $request->all(), $this->translatableFields);
        Cache::flush();

        if ($photoFile) {
            $this->queueImageUpload($member, 'photo', $photoFile, 'members', $previousPhoto, ['thumbnail', 'small', 'medium']);
        }
        if ($coverFile) {
            $this->queueImageUpload($member, 'cover_image', $coverFile, 'members/covers', $previousCover);
        }

        return redirect()->route('admin.members.index')->with('success', 'Member updated.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->deleteMedia($member->photo);
        $this->deleteMedia($member->cover_image);
        $member->delete();
        Cache::flush();

        return redirect()->route('admin.members.index')->with('success', 'Member deleted.');
    }

    /**
     * Distinct generation values already in use, so staff can pick an existing
     * one or add a new one from the admin form instead of a hardcoded list.
     */
    private function generationOptions(): array
    {
        $generations = Member::query()
            ->whereNotNull('generation')
            ->distinct()
            ->orderBy('generation')
            ->pluck('generation');

        if ($generations->isEmpty()) {
            $generations = collect(['1st', '2nd']);
        }

        return $generations
            ->map(fn (string $g) => ['value' => $g, 'label' => "{$g} Generation"])
            ->values()
            ->all();
    }
}
