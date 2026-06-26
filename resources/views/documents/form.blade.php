@extends('layouts.app')

@section('title', $document->id ? 'Editar Norma' : 'Nueva Norma')

@section('content')
  <header class="page-header">
    <h1>{{ $document->id ? 'Editar' : 'Nueva' }} Norma{{ $document->id ? ': ' . $document->number : '' }}</h1>
    <p>{{ $document->id ? 'Actualizar los datos de la norma' : 'Crear una nueva norma' }}</p>
  </header>


    @if ($errors->any())
        <div class="alert alert-warning">
            <p><strong>No se pudo cargar la norma</strong></p>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

  <div class="card">
    <p class="card-section-title">Datos de la Norma</p>

    <form method="POST" action="{{ route('documents.save') }}" novalidate autocomplete="off">
        <input type="hidden" name="id" value="{{ $document->id }}" />
      <div class="form-grid">

        <div class="form-group" style="float:left;">
          <label for="document_type_id">Tipo de Norma <span class="required">*</span></label>
          <select id="document_type_id" name="document_type_id" required>
            <option value="" disabled selected>- Seleccione el tipo de norma -</option>
            @foreach ($documentTypes as $id => $description)
                <option value="{{ $id }}" @selected($document->document_type_id == $id)>
                    {{ $description }}
                </option>
            @endforeach
          </select>
        </div>

        <div class="form-group" style="float:right; margin-right: 30px;">
          <label for="number">Número <span class="required">*</span></label>
          <input type="text" id="number" name="number" placeholder="Ej: 12/2025" maxlength="50" required value="{{ $document->number }}"/>
        </div>

        <div class="form-group" style="float:left;">
          <label for="status">Estado <span class="required">*</span></label>
          <select id="status" name="status" required>
            @foreach ($statuses as $id => $description)
                <option value="{{ $id }}" @selected($document->status == $id)>
                    {{ $description }}
                </option>
            @endforeach
          </select>
        </div>


        <div class="form-group full">
          <label>Contenido <span class="required">*</span></label>
          <div class="rich-editor-wrapper">
            <div class="toolbar" id="rich-toolbar">
              <button type="button" class="toolbar-btn" data-cmd="bold" title="Negrita (Ctrl+B)">
                <i class="bi bi-type-bold"></i>
              </button>
              <button type="button" class="toolbar-btn" data-cmd="underline" title="Subrayado (Ctrl+U)">
                <i class="bi bi-type-underline"></i>
              </button>
              <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList" title="Lista">
                <i class="bi bi-list-ul"></i>
              </button>
            </div>
            <div id="rich-editor" class="rich-editor" contenteditable="true" data-placeholder="" aria-label="Contenido">
{!! $document->content !!}
            </div>
          <input type="hidden" id="content" name="content" />
        </div>

        <div class="form-group full">
          <label>Personas autorizadas para el acceso</label>
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
          <!-- Hidden input that stores the actual value -->
          <input type="hidden" name="authorized_user_ids" id="authorized_user_ids" />

        </div>

        <div class="form-group full">
          <label>Grupos autorizados para el acceso</label>
          <div class="tag-input-wrapper" id="group-wrapper">
            <input
              type="text"
              class="tag-input"
              id="group_input"
              placeholder="Escriba el nombre de un grupo y presione Enter"
              autocomplete="off"
            />
            <div class="tag-container"></div>
            <div class="search-results"></div>
          </div>
          <!-- Hidden input that stores the actual value -->
          <input type="hidden" name="authorized_group_ids" id="authorized_group_ids" />
        </div>

        <div class="form-group full">
          <label>Adjuntar documento original</label>
          <label class="file-drop-zone" id="file-drop-zone" for="file-input">
            <i class="bi bi-paperclip"></i>
            <p><strong>Hacé click para adjuntar</strong> o arrastrá archivos aquí</p>
            <small>PDF, DOC, DOCX, etc máx. 10 MB c/u</small>
          </label>
          <input type="file" id="file-input" class="file-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" />
          <div class="file-list" id="file-list"></div>
        </div>

      </div>

      <div class="form-actions">
        <button type="button" class="btn btn-secondary">Cancelar</button>
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
    var availableGroups = [
        @foreach ($groups as $group)
{"id": "{{ $group->id }}", "name": "{{ $group->description }}"},
        @endforeach
    ];

    var userSearchState = {
        selectedIndex: -1,
        items: []
    };

    var groupSearchState = {
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

    function updateGroupSearchItemHighlight() {
        groupSearchState.items.forEach((item, idx) => {
            if (idx === groupSearchState.selectedIndex) {
                $(item).addClass('hover');
            } else {
                $(item).removeClass('hover');
            }
        });
    }

    function filterAndDisplayUsers($input, $resultsContainer, availableUsers) {
        const searchTerm = $input.val().toLowerCase().trim();

        if (searchTerm === '') {
            $resultsContainer.empty();
            $resultsContainer.hide();
            userSearchState.selectedIndex = 0;
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
            userSearchState.selectedIndex = 0;
            userSearchState.items = [];
            return;
        }

        const $list = $('<ul class="search-results-list"></ul>');
        userSearchState.items = [];

        filteredUsers.forEach(user => {
            const $item = $('<li class="search-result-item"></li>');
            $item.html(`
                <strong>${escapeHtml(user.name)}</strong><br/>
                <small>${escapeHtml(user.email)}</small>
            `);
            $item.on('click', function () {
                const userIds = $("#authorized_user_ids").val() ? $("#authorized_user_ids").val().split(',') : [];

                if (userIds.includes(user.id)) {
                    $input.val('');
                    $resultsContainer.empty();
                    $resultsContainer.hide();
                    userSearchState.selectedIndex = 0;
                    userSearchState.items = [];
                    return;
                }

                const $tag = $('<span class="tag" data-user-id="' + escapeHtml(user.id) + '">' +
                    escapeHtml(user.name) +
                    '<button type="button" class="tag-remove" aria-label="Quitar ' +
                    escapeHtml(user.name) +
                    '">&times;</button>' +
                '</span>');

                $tag.find('.tag-remove').on('click', function(e) {
                    e.preventDefault();
                    const removedId = $tag.attr('data-user-id');
                    const currentIds = $("#authorized_user_ids").val() ? $("#authorized_user_ids").val().split(',') : [];
                    const filteredIds = currentIds.filter(id => id !== removedId);
                    $("#authorized_user_ids").val(filteredIds.join(','));
                    $tag.remove();
                });

                userIds.push(user.id);
                $("#authorized_user_ids").val(userIds.join(','));

                $("#user-wrapper div.tag-container").append($tag);
                $input.val('');
                $resultsContainer.empty();
                $resultsContainer.hide();
                userSearchState.selectedIndex = 0;
                userSearchState.items = [];
            });
            $list.append($item);
            userSearchState.items.push($item[0]);
        });

        $resultsContainer.append($list);
        $resultsContainer.show();
        updateSearchItemHighlight();
    }

    function filterAndDisplayGroups($input, $resultsContainer, availableGroups) {
        const searchTerm = $input.val().toLowerCase().trim();

        if (searchTerm === '') {
            $resultsContainer.empty();
            $resultsContainer.hide();
            groupSearchState.selectedIndex = 0;
            groupSearchState.items = [];
            return;
        }

        const filteredGroups = availableGroups.filter(group =>
            group.name.toLowerCase().includes(searchTerm)
        );

        $resultsContainer.empty();
        $resultsContainer.hide();

        if (filteredGroups.length === 0) {
            groupSearchState.selectedIndex = 0;
            groupSearchState.items = [];
            return;
        }

        const $list = $('<ul class="search-results-list"></ul>');
        groupSearchState.items = [];

        filteredGroups.forEach(group => {
            const $item = $('<li class="search-result-item"></li>');
            $item.html(`<strong>${escapeHtml(group.name)}</strong>`);
            $item.on('click', function () {
                const groupIds = $("#authorized_group_ids").val() ? $("#authorized_group_ids").val().split(',') : [];

                if (groupIds.includes(group.id)) {
                    $input.val('');
                    $resultsContainer.empty();
                    $resultsContainer.hide();
                    groupSearchState.selectedIndex = 0;
                    groupSearchState.items = [];
                    return;
                }

                const $tag = $('<span class="tag" data-group-id="' + escapeHtml(group.id) + '">' +
                    escapeHtml(group.name) +
                    '<button type="button" class="tag-remove" aria-label="Quitar ' +
                    escapeHtml(group.name) +
                    '">&times;</button>' +
                '</span>');

                $tag.find('.tag-remove').on('click', function(e) {
                    e.preventDefault();
                    const removedId = $tag.attr('data-group-id');
                    const currentIds = $("#authorized_group_ids").val() ? $("#authorized_group_ids").val().split(',') : [];
                    const filteredIds = currentIds.filter(id => id !== removedId);
                    $("#authorized_group_ids").val(filteredIds.join(','));
                    $tag.remove();
                });

                groupIds.push(group.id);
                $("#authorized_group_ids").val(groupIds.join(','));

                $("#group-wrapper div.tag-container").append($tag);
                $input.val('');
                $resultsContainer.empty();
                $resultsContainer.hide();
                groupSearchState.selectedIndex = 0;
                groupSearchState.items = [];
            });
            $list.append($item);
            groupSearchState.items.push($item[0]);
        });

        $resultsContainer.append($list);
        $resultsContainer.show();
        updateGroupSearchItemHighlight();
    }

    $(document).ready(function() {
        $(".toolbar-btn").on("click", function() {
            var command = $(this).attr("data-cmd");

            if (!command) {
              return;
            }

            document.execCommand(command, false, null);
            $("#rich-editor").trigger('focus');
        });

        $("#rich-editor").on('keyup mouseup input', function () {
            $("#content").val($("#rich-editor").html());
        });

        $("form").on('submit', function() {
            $("#content").val($("#rich-editor").html());
        });

        $("#user_input").on('keydown', function (event) {
            const $resultsContainer = $("#user-search-results");
            const isSearchVisible = $resultsContainer.is(':visible') && userSearchState.items.length > 0;

            if (event.key === 'ArrowDown') {
                if (isSearchVisible) {
                    event.preventDefault();
                    if (userSearchState.selectedIndex + 1 >= userSearchState.items.length) {
                        userSearchState.selectedIndex = 0;
                    } else {
                        userSearchState.selectedIndex = userSearchState.selectedIndex + 1;
                    }
                    updateSearchItemHighlight();
                }
                return;
            }

            if (event.key === 'ArrowUp') {
                if (isSearchVisible) {
                    event.preventDefault();
                    if (userSearchState.selectedIndex - 1 < 0) {
                        userSearchState.selectedIndex = userSearchState.items.length - 1;
                    } else {
                        userSearchState.selectedIndex = userSearchState.selectedIndex - 1;
                    }
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
            filterAndDisplayUsers($(this), $("#user-search-results"), availableUsers);
        });

        $("#user_input").on('blur', function () {
            setTimeout(() => {
                $("#user-search-results").empty();
                $("#user-search-results").hide();
            }, 200);
        });

        $("#user-search-results").hide();

        // Group search functionality
        $("#group_input").on('keydown', function (event) {
            const $resultsContainer = $("#group-wrapper .search-results");
            const isSearchVisible = $resultsContainer.is(':visible') && groupSearchState.items.length > 0;

            if (event.key === 'ArrowDown') {
                if (isSearchVisible) {
                    event.preventDefault();
                    if (groupSearchState.selectedIndex + 1 >= groupSearchState.items.length) {
                        groupSearchState.selectedIndex = 0;
                    } else {
                        groupSearchState.selectedIndex = groupSearchState.selectedIndex + 1;
                    }
                    updateGroupSearchItemHighlight();
                }
                return;
            }

            if (event.key === 'ArrowUp') {
                if (isSearchVisible) {
                    event.preventDefault();
                    if (groupSearchState.selectedIndex - 1 < 0) {
                        groupSearchState.selectedIndex = groupSearchState.items.length - 1;
                    } else {
                        groupSearchState.selectedIndex = groupSearchState.selectedIndex - 1;
                    }
                    updateGroupSearchItemHighlight();
                }
                return;
            }

            if (event.key === 'Enter') {
                if (isSearchVisible && groupSearchState.selectedIndex >= 0) {
                    event.preventDefault();
                    $(groupSearchState.items[groupSearchState.selectedIndex]).click();
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

        $("#group_input").on('keyup', function () {
            filterAndDisplayGroups($(this), $("#group-wrapper .search-results"), availableGroups);
        });

        $("#group_input").on('blur', function () {
            setTimeout(() => {
                $("#group-wrapper .search-results").empty();
                $("#group-wrapper .search-results").hide();
            }, 200);
        });

        $("#group-wrapper .search-results").hide();
    });
</script>
@endsection
