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
            'description' => ['required', 'string', 'max:255'],
        ]);

        $documentType = $request->input('id')
            ? DocumentType::findOrFail($request->input('id'))
            : new DocumentType;

        $documentType->fill($validated);
        $documentType->name = $this->normalizeName($request['description']);
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

    /**
     * Normalize the name by removing diacritics, replacing spaces with underscores, and converting to lowercase
     */
    private function normalizeName(string $name): string
    {
        // Normalize: remove diacritics and convert special characters
        $normalized = $this->removeDiacritics($name);

        // Replace spaces with underscores
        $normalized = str_replace(' ', '-', $normalized);

        // Convert to lowercase
        $normalized = strtolower($normalized);

        return $normalized;
    }

    /**
     * Remove diacritical marks from a string
     */
    private function removeDiacritics(string $str): string
    {
        // Use PHP's Normalizer to decompose accented characters
        $str = normalizer_normalize($str, \Normalizer::FORM_D);

        // Remove all combining diacritical marks
        $str = preg_replace('/[\x{0300}-\x{036f}]/u', '', $str);

        // Handle special cases
        $replacements = [
            'ñ' => 'n',
            'Ñ' => 'n',
        ];

        return strtr($str, $replacements);
    }
}
