@extends('layouts.app')

@section('title', $document->title)

@section('content')
  <header class="page-header">
    <h1>{{ $document->title }} - {{ $document->number ? '(N° ' . $document->number . ')' : '' }}</h1>
  </header>

  <div class="card">
    <!-- Metadata -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
      <div class="field-group">
        <label>Tipo</label>
        <p>{{ $document->documentType?->description ?? '-' }}</p>
      </div>
      <div class="field-group">
        <label>Estado</label>
        <p>
          <span class="pill">
            {{ ucwords($document->status) }}
          </span>
        </p>
      </div>
      <div class="field-group">
        <label>Autor</label>
        <p>{{ $document->user?->name ?? '-' }}</p>
      </div>
      <div class="field-group">
        <label>Publicado</label>
        <p>
          {{ $document->published_at?->format('d/m/Y') ?? 'No publicado' }}
        </p>
      </div>
    </div>

    <!-- Contenido del documento -->
    <div style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: rgba(255, 255, 255, 0.9);">Contenido</h2>
      <div class="content" style="text-align: justify; background: rgba(0, 0, 0, 0.2); border-radius: 8px; padding: 1.5rem; line-height: 1.7; color: rgba(255, 255, 255, 0.85);">
        {!! $document->content !!}
      </div>
    </div>

    <!-- Usuarios Autorizados -->
    @if($document->authorizedUsers->isNotEmpty())
      <div style="margin-bottom: 2rem; padding: 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; color: rgba(255, 255, 255, 0.85);">
          Usuarios Autorizados
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
          @foreach($document->authorizedUsers as $user)
            <span style="background: rgba(87, 204, 153, 0.15); border: 1px solid rgba(87, 204, 153, 0.3); color: #ffffff; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem;">
              {{ $user->name }}
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
            <a href="{{ $file->url ?? '#' }}" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.7rem 0.9rem; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 6px; color: #57cc99; text-decoration: none; transition: background 0.2s;">
              <i class="bi bi-file-earmark"></i>
              <span>{{ $file->name ?? 'Descargar archivo' }}</span>
            </a>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Acciones -->
    <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
      <a href="{{ route('documents.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al listado
      </a>
      <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Editar
      </a>
    </div>
  </div>
@endsection
