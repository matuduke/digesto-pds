<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Sistema de Digesto') | Consorcio de Gestión del Puerto Dock Sud</title>
<link rel="icon" type="image/png" href="{{ asset('logo.png') }}" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Exo:wght@300;400;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
@yield('extra_css')
<style>
  body {
    font-family: 'Exo', sans-serif;
    background-color: #0c507a;
    color: #ffffff;
    min-height: 100vh;
    display: flex;
    overflow: auto;
    padding: 0px;
    margin: 0px;
  }

  .sidebar {
    width: 240px;
    min-height: 100vh;
    background-color: #083e5f;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    transition: width 0.25s ease;
    overflow: hidden;
    position: relative;
    z-index: 10;
    padding: 0px;
    margin: 0px;
  }

  .sidebar.collapsed {
    width: 56px;
  }

  .sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    min-height: 30px;
    padding: 18px 16px 18px 16px;
  }

  .sidebar-brand {
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    white-space: nowrap;
    opacity: 1;
    transition: opacity 0.15s ease;
    box-sizing: border-box;
    padding: 0px;
  }

  .sidebar.collapsed .sidebar-brand {
    opacity: 0;
    pointer-events: none;
    width: 0;
    overflow: hidden;
  }

  .sidebar-toggle {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    flex-shrink: 0;
    transition: background 0.15s, color 0.15s;
  }

  .sidebar-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
  }

  .sidebar-toggle i {
    font-size: 20px;
    display: inline-block;
    transition: transform 0.25s ease;
  }

  .sidebar.collapsed .sidebar-toggle i {
    transform: rotate(180deg);
  }

  .sidebar.collapsed .sidebar-header {
    justify-content: center;
    padding: 1.1rem 0;
  }

  .sidebar-nav {
    flex: 1;
    padding: 0.75rem 0;
    list-style: none;
  }

  .sidebar-nav li {
    position: relative;
  }

  .sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.7rem 1rem;
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
    transition: background 0.15s, color 0.15s;
    border-left: 3px solid transparent;
  }

  .sidebar-nav a:hover,
  .sidebar-nav a.active {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
    border-left-color: #57cc99;
  }

  .sidebar-nav a.active {
    background: rgba(87, 204, 153, 0.12);
  }

  .sidebar-nav a i {
    font-size: 18px;
    width: 18px;
    text-align: center;
    flex-shrink: 0;
    opacity: 0.8;
  }

  .sidebar-nav a:hover i,
  .sidebar-nav a.active i {
    opacity: 1;
  }

  .nav-label {
    opacity: 1;
    transition: opacity 0.15s ease;
  }

  .sidebar.collapsed .nav-label {
    opacity: 0;
    pointer-events: none;
  }

  .sidebar.collapsed .sidebar-nav a::after {
    content: attr(data-label);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #083e5f;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    white-space: nowrap;
    pointer-events: none;
    opacity: 0;
    margin-left: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: opacity 0.15s;
    z-index: 100;
  }

  .sidebar.collapsed .sidebar-nav a:hover::after {
    opacity: 1;
  }

  .sidebar-nav .nav-divider {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin: 0.5rem 1rem;
  }

  .main-content {
    flex: 1;
    overflow-y: auto;
    padding: 2.5rem 1.5rem;
    transition: padding 0.25s;
  }

  .page-header {
    max-width: 1240px;
    margin: 0 auto 2rem;
  }

  .page-header h1 {
    font-size: 1.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 0px;
  }

  .page-header p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.9rem;
    margin-top: 0.4rem;
  }

  .card {
    background-color: #1274a1;
    border-radius: 10px;
    padding: 2rem 2rem 2.5rem;
    max-width: 1240px;
    margin: 0 auto;
    box-sizing: border-box;
  }

  .field-group label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.6);
  }

  .field-group p {
    style="margin-top: 0.35rem;
    color: #ffffff;
    margin: 5.6px 0px 0px 0px;
  }

  .field-group span.pill {
    background: rgba(87, 204, 153, 0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
  }

  .card-section-title {
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.85);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 0.6rem;
    margin-bottom: 1.5rem;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem 1.5rem;
  }

  .form-grid .full {
    grid-column: 1 / -1;
  }

  select.select-field {
    width: 100%;
    border: 1px solid rgba(255, 255, 255, 0.14);
    outline: none;
    background: rgba(7, 31, 48, 0.35); color: #ffffff;
    padding: 0.85rem;
    border-radius: 14px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
  }

  .form-group.search-bar {
    padding-left: 14px;
    align-items: stretch;
    width: 100%;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 14px;
    overflow: hidden;
    background: rgba(7, 31, 48, 0.35);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
  }

  .form-group label {
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.75);
  }

  .form-group label .required {
    color: #57cc99;
    margin-left: 2px;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    font-family: 'Exo', sans-serif;
    font-size: 0.95rem;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 8px;
    padding: 0.7rem 0.9rem;
    transition: border-color 0.2s, background 0.2s;
    outline: none;
    width: 100%;
  }

  .form-group input::placeholder,
  .form-group textarea::placeholder {
    color: rgba(255, 255, 255, 0.35);
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    border-color: #57cc99;
    background: rgba(255, 255, 255, 0.18);
  }

  .form-group select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.6)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.8rem center;
    padding-right: 2.4rem;
    cursor: pointer;
  }

  .form-group select option {
    background: #0c507a;
    color: #ffffff;
  }

  .form-group textarea {
    resize: vertical;
    min-height: 140px;
    line-height: 1.55;
  }

  .form-group span.validation-error {
    color: #ff6b6b;
    font-size: 0.8rem;
  }

  .btn {
    font-family: 'Exo', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 0.7rem 1.4rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    text-decoration: none;
    display: inline-block;
    text-align: center;
  }

  .btn:active {
    transform: scale(0.98);
  }

  .btn-secondary {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.25);
  }

  .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.18);
  }

  .btn-primary {
    background: #57cc99;
    color: #0c507a;
  }

  .btn-primary:hover {
    background: #4db87d;
  }

  div.content ul {
    padding-left: 30px;
  }

  table.data-table {
    width: 100%;
    border-collapse: collapse;
  }

  table.data-table thead {
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
  }

  table.data-table thead th {
    text-align: left;
    padding: 1rem;
  }

  table.data-table thead th a {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
  }

  table.data-table thead th a:hover {
    text-decoration: underline;
  }

  table.data-table tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }

  table.data-table tr td {
    padding: 1rem;
  }

  table.data-table tr td a {
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
  }

  table.data-table tr td a:hover {
    text-decoration: underline;
  }

  table.data-table tr td button.action-btn {
    background: none;
    border: none;
    color: #ff6b6b;
/** 57cc99 **/
    cursor: pointer;
  }

  table.data-table tr td span.status-pill.borrador {
    background: rgba(12, 80, 122, 0.5);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
  }

  table.data-table tr td span.status-pill.publicado {
    background: rgba(87, 204, 153, 0.5);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
  }

  /* Pagination Styles */
  nav ul.pagination {
    list-style: none;
    display: flex;
    gap: 0.5rem;
    padding: 0;
    margin: 20 0 0 0;
    justify-content: center;
    flex-wrap: wrap;
  }

  .pagination .page-item {
    display: flex;
  }

  .pagination .page-item .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.1rem 0.15rem;
    min-width: 2.5rem;
    height: 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.05);
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .pagination .page-item .page-link:hover {
    color: #ffffff;
    background: rgba(87, 204, 153, 0.15);
    border-color: #57cc99;
  }

  .pagination .page-item.active .page-link {
    background: #57cc99;
    border-color: #57cc99;
    color: #0c507a;
    font-weight: 700;
  }

  .pagination .page-item.disabled .page-link {
    color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.1);
    cursor: not-allowed;
  }

  .pagination .page-item.disabled .page-link:hover {
    color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.1);
  }

  nav span.pagination-info {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
  }

  div.alert {
    padding: 1rem;
    max-width: 1240px;
    margin: 0 auto 1rem auto;
    box-sizing: border-box;
  }

  div.alert.alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffc107;
  }

  div.alert.alert-warning p {
    margin: 0;
    color: #856404;
  }

  div.alert.alert-warning ul li {
    margin: 0;
    color: #856404;
  }

  div.alert.alert-warning strong::before {
    content: "⚠️";
  }

  div.alert.alert-info {
    background-color: #d0eaf8;
    border: 1px solid #2196f3;
  }

  div.alert.alert-info p {
    margin: 0;
    color: #0c507a;
  }

  div.alert.alert-info ul li {
    margin: 0;
    color: #0c507a;
  }

  div.alert.alert-info strong::before {
    content: "ℹ️";
  }

  .rich-editor-wrapper {
    display: flex;
    flex-direction: column;
  }

    .toolbar {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        padding: 0.35rem 0.5rem;
        transition: border-color 0.2s;
    }

    .rich-editor-wrapper:focus-within .toolbar {
        border-color: #57cc99;
    }

    .toolbar-btn {
        align-items: center;
        background: none;
        border: 1px solid transparent;
        border-radius: 5px;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        display: flex;
        font-family: 'Exo', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        height: 28px;
        justify-content: center;
        min-width: 28px;
        padding: 0 0.45rem;
        transition: background 0.15s, color 0.15s, border-color 0.15s;
    }

    .toolbar-btn:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    .toolbar-btn.active {
        background: rgba(87, 204, 153, 0.2);
        border-color: rgba(87, 204, 153, 0.4);
        color: #57cc99;
    }

    .toolbar-sep {
        background: rgba(255, 255, 255, 0.15);
        height: 18px;
        margin: 0 0.15rem;
        width: 1px;
    }

    .rich-editor {
        box-sizing: border-box;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-top: none;
        border-radius: 0 0 8px 8px;
        color: #ffffff;
        font-family: 'Exo', sans-serif;
        font-size: 0.95rem;
        line-height: 1.55;
        min-height: 140px;
        max-height: 320px;
        overflow-y: auto;
        outline: none;
        padding: 10px 15px;
        transition: border-color 0.2s, background 0.2s;
        width: 100%;
        text-align: justify;
    }

    .rich-editor:focus {
        border-color: #57cc99;
    }

    .rich-editor[data-placeholder]:empty::before {
        content: attr(data-placeholder);
        color: rgba(255, 255, 255, 0.35);
        pointer-events: none;
    }

    .file-drop-zone {
        align-items: center;
        background: rgba(255, 255, 255, 0.08);
        border: 2px dashed rgba(255, 255, 255, 0.25);
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        justify-content: center;
        padding: 1.4rem 1rem;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
    }

    .file-drop-zone:hover,
    .file-drop-zone.dragover {
        background: rgba(255, 255, 255, 0.13);
        border-color: #57cc99;
    }

    .file-drop-zone i {
        font-size: 28px;
        opacity: 0.55;
    }

    .file-drop-zone p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.88rem;
        margin: 0;
    }

    .file-drop-zone p strong {
        color: #57cc99;
    }

    .file-drop-zone small {
        color: rgba(255, 255, 255, 0.35);
        font-size: 0.75rem;
    }

    .file-input {
        display: none;
    }

    .file-list {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        margin-top: 0.5rem;
    }

    .file-item {
        align-items: center;
        animation: tagIn 0.15s ease;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        display: flex;
        font-size: 0.83rem;
        gap: 0.5rem;
        padding: 0.4rem 0.75rem;
    }

    .file-item i {
        flex-shrink: 0;
        font-size: 14px;
        opacity: 0.6;
    }

    .file-item-name {
        color: rgba(255, 255, 255, 0.85);
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-item-size {
        color: rgba(255, 255, 255, 0.4);
        flex-shrink: 0;
        font-size: 0.75rem;
    }

    .file-remove {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.45);
        cursor: pointer;
        flex-shrink: 0;
        font-size: 1rem;
        line-height: 1;
        padding: 0;
        transition: color 0.15s;
    }

    .file-remove:hover {
        color: #57cc99;
    }

    .tag-input-wrapper {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 8px;
        padding: 0.5rem 0.7rem;
        display: block;
        flex-wrap: wrap;
        gap: 0.4rem;
        cursor: text;
        transition: border-color 0.2s, background 0.2s;
        min-height: 50px;
        position: relative;
    }

    .tag-input-wrapper:focus-within {
        border-color: #57cc99;
        background: rgba(255, 255, 255, 0.18);
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: rgba(12, 80, 122, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 12px 15px;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
        animation: tagIn 0.15s ease;
    }

    .tag-container {
        margin-top: 5px;
    }

    @keyframes tagIn {
      from { opacity: 0; transform: scale(0.85); }
      to   { opacity: 1; transform: scale(1); }
    }

    .tag-remove {
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.6);
      cursor: pointer;
      font-size: 1rem;
      line-height: 1;
      padding: 0;
      transition: color 0.15s;
    }

    .tag-remove:hover {
      color: #57cc99;
    }

    .tag-input {
      box-sizing: border-box;
      background: transparent;
      border: none;
      color: #ffffff;
      font-family: 'Exo', sans-serif;
      font-size: 0.92rem;
      outline: none;
      flex: 1;
      min-width: 160px;
      padding: 0.2rem 0.2rem;
    }

    .tag-input::placeholder {
      color: rgba(255, 255, 255, 0.35);
    }

    .tag-hint {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.4);
      margin-top: 0.3rem;
    }

  .search-results {
    border-radius: 14px;
    position: absolute;
    top: 60px;
    left: 10px;
    border: 1px solid #093957;
    background-color: #0c507a;
    width: 600px;
    overflow: hidden;
    z-index: 1000;
  }

  .search-results ul.search-results-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  .search-results li.search-result-item {
    background-color: #0c507a;
    cursor: pointer;
    padding: 5px 15px 5px 15px;
  }

  .search-results li.search-result-item:first-child {
    padding: 10px 15px 5px 15px;
  }

  .search-results li.search-result-item:hover, .search-results li.search-result-item.hover {
    background-color: #1274a1;
  }


  @media (max-width: 600px) {
    .sidebar {
      width: 100%;
      min-height: auto;
    }

    .sidebar.collapsed {
      width: 100%;
    }

    body {
      flex-direction: column;
    }

    .main-content {
      padding: 0px;
    }
  }
</style>
