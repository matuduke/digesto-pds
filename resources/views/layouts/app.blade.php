<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.partials.head')
</head>
<body>
  @include('layouts.partials.sidebar')

  <div class="main-content">
    @yield('content')
  </div>

  @include('layouts.partials.scripts')
</body>
</html>
