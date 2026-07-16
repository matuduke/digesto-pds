<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDocumentTypeController extends Controller
{
    public function index(): View
    {
        $documentTypes = DocumentType::query()
            ->orderBy(DocumentType::DESCRIPTION)
            ->paginate(20);

        return view('admin.document-types.index', [
            'documentTypes' => $documentTypes,
        ]);
    }

    public function new(): View
    {
        $documentType = new DocumentType;

        return $this->editOrNew($documentType);
    }

    public function edit(DocumentType $documentType): View
    {
        return $this->editOrNew($documentType);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $documentType = $request->input('id')
            ? DocumentType::findOrFail($request->input('id'))
            : new DocumentType;

        $documentType->fill($validated);
        $documentType->save();

        return redirect()->route('admin.document-types.index');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $documentType->delete();

        return redirect()->route('admin.document-types.index');
    }

    private function editOrNew(DocumentType $documentType): View
    {
        return view('admin.document-types.form', [
            'documentType' => $documentType,
        ]);
    }
}
