<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $documentTypes = DocumentType::query()
            ->latest(DocumentType::ID)
            ->get();

        return response()->json($documentTypes);
    }

    public function show(DocumentType $documentType): JsonResponse
    {
        $documentType->load('documents');

        return response()->json($documentType);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $documentType = new DocumentType();
        $documentType->name = $validated['name'];
        $documentType->description = $validated['description'];
        $documentType->save();

        return response()->json($documentType, 201);
    }

    public function update(Request $request, DocumentType $documentType): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        if (array_key_exists('name', $validated)) {
            $documentType->name = $validated['name'];
        }

        if (array_key_exists('description', $validated)) {
            $documentType->description = $validated['description'];
        }

        $documentType->save();

        return response()->json($documentType);
    }

    public function destroy(DocumentType $documentType): JsonResponse
    {
        $documentType->delete();

        return response()->json(status: 204);
    }
}
