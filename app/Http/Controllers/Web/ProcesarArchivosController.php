<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProcesarArchivosController extends Controller
{
    private const CARGA_DIRECTORY = 'carga';

    private const RESOLUCIONES_PRESIDENCIA_TYPE = 'resoluciones-presidencia';

    private const ACTAS_DIRECTORIO_TYPE = 'actas-directorio';

    public function procesarArchivos(): void
    {
        $this->procesarResolucionesPresidencia();
        $this->procesarActasDirectorio();
    }

    private function procesarResolucionesPresidencia(): void
    {
        $baseDirectory = self::CARGA_DIRECTORY.'/RESOLUCIONES PRESIDENCIA';

        foreach (Storage::disk('local')->directories($baseDirectory) as $yearDirectory) {
            if (! preg_match('/^RESOLUCIONES\s+(\d{4})$/i', basename($yearDirectory), $yearMatches)) {
                continue;
            }

            $year = (int) $yearMatches[1];

            foreach (Storage::disk('local')->directories($yearDirectory) as $numberDirectory) {
                if (! preg_match('/^RESO\s+N\s+(\d+)$/i', basename($numberDirectory), $numberMatches)) {
                    continue;
                }

                $documentNumber = $numberMatches[1];

                $documents = Document::whereHas('documentType', function ($query) {
                    $query->where('name', self::RESOLUCIONES_PRESIDENCIA_TYPE);
                })
                    ->whereYear('publish_date', $year)
                    ->where('number', $documentNumber)
                    ->get();

                foreach ($documents as $document) {
                    $this->attachFilesInDirectory($document, $numberDirectory);
                }
            }
        }
    }

    private function procesarActasDirectorio(): void
    {
        $baseDirectory = self::CARGA_DIRECTORY.'/ACTAS DE DIRECTORIO/ACTAS PDF Y WORD';

        foreach (Storage::disk('local')->directories($baseDirectory) as $actaDirectory) {
            if (! preg_match('/^ACTA\s+(\d+)$/i', basename($actaDirectory), $matches)) {
                continue;
            }

            $documentNumber = $matches[1];

            $documents = Document::whereHas('documentType', function ($query) {
                $query->where('name', self::ACTAS_DIRECTORIO_TYPE);
            })->where('number', $documentNumber)->get();

            foreach ($documents as $document) {
                $this->attachFilesInDirectory($document, $actaDirectory);
            }
        }
    }

    private function attachFilesInDirectory(Document $document, string $directory): void
    {
        foreach (Storage::disk('local')->files($directory) as $filePath) {
            if (strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) !== 'pdf') {
                continue;
            }

            echo 'Procesando norma: '.$document->number.' - archivo: '.$filePath.'<br>'.PHP_EOL;

            $uploadedFile = new UploadedFile(
                Storage::disk('local')->path($filePath),
                basename($filePath),
                Storage::disk('local')->mimeType($filePath),
                null,
                true
            );

            $this->saveAttachedFile($document, $uploadedFile);
        }
    }

    private function saveAttachedFile(Document $document, $file): void
    {
        if (! $file || ! is_object($file) || ! method_exists($file, 'getClientOriginalName')) {
            return;
        }

        $originalName = $file->getClientOriginalName();
        $normalizedPath = $this->normalizeFileName($originalName);
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();

        // Store file in local filesystem under documents/{document_id}/
        $storagePath = "documents/{$document->id}/{$normalizedPath}";
        Storage::disk('local')->put($storagePath, file_get_contents($file));

        // Create file record in database
        File::create([
            'document_id' => $document->id,
            'name' => $originalName,
            'path' => $storagePath,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);
    }

    /**
     * Normalize file name by removing diacritics, replacing spaces with underscores, and converting to lowercase
     */
    private function normalizeFileName(string $fileName): string
    {
        // Separate name and extension
        $pathInfo = pathinfo($fileName);
        $name = $pathInfo['filename'] ?? '';
        $extension = isset($pathInfo['extension']) ? '.'.$pathInfo['extension'] : '';

        // Normalize: remove diacritics and convert special characters
        $normalized = $this->removeDiacritics($name);

        // Replace spaces with underscores
        $normalized = str_replace(' ', '_', $normalized);

        // Convert to lowercase
        $normalized = strtolower($normalized);

        return $normalized.$extension;
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
