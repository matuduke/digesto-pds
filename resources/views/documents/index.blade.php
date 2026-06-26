@extends('layouts.app')

@section('title', 'Normativas')

@section('content')
    @php
        $sortColumn = str_replace('-', '', $filters['sort']);
        $sortDirection = $sortColumn == $filters['sort'] ? '' : '-';
        $sortIconClass = $sortDirection == '-' ? 'sort-alpha-down-alt' : 'sort-alpha-up';
    @endphp
  <header class="page-header">
    <h1>Normativas</h1>
    <p>Gestión de normativas del sistema</p>
  </header>

  <div class="card">
    <div class="listing-toolbar" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem;">

        <form name="search" method="post" action="{{ route('documents.search') }}" style="display: flex; gap: 1rem; align-items: center;" autocomplete="off">
            <input type="hidden" name="sort" id="sort" value="{{ $filters['sort'] }}" />
            <div class="form-group search-bar" style="margin-bottom: 0; max-width: 360px;">
                <div style="display: flex;">
                    <input type="text" name="keyword" id="keyword" value="{{ $filters['keyword'] }}" autocomplete="off" style="flex: 1; min-width: 0; border: 0; outline: none; background: transparent; color: #ffffff; padding: 0.85rem 0.25rem 0.85rem 0;" placeholder="Buscar normativas..." />
                    <button type="button" id="searchButton" aria-label="Buscar normativas" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="form-group" style="min-width: 200px;">
                <select id="document_type_id" name="document_type_id">
                    <option value="" style="color: rgba(255, 255, 255, 0.35);">- Tipo de norma -</option>
                    @foreach ($documentTypes as $id => $description)
                        <option value="{{ $id }}" @selected($filters['document_type_id'] == $id)>
                            {{ $description }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="min-width: 200px;">
            <select id="status" name="status">
                <option value="" style="color: rgba(255, 255, 255, 0.35);">- Estado -</option>
                @foreach ($statuses as $id => $name)
                    <option value="{{ $id }}" @selected($filters['status'] == $id)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            </div>
        </form>
    </div>

    <div class="listing-toolbar" style="gap: 1rem; margin-bottom: 1.5rem;">
      <a href="{{ route('documents.create') }}" class="btn btn-primary" style="float: right;">
        <i class="bi bi-file-earmark-plus"></i> Cargar norma
      </a>
    </div>

    <table class="data-table" id="data-table">
      <thead>
        <tr>
          <th><a href="#" id="sortNumber">Número @if($sortColumn == 'number') <i class="bi bi-{{ $sortIconClass }}"> @endif</a></th>
          <th><a href="#" id="sortTitle">Título @if($sortColumn == 'title') <i class="bi bi-{{ $sortIconClass }}"> @endif</a></th>
          <th><a href="#" id="sortType">Tipo @if($sortColumn == 'document_type_id') <i class="bi bi-{{ $sortIconClass }}"> @endif</a></th>
          <th><a href="#" id="sortStatus">Estado @if($sortColumn == 'status') <i class="bi bi-{{ $sortIconClass }}"> @endif</a></th>
          <th><a href="#" id="sortDate">Fecha @if($sortColumn == 'published_at') <i class="bi bi-{{ $sortIconClass }}"> @endif</a></th>
          <th style="text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody id="table-body">
        @foreach ($documents as $document)
            <tr>
                <td>{{ $document->number }}</td>
                <td>
                    @if(strlen($document->title) > 50)
                    <span title="{{ $document->title }}">{{ substr($document->title, 0, 50) }}...</span>
                    @else
                    {{ $document->title }}
                    @endif
                </td>
                <td>{{ $document->documentType->description }}</td>
                <td>
                    <span class="status-pill {{ $document->status }}">{{ ucwords($document->status) }}</span>
                </td>
                <td>
                    @if ($document->published_at != null)
                        {{ $document->published_at->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center;">
                    <a href="/documents/{{ $document->id }}" class="action-btn" style="color: #57cc99; text-decoration: none; cursor: pointer;" title="Ver">
                    <i class="bi bi-eye"></i>
                    </a>
                    <a href="/documents/{{ $document->id }}/edit" class="action-btn" style="color: #57cc99; text-decoration: none; cursor: pointer; margin: 0 0.5rem;" title="Editar">
                    <i class="bi bi-pencil"></i>
                    </a>
                    <button class="action-btn" style="background: none; border: none; color: #ff6b6b; cursor: pointer;" title="Eliminar">
                    <i class="bi bi-trash"></i>
                    </button>
                </td>
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
        var documentTypeId = $("#document_type_id").val();
        var status = $("#status").val();

        window.location.href = url + createQueryString(pageNumber, sort, keyword, documentTypeId, status);
    }

    function createQueryString(pageNumber, sort, keyword, documentTypeId, status) {
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

        if (documentTypeId != "") {
            queryString.push("document_type_id=" + documentTypeId);
        }

        if (status != "") {
            queryString.push("status=" + status);
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

        $("select#document_type_id").change(function() {
            search();
        });

        $("select#status").change(function() {
            search();
        });

        $("#sortNumber").click(function() {
            sort("number");
            search();
        });

        $("#sortTitle").click(function() {
            sort("title");
            search();
        });

        $("#sortType").click(function() {
            sort("document_type_id");
            search();
        });

        $("#sortStatus").click(function() {
            sort("status");
            search();
        });

        $("#sortDate").click(function() {
            sort("published_at");
            search();
        });
    });
</script>
@endsection
