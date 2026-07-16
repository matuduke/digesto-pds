@extends('layouts.app')

@section('title', $group->id ? 'Editar Grupo' : 'Nuevo Grupo')

@section('content')
  <header class="page-header">
    <h1>{{ $group->id ? 'Editar Grupo' : 'Nuevo Grupo' }}</h1>
    <p>{{ $group->id ? 'Actualizar los datos del grupo' : 'Registrar un nuevo grupo de usuarios' }}</p>
  </header>

  @if ($errors->any())
    <div class="alert alert-warning">
      <p><strong>No se pudo guardar el grupo</strong></p>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <p class="card-section-title">Datos del Grupo</p>

    <form method="POST" action="{{ route('admin.groups.save') }}" novalidate autocomplete="off">
      @csrf
      <input type="hidden" name="id" value="{{ $group->id }}" />

      <div class="form-grid">
        <div class="form-group">
          <label for="description">Descripción <span class="required">*</span></label>
          <input type="text" id="description" name="description" placeholder="Ej: Dirección General" maxlength="255" required value="{{ old('description', $group->description) }}" />
          @error('description')
            <span class="validation-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
        </div>
      </div>

      <div class="form-grid" style="margin-top: 1.25rem;">
        <div class="form-group full">
          <label>Miembros del grupo</label>
          <div class="tag-input-wrapper" id="user-wrapper">
            <input
              type="text"
              class="tag-input"
              id="user_input"
              placeholder="Escriba el nombre de una persona o email y presione Enter"
              autocomplete="chrome-off"
            />
            <div class="tag-container"></div>
            <div class="search-results" id="user-search-results"></div>
          </div>
          <input type="hidden" name="member_ids" id="member_ids" value="{{ $memberIds ?? '' }}" />
        </div>
      </div>

      <div class="form-actions" style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <a href="{{ route('admin.groups.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
@endsection

@section('extra_scripts')
<script>
    var availableUsers = [
        @foreach ($users as $user)
        {"id": "{{ $user->id }}", "name": "{{ $user->name }}", "email": "{{ $user->email }}"},
        @endforeach
    ];

    var userSearchState = {
        selectedIndex: -1,
        items: []
    };

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function updateSearchItemHighlight() {
        userSearchState.items.forEach((item, idx) => {
            if (idx === userSearchState.selectedIndex) {
                $(item).addClass('hover');
            } else {
                $(item).removeClass('hover');
            }
        });
    }

    function filterAndDisplayUsers($input, $resultsContainer) {
        const searchTerm = $input.val().toLowerCase().trim();

        if (searchTerm === '') {
            $resultsContainer.empty();
            $resultsContainer.hide();
            userSearchState.selectedIndex = -1;
            userSearchState.items = [];
            return;
        }

        const filteredUsers = availableUsers.filter(user =>
            user.name.toLowerCase().includes(searchTerm) ||
            user.email.toLowerCase().includes(searchTerm)
        );

        $resultsContainer.empty();
        $resultsContainer.hide();

        if (filteredUsers.length === 0) {
            userSearchState.selectedIndex = -1;
            userSearchState.items = [];
            return;
        }

        const $list = $('<ul class="search-results-list"></ul>');
        userSearchState.items = [];

        filteredUsers.forEach(user => {
            const $item = $('<li class="search-result-item"></li>');
            $item.html(`<strong>${escapeHtml(user.name)}</strong><br/><small>${escapeHtml(user.email)}</small>`);
            $item.on('click', function () {
                const memberIds = $("#member_ids").val() ? $("#member_ids").val().split(',') : [];

                if (memberIds.includes(user.id)) {
                    $input.val('');
                    $resultsContainer.empty();
                    $resultsContainer.hide();
                    userSearchState.selectedIndex = -1;
                    userSearchState.items = [];
                    return;
                }

                const $tag = $('<span class="tag" data-user-id="' + escapeHtml(user.id) + '">' +
                    escapeHtml(user.name) +
                    '<button type="button" class="tag-remove" aria-label="Quitar ' + escapeHtml(user.name) + '">&times;</button>' +
                '</span>');

                $tag.find('.tag-remove').on('click', function (e) {
                    e.preventDefault();
                    const removedId = $tag.attr('data-user-id');
                    const currentIds = $("#member_ids").val() ? $("#member_ids").val().split(',') : [];
                    $("#member_ids").val(currentIds.filter(id => id !== removedId).join(','));
                    $tag.remove();
                });

                memberIds.push(user.id);
                $("#member_ids").val(memberIds.join(','));
                $("#user-wrapper div.tag-container").append($tag);
                $input.val('');
                $resultsContainer.empty();
                $resultsContainer.hide();
                userSearchState.selectedIndex = -1;
                userSearchState.items = [];
            });
            $list.append($item);
            userSearchState.items.push($item[0]);
        });

        $resultsContainer.append($list);
        $resultsContainer.show();
        updateSearchItemHighlight();
    }

    $(document).ready(function () {
        $("#user_input").on('keydown', function (event) {
            const $resultsContainer = $("#user-search-results");
            const isSearchVisible = $resultsContainer.is(':visible') && userSearchState.items.length > 0;

            if (event.key === 'ArrowDown') {
                if (isSearchVisible) {
                    event.preventDefault();
                    userSearchState.selectedIndex = (userSearchState.selectedIndex + 1) % userSearchState.items.length;
                    updateSearchItemHighlight();
                }
                return;
            }

            if (event.key === 'ArrowUp') {
                if (isSearchVisible) {
                    event.preventDefault();
                    userSearchState.selectedIndex = userSearchState.selectedIndex <= 0
                        ? userSearchState.items.length - 1
                        : userSearchState.selectedIndex - 1;
                    updateSearchItemHighlight();
                }
                return;
            }

            if (event.key === 'Enter') {
                if (isSearchVisible && userSearchState.selectedIndex >= 0) {
                    event.preventDefault();
                    $(userSearchState.items[userSearchState.selectedIndex]).click();
                    return;
                }
                event.preventDefault();
                return;
            }

            if (event.key === ',') {
                event.preventDefault();
                return;
            }
        });

        $("#user_input").on('keyup', function () {
            filterAndDisplayUsers($(this), $("#user-search-results"));
        });

        $("#user_input").on('blur', function () {
            setTimeout(() => {
                $("#user-search-results").empty();
                $("#user-search-results").hide();
            }, 200);
        });

        $("#user-search-results").hide();

        // Preload existing members as tags
        var preloadedMemberIds = $("#member_ids").val() ? $("#member_ids").val().split(',') : [];
        preloadedMemberIds.forEach(function (userId) {
            var user = availableUsers.find(function (u) { return u.id === userId; });
            if (!user) { return; }
            var $tag = $('<span class="tag" data-user-id="' + escapeHtml(user.id) + '">' +
                escapeHtml(user.name) +
                '<button type="button" class="tag-remove" aria-label="Quitar ' + escapeHtml(user.name) + '">&times;</button>' +
            '</span>');
            $tag.find('.tag-remove').on('click', function (e) {
                e.preventDefault();
                var removedId = $tag.attr('data-user-id');
                var currentIds = $("#member_ids").val() ? $("#member_ids").val().split(',') : [];
                $("#member_ids").val(currentIds.filter(function (id) { return id !== removedId; }).join(','));
                $tag.remove();
            });
            $("#user-wrapper div.tag-container").append($tag);
        });
    });
</script>
@endsection
