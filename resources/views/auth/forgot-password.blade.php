@extends('layouts.app')

@section('title', 'Reestablecer contraseña')

@section('content')
  <header class="page-header">
    <h1>Reestablecer contraseña</h1>
    <p>Te enviaremos un enlace para definir una nueva contraseña.</p>
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

    <form method="POST" action="{{ route('password.email') }}" novalidate>
      @csrf

      <div class="form-group" style="margin-bottom: 1rem;">
        <label for="email">Email <span class="required">*</span></label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}" autocomplete="email" autofocus />
      </div>

      <div class="form-actions" style="display: flex; justify-content: space-between;">
        <a href="{{ route('login') }}" class="btn btn-secondary">Volver</a>
        <button type="submit" class="btn btn-primary">Enviar enlace</button>
      </div>
    </form>
  </div>
@endsection
