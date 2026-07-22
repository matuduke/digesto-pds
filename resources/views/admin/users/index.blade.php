@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
  <header class="page-header">
    <h1>Usuarios</h1>
    <p>Gestión de usuarios del sistema</p>
  </header>

  <div class="card">
    <div class="listing-toolbar" style="gap: 1rem; margin-bottom: 1.5rem;">
      <a href="{{ route('admin.users.new') }}" class="btn btn-primary" style="float: right;">
        <i class="bi bi-person-plus"></i> Nuevo usuario
      </a>
    </div>

    <table class="data-table" id="data-table">
      <thead>
        <tr>
          <th><a href="#">Nombre</a></th>
          <th><a href="#">Email</a></th>
          <th style="text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td style="text-align: center;">
              <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn" style="color: #57cc99; text-decoration: none; cursor: pointer; margin: 0 0.5rem;" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display: inline;" onsubmit="return confirm('¿Está seguro que desea eliminar este usuario?')">
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
              No hay usuarios registrados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @if ($users->hasPages())
      <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $users->links() }}
      </div>
    @endif
  </div>
@endsection
