<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Member;
use App\Services\DocumentStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(private DocumentStorageService $storage)
    {
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $this->authorize('manage-documents');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type'  => ['required', 'in:signed_contract,id_document,guardian_consent,other'],
            'file'  => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ]);

        $path = $this->storage->store($request->file('file'), 'documents/' . $member->id);

        if (!$path) {
            return back()->with('error', 'Failed to upload document.');
        }

        $member->documents()->create([
            'type'        => $data['type'],
            'title'       => $data['title'],
            'file_path'   => $path,
            'mime_type'   => $request->file('file')->getMimeType(),
            'file_size'   => $request->file('file')->getSize(),
            'uploaded_by' => $request->user()->id,
            'notes'       => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Document uploaded.');
    }

    public function download(Document $document): RedirectResponse
    {
        $this->authorize('manage-documents');

        return redirect($this->storage->getTemporaryUrl($document->file_path));
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('manage-documents');

        $this->storage->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }
}
