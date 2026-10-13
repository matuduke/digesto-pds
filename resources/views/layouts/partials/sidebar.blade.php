@php
    use App\Models\DocumentType;

    $documentTypes = DocumentType::query()
            ->orderBy(DocumentType::DESCRIPTION)
            ->get();
@endphp
<aside class="sidebar" id="sidebar">
  <img src="/images/logo_consorcio.png" id="logo" alt="Logo Consorcio" style="padding: 1rem 0.75rem;"/>
  <div class="sidebar-header">
    <span class="sidebar-brand">Menú</span>
    <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>
  <ul class="sidebar-nav">
    @foreach ($documentTypes as $documentType)
    <li>
      <a href="{{ route('documents.byType', ['documentType' => $documentType->name]) }}" class="{{ request()->routeIs('documents.byType') && request()->route('documentType') === $documentType->name ? 'active' : '' }}" data-label="Normativas">
        <i class="bi bi-file-earmark-text"></i>
        <span class="nav-label">{{ $documentType->description }}</span>
      </a>
    </li>
    @endforeach
    <li class="nav-divider"></li>
    @php
        $user = Auth::user();
    @endphp
    @if($user && ($user->isCreator() || $user->isApprover() || $user->isAdmin()))
    <li>
      <a href="{{ route('admin.documents.index') }}" class="@if(request()->routeIs('admin.documents.*')) active @endif" data-label="Administracion">
        <i class="bi bi-pencil"></i>
        <span class="nav-label">Administración</span>
      </a>
    </li>
    @endif
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
