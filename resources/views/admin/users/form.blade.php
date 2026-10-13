@extends('layouts.app')

@section('title', $user->id ? 'Editar Usuario' : 'Nuevo Usuario')

@section('content')
  <header class="page-header">
    <h1>{{ $user->id ? 'Editar Usuario' : 'Nuevo Usuario' }}</h1>
    <p>{{ $user->id ? 'Actualizar los datos del usuario' : 'Registrar un nuevo usuario del sistema' }}</p>
  </header>

  @if ($errors->any())
    <div class="alert alert-warning">
      <p><strong>No se pudo guardar el usuario</strong></p>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <p class="card-section-title">Datos del Usuario</p>

    <form method="POST" action="{{ route('admin.users.save') }}" novalidate autocomplete="off">
      @csrf
      <input type="hidden" name="id" value="{{ $user->id }}" />

      <div class="form-grid">
        <div class="form-group">
          <label for="name">Nombre <span class="required">*</span></label>
          <input type="text" id="name" name="name" placeholder="Ej: Juan García" maxlength="255" required value="{{ old('name', $user->name) }}" style="width: 550px;" />
          @error('name')
            <span class="validation-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="email">Email <span class="required">*</span></label>
          <input type="email" id="email" name="email" placeholder="usuario@ejemplo.com" maxlength="255" required value="{{ old('email', $user->email) }}" style="width: 550px;" />
          @error('email')
            <span class="validation-error">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-actions" style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
@endsection
