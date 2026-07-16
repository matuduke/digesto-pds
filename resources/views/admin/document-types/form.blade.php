@extends('layouts.app')

@section('title', $documentType->id ? 'Editar Tipo de Documento' : 'Nuevo Tipo de Documento')

@section('content')
  <header class="page-header">
    <h1>{{ $documentType->id ? 'Editar Tipo de Documento' : 'Nuevo Tipo de Documento' }}</h1>
    <p>{{ $documentType->id ? 'Actualizar los datos del tipo de documento' : 'Registrar un nuevo tipo de documento' }}</p>
  </header>

  @if ($errors->any())
    <div class="alert alert-warning">
      <p><strong>No se pudo guardar el tipo de documento</strong></p>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <p class="card-section-title">Datos del Tipo de Documento</p>

    <form method="POST" action="{{ route('admin.document-types.save') }}" novalidate autocomplete="off">
      @csrf
      <input type="hidden" name="id" value="{{ $documentType->id }}" />

      <div class="form-grid">
        <div class="form-group">
          <label for="name">Nombre <span class="required">*</span></label>
          <input type="text" id="name" name="name" placeholder="Ej: Decreto" maxlength="255" required value="{{ old('name', $documentType->name) }}" />
          @error('name')
            <span class="validation-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description">Descripción <span class="required">*</span></label>
          <input type="text" id="description" name="description" placeholder="Ej: Documento de tipo decreto municipal" maxlength="255" required value="{{ old('description', $documentType->description) }}" />
          @error('description')
            <span class="validation-error">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions" style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <a href="{{ route('admin.document-types.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
@endsection
