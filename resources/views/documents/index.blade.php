@extends('layouts.app')

@section('title', 'Normativas')

@section('content')
    @php
        $sortColumn = str_replace('-', '', $filters['sort']);
        $sortDirection = $sortColumn == $filters['sort'] ? '' : '-';
        $sortIconClass = $sortDirection == '-' ? 'sort-alpha-down-alt' : 'sort-alpha-up';
    @endphp
  <header class="page-header">
    <h1>{{ $documentType->description }}</h1>
    <p>Listado de {{ $documentType->description }}</p>
  </header>

  <div class="card">
    <div class="listing-toolbar" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem;">

        <form name="search" method="post" action="{{ route('documents.search', ['documentType' => $documentType->name]) }}" style="display: flex; gap: 1rem; align-items: center;" autocomplete="off">
            <input type="hidden" name="sort" id="sort" value="{{ $filters['sort'] }}" />
            <div class="form-group search-bar" style="margin-bottom: 0; max-width: 360px;">
                <div style="display: flex;">
                    <input type="text" name="keyword" id="keyword" value="{{ $filters['keyword'] }}" autocomplete="off" style="flex: 1; min-width: 0; border: 0; outline: none; background: transparent; color: #ffffff; padding: 0.85rem 0.25rem 0.85rem 0;" placeholder="Buscar normativas..." />
                    <button type="button" id="searchButton" aria-label="Buscar normativas" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <table class="data-table" id="data-table">
      <thead>
        <tr>
          <th>Número @if($sortColumn == 'number') <i class="bi bi-{{ $sortIconClass }}"> @endif</th>
          <th>Contenido @if($sortColumn == 'document_type_id') <i class="bi bi-{{ $sortIconClass }}"> @endif</th>
        </tr>
      </thead>
      <tbody id="table-body">
        @foreach ($documents as $document)
            <tr>
                @if ($documentType->name == 'normativa-externa')
                <td><a href="{{ $document->content }}" target="_blank">{{ $document->number }}</a></td>
                <td><a href="{{ $document->content }}" target="_blank">{{ $document->content }}</a></td>
                @else
                <td><a href="{{ route('documents.show', ['document' => $document->id]) }}">{{ $document->number }}</a></td>
                <td>{{ substr(strip_tags($document->content), 0, 100) }}...</td>
                @endif
            </tr>
        @endforeach
      </tbody>
    </table>

    {{ $documents->links('vendor.pagination.bootstrap-4') }}

    @if(count($documents) == 0)
    <div class="empty-state" id="empty-state" style="text-align: center; padding: 3rem 1rem;">
        <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5; display: block; margin-bottom: 1rem;"></i>
        <p style="color: rgba(255, 255, 255, 0.6);">No hay normativas para mostrar</p>
    </div>
    @endif

  </div>
@endsection

@section('extra_scripts')
<script>
    function search() {
        var url = window.location.origin + window.location.pathname;
        var pageNumber = "1";
        var sort = $("#sort").val();
        var keyword = $("#keyword").val();

        window.location.href = url + createQueryString(pageNumber, sort, keyword);
    }

    function createQueryString(pageNumber, sort, keyword) {
        var queryString = [];
        if (pageNumber != "") {
            queryString.push("page=" + pageNumber);
        }

        if (sort != "") {
            queryString.push("sort=" + sort);
        }

        if (keyword != "") {
            queryString.push("keyword=" + keyword);
        }

        return "?" + queryString.join("&");
    }

    function sort(column) {
        var currentSort = $("#sort").val();
        if (currentSort == column) {
            $("#sort").val("-" + column);
        } else {
            $("#sort").val(column);
        }
    }

    $(document).ready(function() {
        $("#searchButton").click(function() {
            search();
        });

        $("#sortNumber").click(function() {
            sort("number");
            search();
        });

        $("#sortDate").click(function() {
            sort("created_at");
            search();
        });
    });
</script>
@endsection
