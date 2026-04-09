<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    use SavesTranslations;

    private array $translatableFields = ['bio', 'hometown'];

    public function index(): Response
    {
        return Inertia::render('Admin/Members/Index', [
            'members' => Member::orderBy('sort_order')->orderBy('name_english')->get([
                'id', 'slug', 'name_english', 'name_native', 'photo', 'generation', 'status', 'sort_order',
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Members/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name_english' => ['required', 'string', 'max:255'],
            'name_native'  => ['nullable', 'string', 'max:255'],
            'nickname'     => ['nullable', 'string', 'max:255'],
            'slug'         => ['required', 'string', 'max:255', 'unique:members,slug'],
            'generation'   => ['required', 'in:1st,2nd'],
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

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('members/covers', 'public');
        }

        $member = Member::create($data);
        $this->saveTranslations($member, $request->all(), $this->translatableFields);

        return redirect()->route('admin.members.index')->with('success', 'Member created.');
    }

    public function edit(Member $member): Response
    {
        return Inertia::render('Admin/Members/Edit', [
            'member'       => $member,
            'translations' => $this->loadTranslations($member, $this->translatableFields),
            'photoUrl'     => $member->photo ? Storage::url($member->photo) : null,
            'coverUrl'     => $member->cover_image ? Storage::url($member->cover_image) : null,
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $request->validate([
            'name_english' => ['required', 'string', 'max:255'],
            'name_native'  => ['nullable', 'string', 'max:255'],
            'nickname'     => ['nullable', 'string', 'max:255'],
            'slug'         => ['required', 'string', 'max:255', "unique:members,slug,{$member->id}"],
            'generation'   => ['required', 'in:1st,2nd'],
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

        if ($request->hasFile('photo')) {
            if ($member->photo) Storage::disk('public')->delete($member->photo);
            $data['photo'] = $request->file('photo')->store('members', 'public');
        } else {
            unset($data['photo']);
        }

        if ($request->hasFile('cover_image')) {
            if ($member->cover_image) Storage::disk('public')->delete($member->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('members/covers', 'public');
        } else {
            unset($data['cover_image']);
        }

        $member->update($data);
        $this->saveTranslations($member, $request->all(), $this->translatableFields);

        return redirect()->route('admin.members.index')->with('success', 'Member updated.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->photo) Storage::disk('public')->delete($member->photo);
        if ($member->cover_image) Storage::disk('public')->delete($member->cover_image);
        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Member deleted.');
    }
}
