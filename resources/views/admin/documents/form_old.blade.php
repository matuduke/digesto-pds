@extends('layouts.app')

@section('title', $document->id ? 'Editar Documento' : 'Nuevo Documento')

@section('content')
  <header class="page-header">
    <h1>{{ $document->id ? 'Editar' : 'Nueva' }} Norma</h1>
    <p>{{ $document->id ? 'Actualiza los datos del documento' : 'Completá los campos para registrar un nuevo documento' }}</p>
  </header>

  <div class="card">
    <p class="card-section-title">Datos del Documento</p>

    <!-- NOTE: Form routes (documents.store, documents.update) are not implemented in web.php. Use API endpoints instead. -->
    <div class="alert alert-warning" style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 1rem; border-radius: 0.25rem; margin-bottom: 1rem;">
      <p style="margin: 0; color: #856404;"><strong>⚠️ Funcionalidad deshabilitada:</strong> El formulario de documentos aún no está implementado en esta interfaz web. Las rutas documents.store y documents.update no están definidas.</p>
    </div>
    <form method="POST" action="#" disabled novalidate style="opacity: 0.5; pointer-events: none;">
      @csrf
      @if($document->id)
        @method('PUT')
      @endif

      <div class="form-grid">
        <!-- Tipo de Documento -->
        <div class="form-group">
          <label>Tipo de Documento <span class="required">*</span></label>
          <select name="document_type_id" required>
            <option value="">Seleccionar tipo...</option>
            @forelse($documentTypes as $type)
              <option value="{{ $type->id }}" @selected(($document->document_type_id ?? old('document_type_id')) == $type->id)>
                {{ $type->name }}
              </option>
            @empty
              <option disabled>No hay tipos disponibles</option>
            @endforelse
          </select>
          @error('document_type_id')
            <span style="color: #ff6b6b; font-size: 0.8rem;">{{ $message }}</span>
          @enderror
        </div>

        <!-- Usuario Propietario -->
        <div class="form-group">
          <label>Usuario Propietario <span class="required">*</span></label>
          <select name="user_id" required>
            <option value="">Seleccionar usuario...</option>
            @forelse($users as $user)
              <option value="{{ $user->id }}" @selected(($document->user_id ?? old('user_id')) == $user->id)>
                {{ $user->name }}
              </option>
            @empty
              <option disabled>No hay usuarios disponibles</option>
            @endforelse
          </select>
          @error('user_id')
            <span style="color: #ff6b6b; font-size: 0.8rem;">{{ $message }}</span>
          @enderror
        </div>

        <!-- Título -->
        <div class="form-group">
          <label>Título <span class="required">*</span></label>
          <input type="text" name="title" value="{{ $document->title ?? old('title') }}" placeholder="Ingresá el título" required />
          @error('title')
            <span style="color: #ff6b6b; font-size: 0.8rem;">{{ $message }}</span>
          @enderror
        </div>

        <!-- Número -->
        <div class="form-group">
          <label>Número</label>
          <input type="text" name="number" value="{{ $document->number ?? old('number') }}" placeholder="Ej: DOC-2025-001" />
          @error('number')
            <span style="color: #ff6b6b; font-size: 0.8rem;">{{ $message }}</span>
          @enderror
        </div>

        <!-- Estado -->
        <div class="form-group">
          <label>Estado <span class="required">*</span></label>
          <select name="status" required>
            <option value="">Seleccionar estado...</option>
            <option value="borrador" @selected(($document->status ?? old('status')) === 'borrador')>Borrador</option>
            <option value="publicado" @selected(($document->status ?? old('status')) === 'publicado')>Publicado</option>
          </select>
          @error('status')
            <span style="color: #ff6b6b; font-size: 0.8rem;">{{ $message }}</span>
          @enderror
        </div>

        <!-- Contenido -->
        <div class="form-group full">
          <label>Contenido <span class="required">*</span></label>
          <div class="rich-editor-wrapper">
            <div class="toolbar">
              <button type="button" class="toolbar-btn" data-command="bold" title="Negrita">
                <i class="bi bi-type-bold"></i>
              </button>
              <button type="button" class="toolbar-btn" data-command="italic" title="Cursiva">
                <i class="bi bi-type-italic"></i>
              </button>
              <div class="toolbar-sep"></div>
              <button type="button" class="toolbar-btn" data-command="insertUnorderedList" title="Lista">
                <i class="bi bi-list-ul"></i>
              </button>
            </div>
            <div
              class="rich-editor"
              id="content-editor"
              contenteditable="true"
              data-placeholder="Ingresá el contenido del documento..."
              style="min-height: 200px;">{{ $document->content ?? old('content') }}</div>
          </div>
          <input type="hidden" name="content" id="content-input" />
          @error('content')
            <span style="color: #ff6b6b; font-size: 0.8rem;">{{ $message }}</span>
          @enderror
        </div>

        <!-- Grupos Autorizados -->
        <div class="form-group full">
          <label>Grupos Autorizados</label>
          <div class="tag-input-wrapper" id="groups-wrapper">
            <input type="text" class="tag-input" placeholder="Ingresá un grupo y presioná Enter..." id="groups-input" />
          </div>
          <input type="hidden" name="authorized_groups" id="groups-value" />
        </div>

        <!-- Usuarios Autorizados -->
        <div class="form-group full">
          <label>Usuarios Autorizados</label>
          <div class="tag-input-wrapper" id="users-wrapper">
            <input type="text" class="tag-input" placeholder="Ingresá un usuario y presioná Enter..." id="users-input" />
          </div>
          <input type="hidden" name="authorized_users" id="users-value" />
        </div>
      </div>

      <div class="form-actions" style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <a href="{{ route('documents.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">{{ $document->id ? 'Actualizar' : 'Guardar' }} Documento</button>
      </div>
    </form>
  </div>
@endsection

@section('extra_scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Rich editor toolbar
    const editor = document.getElementById('content-editor');
    const buttons = document.querySelectorAll('.toolbar-btn');
    const contentInput = document.getElementById('content-input');

    buttons.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const command = btn.dataset.command;
        document.execCommand(command, false, null);
        editor.focus();
      });
    });

    editor?.addEventListener('blur', () => {
      contentInput.value = editor.innerHTML;
    });

    // Tag input for groups and users
    const setupTagInput = (wrapperId, inputId, hiddenId) => {
      const wrapper = document.getElementById(wrapperId);
      const input = document.getElementById(inputId);
      const hidden = document.getElementById(hiddenId);
      let tags = [];

      const renderTags = () => {
        const tagElements = wrapper.querySelectorAll('.tag');
        tagElements.forEach(tag => tag.remove());

        tags.forEach((tag, idx) => {
          const tagEl = document.createElement('div');
          tagEl.className = 'tag';
          tagEl.innerHTML = `
            ${tag}
            <button type="button" class="tag-remove" data-idx="${idx}">
              <i class="bi bi-x"></i>
            </button>
          `;
          wrapper.insertBefore(tagEl, input);
        });

        wrapper.querySelectorAll('.tag-remove').forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.preventDefault();
            const idx = btn.dataset.idx;
            tags.splice(idx, 1);
            renderTags();
            hidden.value = JSON.stringify(tags);
          });
        });

        hidden.value = JSON.stringify(tags);
      };

      input.addEventListener('keydown', (e) => {
        if ((e.key === 'Enter' || e.key === ',') && input.value.trim()) {
          e.preventDefault();
          const value = input.value.trim().replace(/,$/g, '');
          if (!tags.includes(value)) {
            tags.push(value);
            renderTags();
          }
          input.value = '';
        }
      });
    };

    setupTagInput('groups-wrapper', 'groups-input', 'groups-value');
    setupTagInput('users-wrapper', 'users-input', 'users-value');
  });
</script>
@endsection
