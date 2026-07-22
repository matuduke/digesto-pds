<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\File;
use App\Models\User;
use Tests\TestCase;

class AdminDocumentControllerTest extends TestCase
{
    /**
     * Test file upload with normalized file name
     */
    public function test_update_document_with_file_attachment(): void
    {
        $user = User::factory()->create();
        $documentType = DocumentType::factory()->create();

        // Create a fake file
        $file = $this->makeUploadedFile(
            'Test Documento - Acción.pdf',
            'application/pdf',
            'PDF content here'
        );

        $response = $this->post(route('admin.documents.update'), [
            'id' => null,
            'document_type_id' => $documentType->id,
            'status' => 'borrador',
            'number' => null,
            'is_public' => false,
            'content' => 'Test content',
            'files' => [$file],
        ]);

        $response->assertOk();

        // Verify file was created in database
        $fileRecord = File::where('name', 'Test Documento - Acción.pdf')->first();

        $this->assertNotNull($fileRecord);
        $this->assertStringContainsString('test_documento_-_accion.pdf', $fileRecord->path);
    }

    /**
     * Test file name normalization with various special characters
     */
    public function test_file_name_normalization(): void
    {
        // Test cases: [original_name => expected_normalized_name]
        $testCases = [
            'Documento Áéíóú.pdf' => 'documento_aeiou.pdf',
            'File with Ñ in name.txt' => 'file_with_n_in_name.txt',
            'MiXeD CaSe FiLeNaMe.doc' => 'mixed_case_filename.doc',
            'Multiple   Spaces.pdf' => 'multiple___spaces.pdf',
            'Acción y Reacción.xlsx' => 'accion_y_reaccion.xlsx',
            'file.with.dots.txt' => 'file.with.dots.txt',
        ];

        $controller = $this->app->make(\App\Http\Controllers\Web\AdminDocumentController::class);

        foreach ($testCases as $original => $expected) {
            $normalized = \PHPUnit\Framework\Assert::readAttribute(
                $controller,
                'normalizeFileName'
            );
            $result = call_user_func($normalized->getClosure($controller), $original);
            $this->assertEquals($expected, $result, "Failed to normalize '{$original}'");
        }
    }

    /**
     * Create an uploaded file for testing
     */
    private function makeUploadedFile(string $name, string $mimeType, string $content): \Illuminate\Http\UploadedFile
    {
        $path = sys_get_temp_dir().'/'.uniqid();
        file_put_contents($path, $content);

        return new \Illuminate\Http\UploadedFile(
            $path,
            $name,
            $mimeType,
            null,
            true
        );
    }
}
