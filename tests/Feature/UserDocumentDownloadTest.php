<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserDocumentDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_download_document_file_from_local_disk(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $defaultDocumentType = DocumentType::where('name', 'like', '%extern%')->first()->name;
        $documentType = DocumentType::query()->create([
            'name' => $defaultDocumentType,
            'description' => 'Normativas Externas',
        ]);

        $document = Document::query()->create([
            'document_type_id' => $documentType->id,
            'user_id' => $user->id,
            'number' => 'DOC-001',
            'status' => 'publicado',
            'is_public' => true,
            'content' => 'Contenido de prueba',
            'publish_date' => now()->toDateString(),
        ]);

        $file = File::query()->create([
            'document_id' => $document->id,
            'name' => 'archivo-prueba.pdf',
            'path' => 'documents/'.$document->id.'/archivo-prueba.pdf',
            'mime_type' => 'application/pdf',
            'size' => 128,
        ]);

        Storage::disk('local')->put($file->path, 'contenido');

        $response = $this->actingAs($user)->get(route('documents.file', [
            'document' => $document->id,
            'file' => $file->id,
        ]));

        $response->assertOk();
        $response->assertDownload('archivo-prueba.pdf');
    }
}
