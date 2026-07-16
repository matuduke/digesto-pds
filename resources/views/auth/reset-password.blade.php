@extends('layouts.app')

@section('title', 'Restablecer contraseña')

@section('content')
  <header class="page-header">
    <h1>Restablecer contraseña</h1>
    <p>Elegí una nueva contraseña para continuar.</p>
  </header>

  <div class="card" style="max-width: 560px;">
    @if ($errors->any())
      <div class="alert alert-warning" style="margin-bottom: 1rem;">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" novalidate>
      @csrf
      <input type="hidden" name="token" value="{{ $token }}" />

      <div class="form-group" style="margin-bottom: 1rem;">
        <label for="email">Email <span class="required">*</span></label>
        <input type="email" id="email" name="email" required value="{{ old('email', $email) }}" autocomplete="email" autofocus />
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label for="password">Nueva contraseña <span class="required">*</span></label>
        <input type="password" id="password" name="password" required autocomplete="new-password" />
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label for="password_confirmation">Confirmar contraseña <span class="required">*</span></label>
        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" />
      </div>

      <div class="form-actions" style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
      </div>
    </form>
  </div>
@endsection
