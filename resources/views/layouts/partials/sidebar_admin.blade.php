<aside class="sidebar" id="sidebar">
  <img src="/images/logo_consorcio.png" id="logo" alt="Logo Consorcio" style="padding: 1rem 0.75rem;"/>
  <div class="sidebar-header">
    <span class="sidebar-brand">Menú</span>
    <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>
  <ul class="sidebar-nav">
    @php
        $user = Auth::user();
    @endphp
    @if($user && ($user->isCreator() || $user->isApprover()))
    <li>
      <a href="{{ route('admin.documents.index') }}" class="@if(request()->routeIs('admin.documents.*')) active @endif" data-label="Normativas">
        <i class="bi bi-file-earmark-text"></i>
        <span class="nav-label">Normativas</span>
      </a>
    </li>
    @endif
    @if($user && $user->isAdmin())
    <li>
      <a href="{{ route('admin.users.index') }}" class="@if(request()->routeIs('admin.users.*')) active @endif" data-label="Usuarios">
        <i class="bi bi-person-fill"></i>
        <span class="nav-label">Usuarios</span>
      </a>
    </li>
    <li>
      <a href="{{ route('admin.groups.index') }}" class="@if(request()->routeIs('admin.groups.*')) active @endif" data-label="Grupos">
        <i class="bi bi-people-fill"></i>
        <span class="nav-label">Grupos</span>
      </a>
    </li>
    <li>
      <a href="{{ route('admin.document-types.index') }}" class="@if(request()->routeIs('admin.document-types.*')) active @endif" data-label="Tipos de Normas">
        <i class="bi bi-file-earmark-font-fill"></i>
        <span class="nav-label">Tipos de Normas</span>
      </a>
    </li>
    <li class="nav-divider"></li>
    <li>
      <a href="#" data-label="Configuración">
        <i class="bi bi-gear"></i>
        <span class="nav-label">Configuración</span>
      </a>
    </li>
    @endif
    <li>
      <a href="{{ route('documents.index') }}" data-label="Normativas">
        <i class="bi bi-arrow-90deg-left"></i>
        <span class="nav-label">Ver normativas</span>
      </a>
    </li>
    <li class="nav-divider"></li>
    @if(\Illuminate\Support\Facades\Auth::check())
    <li>
      <a href="{{ route('logout') }}" data-label="Salir">
        <i class="bi bi-box-arrow-right"></i>
        <span class="nav-label">Salir</span>
      </a>
    </li>
    @else
    <li>
      <a href="{{ route('login') }}" data-label="Ingresar">
        <i class="bi bi-box-arrow-in-right"></i>
        <span class="nav-label">Ingresar</span>
      </a>
    </li>
    @endif
  </ul>
</aside>
