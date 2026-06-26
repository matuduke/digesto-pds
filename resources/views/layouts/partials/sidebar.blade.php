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
      <a href="{{ route('documents.index') }}" class="@if(request()->routeIs('documents.*')) active @endif" data-label="Documentos">
        <i class="bi bi-file-earmark-text"></i>
        <span class="nav-label">Documentos</span>
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
