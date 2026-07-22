@php
    use App\Models\DocumentType;

    $documentTypes = DocumentType::query()
            ->orderBy(DocumentType::DESCRIPTION)
            ->get();
@endphp
<aside class="sidebar" id="sidebar">
  <img src="https://puertodocksud.com/wp-content/uploads/2025/01/Consorcio_Logo_BarraSuperior.png" alt="Logo Consorcio" style="width: 200px; padding: 1rem 0.75rem;"/>
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
  </ul>
</aside>
