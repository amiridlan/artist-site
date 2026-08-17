<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContractController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('view-contracts');

        $query = Contract::with('member');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $contracts = $query->orderBy('end_date')->paginate(20)->withQueryString();

        return Inertia::render('Admin/Contracts/Index', [
            'contracts' => $contracts,
            'filters' => $request->only(['status', 'member_id']),
            'members' => Member::orderBy('name_english')->get(['id', 'name_english']),
            'renewalLookaheadDays' => config('contracts.renewal_lookahead_days'),
        ]);
    }

    public function create(Member $member): Response
    {
        $this->authorize('manage-contracts');

        return Inertia::render('Admin/Contracts/Create', [
            'member' => $member,
            'signedContracts' => $member->documents()->where('type', 'signed_contract')->get(['id', 'title']),
        ]);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $this->authorize('manage-contracts');

        $data = $this->validateContract($request);

        $member->contracts()->create([...$data, 'created_by' => $request->user()->id]);

        return redirect()->route('admin.members.show', $member->id)->with('success', 'Contract created.');
    }

    public function edit(Contract $contract): Response
    {
        $this->authorize('manage-contracts');

        $contract->load('member');

        return Inertia::render('Admin/Contracts/Edit', [
            'contract' => $contract,
            'signedContracts' => $contract->member->documents()->where('type', 'signed_contract')->get(['id', 'title']),
        ]);
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorize('manage-contracts');

        $contract->update($this->validateContract($request));

        return redirect()->route('admin.members.show', $contract->member_id)->with('success', 'Contract updated.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $this->authorize('manage-contracts');

        $memberId = $contract->member_id;
        $contract->delete();

        return redirect()->route('admin.members.show', $memberId)->with('success', 'Contract deleted.');
    }

    private function validateContract(Request $request): array
    {
        return $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'exclusivity_terms' => ['nullable', 'string'],
            'status' => ['required', 'in:active,expired,terminated'],
            'document_id' => ['nullable', 'exists:documents,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
