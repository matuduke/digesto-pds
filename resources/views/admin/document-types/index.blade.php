@extends('layouts.app')

@section('title', 'Tipos de Documento')

@section('content')
  <header class="page-header">
    <h1>Tipos de Documento</h1>
    <p>Gestión de tipos de documento del sistema</p>
  </header>

  <div class="card">
    <div class="listing-toolbar" style="gap: 1rem; margin-bottom: 1.5rem;">
      <a href="{{ route('admin.document-types.new') }}" class="btn btn-primary" style="float: right;">
        <i class="bi bi-file-earmark-plus"></i> Nuevo tipo
      </a>
    </div>

    <table class="data-table" id="data-table">
      <thead>
        <tr>
          <th><a href="#">Nombre</a></th>
          <th><a href="#">Descripción</a></th>
          <th style="text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($documentTypes as $documentType)
          <tr>
            <td>{{ $documentType->name }}</td>
            <td>{{ $documentType->description }}</td>
            <td style="text-align: center;">
              <a href="{{ route('admin.document-types.edit', $documentType->id) }}" class="action-btn" style="color: #57cc99; text-decoration: none; cursor: pointer; margin: 0 0.5rem;" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="{{ route('admin.document-types.destroy', $documentType->id) }}" style="display: inline;" onsubmit="return confirm('¿Está seguro que desea eliminar este tipo de documento?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn" style="background: none; border: none; color: #ff6b6b; cursor: pointer;" title="Eliminar">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" style="text-align: center; color: rgba(255, 255, 255, 0.5); padding: 2rem;">
              No hay tipos de documento registrados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @if ($documentTypes->hasPages())
      <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $documentTypes->links() }}
      </div>
    @endif
  </div>
@endsection
