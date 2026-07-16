<aside class="sidebar" id="sidebar">
  <img src="https://puertodocksud.com/wp-content/uploads/2025/01/Consorcio_Logo_BarraSuperior.png" alt="Logo Consorcio" style="width: 200px; padding: 1rem 0.75rem;"/>
  <div class="sidebar-header">
    <span class="sidebar-brand">Menú</span>
    <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>
  <ul class="sidebar-nav">
    <li>
      <a href="{{ route('admin.documents.index') }}" class="@if(request()->routeIs('admin.documents.*')) active @endif" data-label="Normativas">
        <i class="bi bi-file-earmark-text"></i>
        <span class="nav-label">Normativas</span>
      </a>
    </li>
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
  </ul>
</aside>
