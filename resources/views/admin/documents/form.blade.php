@php
function formatFileSize(int $bytes, int $decimals = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $factor = 0;

    while ($bytes >= 1024 && $factor < count($units) - 1) {
        $bytes /= 1024;
        $factor++;
    }

    return round($bytes, $decimals) . ' ' . $units[$factor];
}
@endphp
@extends('layouts.app')

@section('title', $document->id ? 'Editar normativa' : 'Nueva normativa')

@section('extra_css')
<style>
    .boolean-switch-row {
        align-items: center;
        display: flex;
        gap: 0.75rem;
        min-height: 38px;
    }

    .boolean-switch {
        display: inline-block;
        height: 30px;
        position: relative;
        width: 56px;
    }

    .boolean-switch input {
        height: 0;
        opacity: 0;
        width: 0;
    }

    .boolean-switch-slider {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 999px;
        bottom: 0;
        cursor: pointer;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        transition: all 0.2s ease;
    }

    .boolean-switch-slider::before {
        background: #ffffff;
        border-radius: 50%;
        content: '';
        height: 22px;
        left: 3px;
        position: absolute;
        top: 3px;
        transition: transform 0.2s ease;
        width: 22px;
    }

    .boolean-switch input:checked + .boolean-switch-slider {
        background: #57cc99;
        border-color: #57cc99;
    }

    .boolean-switch input:checked + .boolean-switch-slider::before {
        transform: translateX(26px);
    }

    .boolean-switch-label {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.92rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
  <header class="page-header">
    <h1>{{ $document->id ? 'Editar' : 'Nueva' }} Normativa{{ $document->id ? ': ' . $document->number : '' }}</h1>
    <p>{{ $document->id ? 'Actualizar los datos de la normativa' : 'Cargar una nueva normativa' }}</p>
  </header>


    @if ($errors->any())
        <div class="alert alert-warning">
            <p><strong>No se pudo cargar la normativa</strong></p>
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    @if ($message != null && $message != '')
        <div class="alert alert-info">
            <p><strong></strong>{{ $message }}</p>
        </div>
    @endif

  <div class="card">
    <p class="card-section-title">Datos de la normativa</p>

    <form method="POST" action="{{ route('admin.documents.save') }}" novalidate autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="id" value="{{ $document->id }}" />
      <div class="form-grid">

        <div class="form-group" style="float:left;">
          <label for="document_type_id">Tipo de normativa <span class="required">*</span></label>
          <select id="document_type_id" name="document_type_id" required>
            <option value="" disabled selected>- Seleccione el tipo de normativa -</option>
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

        <div class="form-group" style="float:left;">
          <label for="is_public">¿La normativa es de acceso público? <span class="required">*</span></label>
          @php($isPublic = (int) old('is_public', $document->is_public ?? 0) === 1)
          <div class="boolean-switch-row">
            <input type="hidden" name="is_public" value="0" />
            <label class="boolean-switch" for="is_public">
              <input type="checkbox" id="is_public" name="is_public" value="1" @checked($isPublic) />
                <span class="boolean-switch-slider"></span>
            </label>
            <span class="boolean-switch-label" id="is_public_label" aria-live="polite">{{ $isPublic ? 'Sí' : 'No' }}</span>
          </div>
        </div>

        <div class="form-group" style="float:left;">
          <label for="publish_date">Fecha de Publicación</label>
          <input type="date" id="publish_date" name="publish_date" value="{{ $document->publish_date ? $document->publish_date->format('Y-m-d') : '' }}" />
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
          <label>Personas autorizadas para la lectura</label>
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
          <input type="hidden" name="authorized_user_ids" id="authorized_user_ids" value="{{ $authorizedUserIds ?? '' }}" />

        </div>

        <div class="form-group full">
          <label>Grupos autorizados para la lectura</label>
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
          <input type="hidden" name="authorized_group_ids" id="authorized_group_ids" value="{{ $authorizedGroupIds ?? '' }}" />
        </div>

        <div class="form-group full">
          <label>Adjuntar documento original</label>
          <label class="file-drop-zone" id="file-drop-zone" for="file-input">
            <i class="bi bi-paperclip"></i>
            <p><strong>Hacé click para adjuntar</strong> o arrastrá archivos aquí</p>
            <small>PDF, DOC, DOCX, etc máx. 10 MB c/u</small>
          </label>
          <input type="file" id="file-input" name="files[]" class="file-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" />
          <input type="hidden" id="removed-files" name="removed-files" value="" />
          <div class="file-list" id="file-list"></div>
        </div>

      </div>

      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="location.href='/admin/documentos'">Cancelar</button>
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
        var $fileInput = $('#file-input');
        var $fileDropZone = $('#file-drop-zone');
        var $fileList = $('#file-list');
        var $removedFilesInput = $('#removed-files');
        var removedFileIds = []; // Track removed database files

        // Preload selectedFiles with existing files from database
        var selectedFiles = [
            @foreach ($files as $file)
            {name: "{{ $file->name }}", size: {{ $file->size }}, id: {{ $file->id }}},
            @endforeach
        ];
        renderSelectedFiles();

        function updateRemovedFilesInput() {
            $removedFilesInput.val(removedFileIds.join(','));
        }

        function formatFileSize(size) {
            if (size < 1024) {
                return size + ' B';
            }
            if (size < 1024 * 1024) {
                return (size / 1024).toFixed(1) + ' KB';
            }
            return (size / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function updateFileInput() {
            var dataTransfer = new DataTransfer();
            // Only add real File objects (new files), not database file mocks
            selectedFiles.forEach(file => {
                if (file instanceof File) {
                    dataTransfer.items.add(file);
                }
            });
            $fileInput[0].files = dataTransfer.files;
        }

        function renderSelectedFiles() {
            // Clear only new file items, keep existing files (those with data-file-id)
            $fileList.find('.file-item:not([data-file-id])').remove();

            // Render both database files and new File objects
            selectedFiles.forEach((file, index) => {
                // Skip if already rendered (database files with id property)
                if (file.id && $fileList.find('[data-file-id="' + file.id + '"]').length > 0) {
                    return;
                }

                var $item = $('<div class="file-item"></div>');

                // Add data-file-id only for database files
                if (file.id) {
                    $item.attr('data-file-id', file.id);
                }

                var $name = $('<span class="file-item-name"></span>').text(file.name);
                var $size = $('<span class="file-item-size"></span>').text(formatFileSize(file.size));
                var $remove = $('<button type="button" class="file-remove" aria-label="Quitar ' + escapeHtml(file.name) + '">&times;</button>');

                $item.append('<i class="bi bi-file-earmark"></i>');
                $item.append($name);
                $item.append($size);
                $item.append($remove);
                $fileList.append($item);
            });
        }

        function addFilesToSelection(newFiles) {
            selectedFiles = selectedFiles.concat(Array.from(newFiles));
            updateFileInput();
            renderSelectedFiles();
        }

        $fileDropZone.on('dragenter dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $fileDropZone.addClass('dragover');
        });

        $fileDropZone.on('dragleave', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $fileDropZone.removeClass('dragover');
        });

        $fileDropZone.on('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            $fileDropZone.removeClass('dragover');

            var droppedFiles = event.originalEvent.dataTransfer.files;
            if (!droppedFiles || droppedFiles.length === 0) {
                return;
            }

            addFilesToSelection(droppedFiles);
        });

        $fileInput.on('change', function () {
            const newFilesFromInput = Array.from($fileInput[0].files);

            // Only add files that aren't already in selectedFiles to avoid duplicates
            const uniqueNewFiles = newFilesFromInput.filter(newFile =>
                !selectedFiles.some(existingFile =>
                    existingFile.name === newFile.name &&
                    existingFile.size === newFile.size &&
                    existingFile.lastModified === newFile.lastModified
                )
            );

            if (uniqueNewFiles.length > 0) {
                addFilesToSelection(uniqueNewFiles);
            }

            // Clear the input so next file selection is detected as a change
            $fileInput.val('');
        });

        // Handle file removal for both new and existing files
        $fileList.on('click', '.file-remove', function (e) {
            e.preventDefault();
            const $item = $(this).closest('.file-item');
            const fileId = $item.attr('data-file-id');
            const fileName = $item.find('.file-item-name').text();

            if (fileId) {
                // Existing file from database - track for deletion
                if (!removedFileIds.includes(fileId)) {
                    removedFileIds.push(fileId);
                    updateRemovedFilesInput();
                }
                // Remove from selectedFiles array
                selectedFiles = selectedFiles.filter(file => file.id !== parseInt(fileId));
                $item.remove();
            } else {
                // New file - remove from selectedFiles array
                selectedFiles = selectedFiles.filter(file =>
                    !(file instanceof File) || file.name !== fileName
                );
                updateFileInput();
                renderSelectedFiles();
            }
        });

        var $isPublic = $('#is_public');
        var $isPublicLabel = $('#is_public_label');

        function updateIsPublicLabel() {
            $isPublicLabel.text($isPublic.is(':checked') ? 'Sí' : 'No');
        }

        $isPublic.on('change', updateIsPublicLabel);
        updateIsPublicLabel();

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

        // Preload authorized users and groups as tags from hidden input values
        var preloadedUserIds = $("#authorized_user_ids").val() ? $("#authorized_user_ids").val().split(',') : [];
        preloadedUserIds.forEach(function (userId) {
            var user = availableUsers.find(function (u) { return u.id === userId; });
            if (!user) { return; }
            var $tag = $('<span class="tag" data-user-id="' + escapeHtml(user.id) + '">' +
                escapeHtml(user.name) +
                '<button type="button" class="tag-remove" aria-label="Quitar ' + escapeHtml(user.name) + '">&times;</button>' +
            '</span>');
            $tag.find('.tag-remove').on('click', function (e) {
                e.preventDefault();
                var removedId = $tag.attr('data-user-id');
                var currentIds = $("#authorized_user_ids").val() ? $("#authorized_user_ids").val().split(',') : [];
                $("#authorized_user_ids").val(currentIds.filter(function (id) { return id !== removedId; }).join(','));
                $tag.remove();
            });
            $("#user-wrapper div.tag-container").append($tag);
        });

        var preloadedGroupIds = $("#authorized_group_ids").val() ? $("#authorized_group_ids").val().split(',') : [];
        preloadedGroupIds.forEach(function (groupId) {
            var group = availableGroups.find(function (g) { return g.id === groupId; });
            if (!group) { return; }
            var $tag = $('<span class="tag" data-group-id="' + escapeHtml(group.id) + '">' +
                escapeHtml(group.name) +
                '<button type="button" class="tag-remove" aria-label="Quitar ' + escapeHtml(group.name) + '">&times;</button>' +
            '</span>');
            $tag.find('.tag-remove').on('click', function (e) {
                e.preventDefault();
                var removedId = $tag.attr('data-group-id');
                var currentIds = $("#authorized_group_ids").val() ? $("#authorized_group_ids").val().split(',') : [];
                $("#authorized_group_ids").val(currentIds.filter(function (id) { return id !== removedId; }).join(','));
                $tag.remove();
            });
            $("#group-wrapper div.tag-container").append($tag);
        });
    });
</script>
@endsection
