<!DOCTYPE html>
<html lang="es">
<head>
  @include('layouts.partials.head')
</head>
<body>
  @if(request()->routeIs('admin.*'))
    @include('layouts.partials.sidebar_admin')
  @else
    @include('layouts.partials.sidebar')
  @endif

  <div class="main-content">
    @yield('content')
  </div>

  @include('layouts.partials.scripts')
</body>
</html>
