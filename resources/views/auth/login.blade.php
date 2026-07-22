@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
  <header class="page-header">
    <h1>Iniciar sesión</h1>
    <p>Accedé con tu correo y contraseña.</p>
  </header>

  <div class="card" style="max-width: 560px;">
    @if (session('status'))
      <div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-warning" style="margin-bottom: 1rem;">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" novalidate>
      @csrf

      <div class="form-group" style="margin-bottom: 1rem;">
        <label for="email">Email <span class="required">*</span></label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}" autocomplete="email" autofocus />
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label for="password">Contraseña <span class="required">*</span></label>
        <input type="password" id="password" name="password" required autocomplete="current-password" />
      </div>

      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <label style="display: inline-flex; align-items: center; gap: 0.5rem; text-transform: none; letter-spacing: normal;">
          <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
          Recordarme
        </label>

        <a href="{{ route('password.request') }}" style="color: #57cc99;">¿Olvidaste tu contraseña?</a>
      </div>

      <div class="form-actions" style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn btn-primary">Ingresar</button>
      </div>
    </form>
  </div>
@endsection
