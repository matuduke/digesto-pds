@extends('layouts.app')

@section('title', $document->number)

@section('content')
  <header class="page-header">
    <h1>{{ $document->documentType->description }} - {{ $document->number ? $document->number : '' }}</h1>
  </header>

  <div class="card">
    <!-- Metadata -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
      <div class="field-group">
        <label>Tipo</label>
        <p>{{ $document->documentType?->description ?? '-' }}</p>
      </div>
      <div class="field-group">
        <label>Cargada por</label>
        <p>{{ $document->user?->name ?? '-' }}</p>
      </div>
      <div class="field-group">
        <label>Fecha de norma</label>
        <p>
          {{ $document->publish_date?->format('d/m/Y') ?? 'No publicado' }}
        </p>
      </div>
      <div class="field-group">
        <label>Cargada el</label>
        <p>
          {{ $document->created_at?->format('d/m/Y') ?? 'No publicado' }}
        </p>
      </div>
    </div>

    <!-- Contenido del documento -->
    <div style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9);">Contenido</h2>
      <div class="content" style="text-align: justify; background: rgba(0, 0, 0, 0.2); border-radius: 8px; padding: 1.5rem; line-height: 1.7; color: rgba(255, 255, 255, 0.85);">
        @if($document->documentType->name == $defaultDocumentType)
        @php
            $content = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", strip_tags($document->content));
            $lines = preg_split('~\R~', $content);
            $firstLine = $lines[0] ?? '';
            $secondLine = $lines[1] ?? '';
            $descripcionNormativaExterna = substr(strip_tags($secondLine), 0, 100);
        @endphp
        <a href="{{ $firstLine }}" target="_blank" style="color: rgba(255, 255, 255, 0.85);">{{ $secondLine }}</a>
        @else
        {!! $document->content !!}
        @endif
      </div>
    </div>

    <!-- Usuarios Autorizados -->
    @if($document->authorizedUsers->isNotEmpty())
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; color: rgba(255, 255, 255, 0.85);">
          Usuarios Autorizados
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
          @foreach($document->authorizedUsers as $authUser)
            <span style="background: rgba(87, 204, 153, 0.15); border: 1px solid rgba(87, 204, 153, 0.3); color: #ffffff; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">
              {{ $authUser->name }}
            </span>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Grupos Autorizados -->
    @if($document->authorizedGroups->isNotEmpty())
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; color: rgba(255, 255, 255, 0.85);">
          Grupos Autorizados
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
          @foreach($document->authorizedGroups as $group)
            <span style="background: rgba(87, 204, 153, 0.15); border: 1px solid rgba(87, 204, 153, 0.3); color: #ffffff; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">
              {{ $group->description }}
            </span>
          @endforeach
        </div>
      </div>
    @endif

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
            <a href="{{ route('documents.file', ['document' => $document->id, 'file' => $file->id]) }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.7rem 0.9rem; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 6px; color: #57cc99; text-decoration: none; transition: background 0.2s;">
              <img src="/images/{{ $fileIcon }}" alt="File type icon" style="height: 80px;" />
              <span style="font-weight: bold;">{{ $file->name ?? 'Descargar archivo' }}</span>
            </a>
          @endforeach
        </div>
      </div>
    @endif

    <div class="form-group full">
        <p class="card-section-title">Historial de cambios</p>
        <div style="overflow-x: auto; overflow-y: auto; height: 300px;">
            <table class="data-table" id="document-logs">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Cambios</th>
                        <th scope="col">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($document->logs as $log)
                        <tr>
                            <td>{{ $log->user?->name ?? '-' }}</td>
                            <td style="white-space: pre-wrap; overflow-wrap: anywhere;">{{ $log->getParsedChanges() }}</td>
                            <td>{{ $log->created_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No hay cambios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Acciones -->
    <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
      <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al listado
      </a>
      @if($user->isCreator())
      <a href="{{ route('admin.documents.edit', $document->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Editar
      </a>
      @endif
    </div>
  </div>
@endsection
