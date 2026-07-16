@extends('layouts.app')

@section('title', $document->number)

@section('content')
  <header class="page-header">
    <h1>{{ $document->documentType->description }} - {{ $document->number ? $document->number : '' }}</h1>
  </header>

  <div class="card">

    <!-- Contenido del documento -->
    <div style="margin-bottom: 2rem;">
      <div class="content" style="text-align: justify; background: rgba(0, 0, 0, 0.2); border-radius: 8px; padding: 1.5rem; line-height: 1.7; color: rgba(255, 255, 255, 0.85);">
        {!! $document->content !!}
      </div>
    </div>

    <!-- Archivos Adjuntos -->
    @if($document->files->isNotEmpty())
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; color: rgba(255, 255, 255, 0.85);">
          Archivos Adjuntos
        </h3>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
          @foreach($document->files as $file)
            @php
              $mimeType = $file->mime_type ?? '';
              $fileIcon = 'default.png';

              // Determine file type based on MIME type
              if (str_contains($mimeType, 'pdf')) {
                $fileIcon = 'pdf.png';
              } elseif (str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel') || str_contains($mimeType, 'sheet')) {
                $fileIcon = 'xls.png';
              } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'wordprocessingml') || str_contains($mimeType, 'document')) {
                $fileIcon = 'doc.png';
              } elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentationml') || str_contains($mimeType, 'presentation')) {
                $fileIcon = 'pps.png';
              } elseif (str_contains($mimeType, 'image')) {
                $fileIcon = 'img.png';
              }
            @endphp
            <a href="/{{ $file->path ?? '#' }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.7rem 0.9rem; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 6px; color: #57cc99; text-decoration: none; transition: background 0.2s;">
              <img src="/images/{{ $fileIcon }}" alt="File type icon" style="height: 80px;" />
              <span style="font-weight: bold;">{{ $file->name ?? 'Descargar archivo' }}</span>
            </a>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Acciones -->
    <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
      <a href="{{ route('documents.byType', ['documentType' => $document->documentType->name]) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al listado
      </a>
    </div>
  </div>
@endsection
