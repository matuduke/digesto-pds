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
            <div class="form-group search-bar" style="margin-bottom: 0; min-width: 300px;">
                <div style="display: flex;">
                    <input type="text" name="keyword" id="keyword" value="{{ $filters['keyword'] }}" autocomplete="off" style="flex: 1; min-width: 0; border: 0; outline: none; background: transparent; color: #ffffff; padding: 0.85rem 0.25rem 0.85rem 0;" placeholder="Buscar texto..." />
                    <button type="button" id="searchButton" aria-label="Buscar normativas" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="form-group search-bar" style="margin-bottom: 0; min-width: 300px;">
                <div style="display: flex;">
                    <input type="text" name="number" id="number" value="{{ $filters['number'] }}" autocomplete="off" style="flex: 1; min-width: 0; border: 0; outline: none; background: transparent; color: #ffffff; padding: 0.85rem 0.25rem 0.85rem 0;" placeholder="Buscar número de normativa" />
                    <button type="button" id="searchNumberButton" aria-label="Buscar normativas" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="form-group " style="margin-bottom: 0; min-width: 250px;">
                <div style="display: flex;">
                    <span style="padding-top: 16px;">DESDE:</span>
                    <input type="date" name="date_from" id="dateFrom" value="{{ $filters['date_from'] }}" placeholder="Desde" autocomplete="off" style="margin-left: 10px; padding: 0.85rem 0.25rem 0.85rem 0.65rem; flex: 1;" />
                </div>
            </div>

            <div class="form-group " style="margin-bottom: 0; min-width: 250px;">
                <div style="display: flex;">
                    <span style="padding-top: 16px;">HASTA:</span>
                    <input type="date" name="date_to" id="dateTo" value="{{ $filters['date_to'] }}" placeholder="Hasta" autocomplete="off" style="margin-left: 10px; padding: 0.85rem 0.25rem 0.85rem 0.65rem; flex: 1;" />
                </div>
            </div>

        </form>
    </div>

    <table class="data-table" id="data-table">
      <thead>
        <tr>
          <th>Número @if($sortColumn == 'number') <i class="bi bi-{{ $sortIconClass }}"> @endif</th>
          <th>Fecha de norma @if($sortColumn == 'publish_date') <i class="bi bi-{{ $sortIconClass }}"> @endif</th>
          <th>Contenido @if($sortColumn == 'document_type_id') <i class="bi bi-{{ $sortIconClass }}"> @endif</th>
        </tr>
      </thead>
      <tbody id="table-body">
        @foreach ($documents as $document)
            <tr>
                @if ($documentType->name == $defaultDocumentType)
                @php
                    $content = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", strip_tags($document->content));
                    $lines = preg_split('~\R~', $content);
                    $firstLine = $lines[0] ?? '';
                    $secondLine = $lines[1] ?? '';
                    $descripcionNormativaExterna = substr(strip_tags($secondLine), 0, 100);
                @endphp
                <td><a href="{{ $firstLine }}" target="_blank">{{ $document->number }}</a></td>
                <td><a href="{{ $firstLine }}" target="_blank">{{ $document->publish_date?->format('d/m/Y') }}</a></td>
                <td><a href="{{ $firstLine }}" target="_blank">{{ $descripcionNormativaExterna }}</a></td>
                @else
                <td><a href="{{ route('documents.show', ['document' => $document->id]) }}">{{ $document->number }}</a></td>
                <td><a href="{{ route('documents.show', ['document' => $document->id]) }}" target="_blank">{{ $document->publish_date?->format('d/m/Y') }}</a></td>
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
        var number = $("#number").val();
        var dateFrom = $("#dateFrom").val();
        var dateTo = $("#dateTo").val();

        window.location.href = url + createQueryString(pageNumber, sort, keyword, number, dateFrom, dateTo);
    }

    function createQueryString(pageNumber, sort, keyword, number, dateFrom, dateTo) {
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

        if (number != "") {
            queryString.push("number=" + number);
        }

        if (dateFrom != "") {
            queryString.push("date_from=" + dateFrom);
        }

        if (dateTo != "") {
            queryString.push("date_to=" + dateTo);
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
        $("#keyword, #number, #dateFrom, #dateTo").keypress(function(event) {
            if (event.which === 13) {
                event.preventDefault();
                search();
            }
        });

        $("#searchButton").click(function() {
            search();
        });

        $("#searchNumberButton").click(function() {
            search();
        });

        $('input[type=date]').change(function () {
            search();
        });

        $('input[type=date]').keypress(function (e) {
            $(this).off('change blur');

            $(this).blur(function () {
                search();
            });

            if (e.keyCode === 13) {
                search();
            }
        });

        $("#sortNumber").click(function() {
            sort("number");
            search();
        });

        $("#sortDate").click(function() {
            sort("publish_date");
            search();
        });
    });
</script>
@endsection
