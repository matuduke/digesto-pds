@extends('layouts.app')

@section('title', 'Grupos')

@section('content')
  <header class="page-header">
    <h1>Grupos</h1>
    <p>Gestión de grupos de usuarios del sistema</p>
  </header>

  @if ($errors->any())
    <div class="alert alert-warning">
      <p><strong>Se produjo un error</strong></p>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{!! $error !!}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="listing-toolbar" style="gap: 1rem; margin-bottom: 1.5rem;">
      <a href="{{ route('admin.groups.new') }}" class="btn btn-primary" style="float: right;">
        <i class="bi bi-people"></i> Nuevo grupo
      </a>
    </div>

    <table class="data-table" id="data-table">
      <thead>
        <tr>
          <th><a href="#">Nombre</a></th>
          <th style="text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($groups as $group)
          <tr>
            <td>{{ $group->description ?? '-' }}</td>
            <td style="text-align: center;">
              <a href="{{ route('admin.groups.edit', $group->id) }}" class="action-btn" style="color: #57cc99; text-decoration: none; cursor: pointer; margin: 0 0.5rem;" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="{{ route('admin.groups.destroy', $group->id) }}" style="display: inline;" onsubmit="return confirm('¿Está seguro que desea eliminar este grupo?')">
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
              No hay grupos registrados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    @if ($groups->hasPages())
      <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $groups->links() }}
      </div>
    @endif
  </div>
@endsection
