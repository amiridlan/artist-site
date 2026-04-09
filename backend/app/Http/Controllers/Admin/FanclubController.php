<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FanclubMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FanclubController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Fanclub/Index', [
            'members' => FanclubMember::orderByDesc('joined_at')->get(),
            'stats' => [
                'total'     => FanclubMember::count(),
                'active'    => FanclubMember::where('status', 'active')->count(),
                'basic'     => FanclubMember::where('tier', 'basic')->where('status', 'active')->count(),
                'gold'      => FanclubMember::where('tier', 'gold')->where('status', 'active')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Fanclub/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:fanclub_members,email'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'tier'       => ['required', 'in:basic,gold'],
            'status'     => ['required', 'in:active,expired,cancelled'],
            'benefits'   => ['nullable', 'array'],
            'joined_at'  => ['required', 'date'],
            'expires_at' => ['nullable', 'date'],
            'notes'      => ['nullable', 'string'],
        ]);

        FanclubMember::create($data);

        return redirect()->route('admin.fanclub.index')->with('success', 'Fanclub member added.');
    }

    public function edit(FanclubMember $fanclub): Response
    {
        return Inertia::render('Admin/Fanclub/Edit', [
            'member' => $fanclub,
        ]);
    }

    public function update(Request $request, FanclubMember $fanclub): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', "unique:fanclub_members,email,{$fanclub->id}"],
            'phone'      => ['nullable', 'string', 'max:50'],
            'tier'       => ['required', 'in:basic,gold'],
            'status'     => ['required', 'in:active,expired,cancelled'],
            'benefits'   => ['nullable', 'array'],
            'joined_at'  => ['required', 'date'],
            'expires_at' => ['nullable', 'date'],
            'notes'      => ['nullable', 'string'],
        ]);

        $fanclub->update($data);

        return redirect()->route('admin.fanclub.index')->with('success', 'Fanclub member updated.');
    }

    public function destroy(FanclubMember $fanclub): RedirectResponse
    {
        $fanclub->delete();

        return redirect()->route('admin.fanclub.index')->with('success', 'Fanclub member removed.');
    }
}
