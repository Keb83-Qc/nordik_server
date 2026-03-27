<!DOCTYPE html>
<html lang="fr">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>
    window.ABF_RECORD_ID   = {{ $record->id }};
    window.ABF_SAVE_URL    = '{{ route("abf.editor.save", $record) }}';
    window.ABF_CSRF_TOKEN  = '{{ csrf_token() }}';
    window.ABF_ADVISOR_NAME = '{{ auth()->user()->full_name ?? auth()->user()->name ?? "" }}';
    window.ABF_INITIAL_PAYLOAD = {!! json_encode($record->payload ?? []) !!};
  </script>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ABF — VIP GPI</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:  #0E1030;
      --gold:  #C9A050;
      --gold2: #b08c3a;
      --bg:    #f4f6fb;
      --white: #ffffff;
      --border:#dde2ef;
      --text:  #1a2340;
      --muted: #7a86a3;
      --valid: #22c55e;
      --nav-w: 260px;
      --top-h: 48px;
    }

    body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); }

    /* ── TOP BAR ─────────────────────────────────────────── */
    .topbar {
      position: fixed; top: 0; left: 0; right: 0; height: var(--top-h);
      background: var(--navy); display: flex; align-items: center;
      justify-content: space-between; padding: 0 20px; z-index: 100;
      box-shadow: 0 2px 8px rgba(0,0,0,.3);
    }
    .topbar-logo { display: flex; align-items: center; gap: 10px; }
    .topbar-logo img { height: 28px; }
    .topbar-logo span { color: var(--gold); font-weight: 700; font-size: 15px; letter-spacing: .5px; }
    .topbar-right { display: flex; align-items: center; gap: 16px; color: #aab3cc; font-size: 13px; }
    .topbar-right strong { color: var(--gold); }

    /* ── LAYOUT ──────────────────────────────────────────── */
    .layout { display: flex; padding-top: var(--top-h); min-height: 100vh; }

    /* ── SIDEBAR ─────────────────────────────────────────── */
    .sidebar {
      width: var(--nav-w); flex-shrink: 0; background: var(--white);
      border-right: 1px solid var(--border); position: fixed;
      top: var(--top-h); bottom: 0; overflow-y: auto;
    }
    .sidebar::-webkit-scrollbar { width: 4px; }
    .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

    .nav-group { padding: 4px 0; }
    .nav-group-title {
      display: flex; align-items: center; gap: 8px;
      padding: 10px 16px; font-size: 12px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .8px;
      color: var(--navy); background: #f0f3fa; border-bottom: 1px solid var(--border);
    }
    .nav-group-title svg { width: 14px; height: 14px; fill: var(--gold); flex-shrink: 0; }

    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 16px 9px 28px; cursor: pointer;
      font-size: 13px; color: var(--muted); border: none; background: none;
      width: 100%; text-align: left; transition: all .15s;
      border-left: 3px solid transparent;
    }
    .nav-item:hover { background: #f4f6fb; color: var(--text); }
    .nav-item.active {
      color: var(--navy); font-weight: 600; background: #eef1fc;
      border-left-color: var(--gold);
    }
    .nav-item .dot {
      width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
      border: 2px solid var(--border); background: white;
    }
    .nav-item.active .dot { background: var(--gold); border-color: var(--gold); }
    .nav-item.done .dot  { background: var(--valid); border-color: var(--valid); }
    .nav-item.locked { opacity:.45; cursor:not-allowed; pointer-events:none; }

    /* ── MAIN CONTENT ────────────────────────────────────── */
    .main {
      margin-left: var(--nav-w); flex: 1; padding: 32px 40px 80px;
      max-width: 1100px;
    }

    .page-title { font-size: 22px; font-weight: 700; color: var(--navy); margin-bottom: 4px; }
    .page-subtitle { font-size: 13px; color: var(--muted); margin-bottom: 28px; }

    /* ── SECTION ─────────────────────────────────────────── */
    .card {
      background: var(--white); border: 1px solid var(--border);
      border-radius: 10px; margin-bottom: 20px; overflow: hidden;
    }
    .card-header {
      padding: 12px 20px; background: #f8f9fd;
      border-bottom: 1px solid var(--border);
      font-size: 13px; font-weight: 700; color: var(--navy);
      letter-spacing: .3px;
    }
    .card-body { padding: 20px; }

    /* ── GRID ────────────────────────────────────────────── */
    .row { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; }
    .row:last-child { margin-bottom: 0; }
    .col { flex: 1; min-width: 160px; }
    .col-full { flex: 0 0 100%; }

    /* ── FORM ELEMENTS ───────────────────────────────────── */
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-label { font-size: 12px; font-weight: 600; color: var(--muted); }
    .form-label.required::after { content: ' *'; color: #ef4444; }

    .form-input {
      height: 38px; padding: 0 12px;
      border: 1px solid var(--border); border-radius: 6px;
      font-size: 13px; color: var(--text); background: white;
      transition: border-color .15s, box-shadow .15s; width: 100%;
      outline: none;
    }
    .form-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,160,80,.15); }
    .input-error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,.12) !important; }
    .form-select.input-error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,.12) !important; }
    .radio-error .radio-pill label { color: #ef4444; }

    .form-select {
      height: 38px; padding: 0 32px 0 12px;
      border: 1px solid var(--border); border-radius: 6px;
      font-size: 13px; color: var(--text); background: white;
      appearance: none; width: 100%; cursor: pointer; outline: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%237a86a3' d='M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 10px center;
      transition: border-color .15s;
    }
    .form-select:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,160,80,.15); }

    /* Radio pills */
    .radio-group { display: flex; gap: 8px; flex-wrap: wrap; }
    .radio-pill input[type="radio"] { display: none; }
    .radio-pill label {
      display: flex; align-items: center; justify-content: center;
      padding: 7px 18px; border-radius: 6px; border: 1px solid var(--border);
      font-size: 13px; cursor: pointer; transition: all .15s;
      color: var(--muted); background: white; user-select: none;
    }
    .radio-pill input:checked + label {
      background: var(--navy); color: white; border-color: var(--navy);
      font-weight: 600;
    }
    .radio-pill:hover label { border-color: var(--gold); color: var(--text); }

    /* Date row */
    .date-row { display: flex; gap: 8px; }
    .date-row .form-input { flex: 1; }
    .date-row .form-select { flex: 2; }

    /* ── COLLAPSIBLE ─────────────────────────────────────── */
    .collapse-toggle {
      width: 100%; background: none; border: none;
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 20px; font-size: 13px; font-weight: 600;
      color: var(--muted); cursor: pointer; border-top: 1px solid var(--border);
      transition: color .15s;
    }
    .collapse-toggle:hover { color: var(--text); }
    .collapse-toggle svg { transition: transform .2s; }
    .collapse-toggle.open svg { transform: rotate(180deg); }
    .collapse-body { padding: 20px; border-top: 1px solid var(--border); display: none; }
    .collapse-body.open { display: block; }

    /* ── LIST SECTION ────────────────────────────────────── */
    .list-empty {
      text-align: center; padding: 20px 0; color: var(--muted);
      font-size: 13px; font-style: italic;
    }

    /* ── BOTTOM BAR ──────────────────────────────────────── */
    .bottom-bar {
      position: fixed; bottom: 0; left: var(--nav-w); right: 0;
      background: white; border-top: 1px solid var(--border);
      padding: 12px 40px; display: flex; justify-content: flex-end; gap: 12px;
      z-index: 50;
    }

    /* ── BUTTONS ─────────────────────────────────────────── */
    .btn {
      height: 38px; padding: 0 22px; border-radius: 7px; border: none;
      font-size: 13px; font-weight: 600; cursor: pointer;
      transition: all .15s; display: inline-flex; align-items: center; gap: 7px;
    }
    .btn-primary { background: var(--navy); color: white; }
    .btn-primary:hover { background: #151940; }
    .btn-secondary { background: white; color: var(--navy); border: 1px solid var(--border); }
    .btn-secondary:hover { border-color: var(--navy); }
    .btn-gold { background: var(--gold); color: white; }
    .btn-gold:hover { background: var(--gold2); }
    .btn-sm { height: 32px; padding: 0 14px; font-size: 12px; }
    .btn svg { width: 15px; height: 15px; fill: currentColor; }

    /* ── HIDDEN PAGES ────────────────────────────────────── */
    .page { display: none; }
    .page.active { display: block; }

    /* ── BADGE ───────────────────────────────────────────── */
    .badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
    }
    .badge-navy { background: #eef1fc; color: var(--navy); }

    /* ── RESULTS PAGE ────────────────────────────────────── */
    .score-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 20px; }
    .score-card {
      background: #f8f9fd; border: 1px solid var(--border); border-radius: 8px;
      padding: 16px; text-align: center;
    }
    .score-card .val { font-size: 26px; font-weight: 800; color: var(--navy); }
    .score-card .lbl { font-size: 11px; color: var(--muted); margin-top: 3px; }

    .recommendation-item {
      display: flex; align-items: flex-start; gap: 12px;
      padding: 14px 0; border-bottom: 1px solid var(--border);
    }
    .recommendation-item:last-child { border-bottom: none; }
    .rec-icon {
      width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
    }
    .rec-icon svg { width: 18px; height: 18px; fill: white; }

    /* ── OBJECTIFS ───────────────────────────────────────── */
    .obj-category { background: var(--white); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
    .obj-cat-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 13px 18px; cursor: pointer; user-select: none;
      background: #f8f9fd; border-bottom: 1px solid transparent;
      transition: background .15s;
    }
    .obj-cat-header:hover { background: #eef1fc; }
    .obj-cat-header.open { border-bottom-color: var(--border); background: #f0f3fa; }
    .obj-cat-title { font-size: 13px; font-weight: 700; color: var(--navy); display: flex; align-items: center; gap: 8px; }
    .obj-cat-badge {
      font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px;
      background: var(--gold); color: white; min-width: 20px; text-align: center;
    }
    .obj-cat-badge.zero { background: var(--border); color: var(--muted); }
    .obj-cat-arrow { transition: transform .2s; }
    .obj-cat-header.open .obj-cat-arrow { transform: rotate(180deg); }
    .obj-cat-body { display: none; }
    .obj-cat-body.open { display: block; }

    /* Each objective row */
    .obj-item { border-bottom: 1px solid var(--border); }
    .obj-item:last-child { border-bottom: none; }
    .obj-item-header {
      display: flex; align-items: center; gap: 0;
      padding: 0; cursor: pointer; transition: background .12s;
    }
    .obj-item-header:hover { background: #f8f9fd; }

    /* Checkbox toggle button */
    .obj-check-btn {
      flex-shrink: 0; width: 44px; height: 44px;
      display: flex; align-items: center; justify-content: center;
      border: none; background: none; cursor: pointer; padding: 0;
    }
    .obj-check-btn svg { width: 18px; height: 18px; }
    .obj-check-btn .icon-unchecked { fill: #c5cce3; display: block; }
    .obj-check-btn .icon-checked   { fill: var(--gold); display: none; }
    .obj-check-btn.checked .icon-unchecked { display: none; }
    .obj-check-btn.checked .icon-checked   { display: block; }

    .obj-item-title-wrap { flex: 1; padding: 0 12px 0 4px; min-height: 44px; display: flex; align-items: center; }
    .obj-item-title { font-size: 13px; color: var(--muted); transition: color .12s; }
    .obj-item-title.checked { color: var(--text); font-weight: 600; }

    /* Expand arrow */
    .obj-expand-btn {
      flex-shrink: 0; width: 36px; height: 44px;
      display: flex; align-items: center; justify-content: center;
      border: none; background: none; cursor: pointer; color: var(--muted);
      transition: transform .2s;
    }
    .obj-expand-btn.open { transform: rotate(180deg); }

    /* Expanded detail panel */
    .obj-item-detail {
      display: none; padding: 12px 16px 14px 44px;
      background: #fafbfe; border-top: 1px solid var(--border);
    }
    .obj-item-detail.open { display: block; }
    .obj-item-detail textarea {
      width: 100%; border: 1px solid var(--border); border-radius: 6px;
      padding: 8px 10px; font-size: 12px; resize: vertical; min-height: 60px;
      font-family: inherit; color: var(--text); background: white; outline: none;
    }
    .obj-item-detail textarea:focus { border-color: var(--gold); }
    .obj-item-detail label { font-size: 11px; color: var(--muted); font-weight: 600; margin-bottom: 5px; display: block; }

    /* Add custom button */
    .obj-add-btn {
      width: 100%; padding: 10px 18px; border: none; background: none;
      display: flex; align-items: center; gap: 7px;
      font-size: 12px; font-weight: 600; color: var(--navy);
      cursor: pointer; border-top: 1px solid var(--border); transition: background .12s;
    }
    .obj-add-btn:hover { background: #eef1fc; }
    .obj-add-btn svg { width: 16px; height: 16px; fill: var(--gold); }

    /* ── LEGAL MENU ──────────────────────────────────────── */
    .legal-menu-item {
      display: block; width: 100%; text-align: left; background: none;
      border: none; padding: 9px 16px; font-size: 13px; color: var(--text);
      cursor: pointer; transition: background .12s;
    }
    .legal-menu-item:hover { background: #f4f6fb; color: var(--navy); }

    /* ── MODAL ───────────────────────────────────────────── */
    #modal-enfant { display: none; }
    #modal-enfant.open { display: flex !important; }
    #modal-legal { display: none; }
    #modal-legal.open { display: flex !important; }
    #modal-placement,#modal-bien,#modal-passif { display: none; }
    #modal-placement.open,#modal-bien.open,#modal-passif.open { display: flex !important; }

    /* Calc-type tabs (passif modal) */
    .calc-tabs { display:flex;border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:16px; }
    .calc-tab { flex:1;padding:7px 4px;font-size:12px;font-weight:600;border:none;background:white;
      cursor:pointer;color:var(--muted);border-right:1px solid var(--border);transition:all .15s; }
    .calc-tab:last-child { border-right:none; }
    .calc-tab.active { background:var(--navy);color:white; }

    /* $ / % suffix inputs */
    .input-sfx { position:relative;display:flex;align-items:center; }
    .input-sfx input { padding-right:26px; }
    .input-sfx .sfx { position:absolute;right:10px;font-size:12px;color:var(--muted);pointer-events:none; }

    /* Actifs/passifs right sidebar (inline sticky) */
    #ap-sidebar {
      width:220px; flex-shrink:0;
      position:sticky; top:calc(var(--top-h) + 20px);
      align-self:start;
    }
    .ap-sidebar-section { padding:14px 16px; }
    .ap-sidebar-section + .ap-sidebar-section { border-top:1px solid var(--border); }
    .ap-sb-total { font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px; }
    .ap-sb-total-val { font-size:22px;font-weight:800;color:var(--navy); }
    .ap-sb-row { display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:3px 0; }
    .ap-sb-label { color:var(--muted); }
    .ap-sb-val { font-weight:600;color:var(--navy); }
    .modal-facultatif-title {
      font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;
      letter-spacing:.6px;margin:20px 0 14px;padding-bottom:8px;
      border-bottom:1px solid var(--border);
    }

    /* ── REVENU ET ÉPARGNE ───────────────────────────────── */
    #re-sidebar { width:220px;flex-shrink:0;position:sticky;top:calc(var(--top-h)+20px);align-self:start; }
    #modal-revenu { display:none; }
    #modal-revenu.open { display:flex !important; }
    #modal-epargne { display:none; }
    #modal-epargne.open { display:flex !important; }

    .re-table { width:100%;border-collapse:collapse;font-size:13px; }
    .re-table th {
      padding:10px 14px;text-align:left;font-size:11px;font-weight:700;
      color:var(--muted);text-transform:uppercase;letter-spacing:.5px;
      background:#f8f9fd;border-bottom:1px solid var(--border);
    }
    .re-table td { padding:10px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle; }
    .re-table tbody tr:last-child td { border-bottom:none; }
    .re-table tbody tr:hover:not(.re-detail-row) { background:#fafbfe; }
    .re-detail-row td { padding:0;background:#f8f9fd;border-bottom:1px solid var(--border); }
    .re-detail-inner { padding:10px 14px 12px;display:grid;grid-template-columns:repeat(3,1fr);gap:6px 14px; }
    .re-detail-item { display:flex;flex-direction:column;gap:1px; }
    .re-detail-lbl { font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px; }
    .re-detail-val { font-size:13px;font-weight:600;color:var(--text); }
    .re-detail-val.net { color:#22c55e; }
    .re-detail-val.deduction { color:#ef4444; }
    .re-table .col-action { text-align:right;white-space:nowrap; }
    .re-tab-bar { display:flex;border-bottom:2px solid var(--border);margin-bottom:0; }
    .re-tab { padding:9px 18px;font-size:13px;font-weight:600;color:var(--muted);background:none;border:none;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px; }
    .re-tab.active { color:var(--navy);border-bottom-color:var(--gold); }
    .re-action-btn {
      background:none;border:1px solid var(--border);border-radius:5px;
      padding:4px 7px;cursor:pointer;color:var(--muted);font-size:12px;
      transition:all .12s;margin-left:4px;
    }
    .re-action-btn:hover { background:var(--navy);color:white;border-color:var(--navy); }
    .re-action-btn.del:hover { background:#ef4444;border-color:#ef4444; }

    /* Revenu add-button + dropdown */
    #revenu-add-wrap { position:relative; }
    #revenu-dropdown {
      display:none;position:absolute;right:0;top:calc(100% + 4px);
      background:white;border:1px solid var(--border);border-radius:8px;
      box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:160px;z-index:200;overflow:hidden;
    }
    #revenu-dropdown.open { display:block; }
    #revenu-dropdown .dd-item {
      display:block;width:100%;text-align:left;padding:10px 16px;
      font-size:13px;color:var(--text);background:none;border:none;
      cursor:pointer;transition:background .12s;
    }
    #revenu-dropdown .dd-item:hover { background:#f4f6fb;color:var(--navy); }

    /* Droits de cotisation */
    .dc-input-cell { display:flex;align-items:center;gap:6px; }

    /* Info tooltip */
    .info-tooltip-wrap { position:relative;display:inline-flex;align-items:center;cursor:default; }
    .info-tooltip-icon {
      display:inline-flex;align-items:center;justify-content:center;
      width:18px;height:18px;border-radius:50%;background:var(--navy);
      color:white;font-size:11px;font-weight:700;cursor:pointer;flex-shrink:0;
      user-select:none;
    }
    .info-tooltip-bubble {
      display:none;position:absolute;bottom:calc(100% + 10px);left:50%;
      transform:translateX(-50%);width:300px;background:white;
      border:1px solid var(--border);border-radius:10px;
      box-shadow:0 8px 28px rgba(0,0,0,.15);padding:14px 16px;
      font-size:12px;color:var(--text);line-height:1.5;z-index:500;
      pointer-events:none;
    }
    .info-tooltip-bubble::after {
      content:'';position:absolute;top:100%;left:50%;transform:translateX(-50%);
      border:8px solid transparent;border-top-color:white;
    }
    .info-tooltip-bubble::before {
      content:'';position:absolute;top:100%;left:50%;transform:translateX(-50%);
      border:9px solid transparent;border-top-color:var(--border);margin-top:1px;
      margin-left:-1px;
    }
    .info-tooltip-wrap:hover .info-tooltip-bubble { display:block; }
    .dc-input-cell .form-input { max-width:130px;min-width:90px; }
    .re-sync-btn {
      flex-shrink:0;background:none;border:1px solid var(--border);
      border-radius:6px;padding:5px 8px;cursor:pointer;color:var(--muted);
      font-size:13px;line-height:1;transition:all .15s;
    }
    .re-sync-btn:hover { background:var(--navy);color:white;border-color:var(--navy); }

    /* Flux monétaire donut placeholder */
    .re-donut-wrap { text-align:center;margin:8px 0 4px; }

    /* ── LANDING PAGE ─────────────────────────────────── */
    #page-accueil, #page-valeurs-defaut {
      position:fixed;inset:0;z-index:500;background:white;overflow-y:auto;
    }
    .ia-topbar {
      background:#0E1030;padding:0 32px;height:76px;
      display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:10;
    }
    .ia-logo { height:54px;object-fit:contain; }
    .ia-topbar-right { display:flex;align-items:center;gap:8px; }
    .ia-btn-secondary {
      display:flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid #C9A050;
      background:transparent;color:#C9A050;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;
    }
    .ia-btn-secondary:hover { background:rgba(201,160,80,.12); }
    .ia-btn-primary {
      display:flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid #C9A050;
      background:#C9A050;color:#0E1030;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer;
    }
    .ia-btn-primary:hover { background:#b8903a; }
    .ia-bottombar {
      background:linear-gradient(90deg,#C9A050 0%,#e8c070 50%,#C9A050 100%);
      height:4px;
    }
    .ia-landing-body {
      max-width:900px;margin:48px auto;padding:0 32px;
    }
    .ia-landing-title { font-size:28px;font-weight:800;color:#0E1030;margin-bottom:32px; }
    .ia-search-section {
      background:white;border:1px solid #d0d5e8;border-radius:10px;padding:24px;margin-bottom:16px;
    }
    .ia-two-col { display:grid;grid-template-columns:1fr 1fr;gap:24px; }
    .ia-field-label { font-size:12px;font-weight:700;color:#1a2340;margin-bottom:6px; }
    .ia-search-wrap { position:relative; }
    .ia-search-wrap input {
      width:100%;padding:9px 12px 9px 36px;border:1px solid #c8cdd8;border-radius:6px;
      font-size:14px;color:#1a2340;outline:none;box-sizing:border-box;
    }
    .ia-search-wrap input:focus { border-color:#C9A050; }
    .ia-search-icon { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#8892a4; }
    .ia-nouveau-section {
      background:white;border:1px solid #d0d5e8;border-radius:10px;padding:24px;
      display:flex;flex-direction:column;gap:10px;
    }
    .ia-demarrer-btn {
      background:#0E1030;color:#C9A050;border:1px solid #C9A050;border-radius:6px;padding:9px 22px;
      font-size:14px;font-weight:700;cursor:pointer;align-self:flex-start;
    }
    .ia-demarrer-btn:hover { background:#C9A050;color:#0E1030; }
    .ia-accordion { margin-top:24px;border:1px solid #d0d5e8;border-radius:10px;overflow:hidden; }
    .ia-accordion-header {
      background:white;padding:16px 20px;cursor:pointer;font-size:14px;font-weight:600;
      color:#1a2340;display:flex;align-items:center;justify-content:space-between;
      user-select:none;
    }
    .ia-accordion-header:hover { background:#f8f9fd; }
    .ia-accordion-body { display:none;padding:16px 20px;border-top:1px solid #e0e3ef;font-size:13px;color:#8892a4; }
    .ia-accordion-body.open { display:block; }
    /* Profil modal */
    #modal-profil { display:none;position:fixed;inset:0;z-index:600;background:rgba(14,16,48,.45);align-items:center;justify-content:center; }
    #modal-profil.open { display:flex !important; }
    /* Impôt modal */
    #modal-impot { display:none;position:fixed;inset:0;z-index:600;background:rgba(14,16,48,.55);align-items:center;justify-content:center; }
    #modal-impot.open { display:flex !important; }
    /* Rente conjoint survivant modal */
    #modal-rente-conj { display:none;position:fixed;inset:0;z-index:600;background:rgba(14,16,48,.55);align-items:center;justify-content:center; }
    #modal-rente-conj.open { display:flex !important; }
    .impot-row { display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px;border-bottom:1px solid var(--border); }
    .impot-row:last-child { border-bottom:none; }
    .impot-label { color:var(--muted); }
    .impot-val { font-weight:600;color:var(--text); }
    .impot-total-row { display:flex;justify-content:space-between;align-items:center;padding:8px 0;font-size:14px;font-weight:700; }
    .impot-net-row { display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px; }
    .impot-section-title { font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:14px 0 6px; }
    /* Valeurs par défaut */
    .vd-header {
      background:white;border-bottom:1px solid #e0e3ef;padding:0 32px;height:64px;
      display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:10;
    }
    .vd-title { font-size:22px;font-weight:800;color:#003DA5; }
    .vd-body { max-width:700px;margin:0 auto;padding:32px; }
    .vd-section-title { font-size:16px;font-weight:700;color:#003DA5;margin:24px 0 12px; }
    .vd-section-subtitle { font-size:13px;font-weight:600;color:#4a5568;margin:16px 0 8px; }
    .vd-divider { border:none;border-top:1px solid #e0e3ef;margin:24px 0; }
    .vd-radio-group { display:flex;gap:8px;flex-wrap:wrap; }
    .vd-radio-pill input[type=radio] { display:none; }
    .vd-radio-pill label {
      padding:7px 16px;border:1px solid #c8cdd8;border-radius:20px;font-size:13px;
      cursor:pointer;color:#4a5568;background:white;transition:all .15s;
    }
    .vd-radio-pill input:checked + label { background:#003DA5;border-color:#003DA5;color:white; }
    .vd-inline { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
    /* Fonds d'urgence radio pills */
    .fu-radio-pill { display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border:1px solid var(--border);border-radius:20px;font-size:13px;cursor:pointer;color:var(--muted);background:var(--bg);transition:all .15s;user-select:none; }
    .fu-radio-pill input[type=radio] { display:none; }
    .fu-radio-pill:has(input:checked) { background:var(--navy);border-color:var(--navy);color:white; }
    .fu-actif-row { display:flex;align-items:center;justify-content:space-between;padding:10px 4px;border-bottom:1px solid var(--border);font-size:13px; }
    .fu-actif-row:last-child { border-bottom:none; }
    .fu-actif-check { width:16px;height:16px;accent-color:var(--navy);cursor:pointer;margin-right:8px; }
    .fu-actif-name { flex:1; }
    .fu-actif-valeur { font-weight:600;color:var(--text); }
    .abf-tooltip-wrap { position:relative;display:inline-flex;align-items:center; }
    .abf-tooltip-icon { display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:var(--navy);color:white;font-size:10px;font-weight:700;cursor:default;user-select:none;font-style:normal; }
    .abf-tooltip-box { display:none;position:absolute;left:20px;top:-8px;z-index:200;background:#1b1b2e;color:white;font-size:12px;font-weight:400;border-radius:8px;padding:10px 14px;width:280px;line-height:1.5;box-shadow:0 4px 16px rgba(0,0,0,.3);pointer-events:none; }
    .abf-tooltip-wrap:hover .abf-tooltip-box { display:block; }
    .deces-person-tab { background:none;border:none;border-bottom:3px solid transparent;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;color:var(--muted);transition:all .15s; }
    .deces-person-tab.active { border-bottom-color:var(--navy);color:var(--navy); }
    .deces-rr-person-tab { background:none;border:none;border-bottom:3px solid transparent;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;color:var(--muted);transition:all .15s; }
    .deces-rr-person-tab.active { border-bottom-color:var(--navy);color:var(--navy); }
    .deces-dep-item { padding:9px 16px;font-size:13px;cursor:pointer;color:var(--text); }
    .deces-dep-item:hover { background:var(--bg); }
    .deces-dep-row { display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px;flex-wrap:wrap; }
    .deces-dep-row:last-child { border-bottom:none; }
    .deces-av-row { display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px; }
    .deces-av-row:last-child { border-bottom:none; }
    .vd-portfolio-table { width:100%;border-collapse:collapse;margin-top:8px; }
    .vd-portfolio-table th { text-align:left;font-size:12px;font-weight:700;color:#8892a4;padding:6px 0; }
    .vd-portfolio-table td { padding:8px 0;font-size:14px; }
    .vd-footer { display:flex;gap:10px;justify-content:flex-end;padding:20px 32px;border-top:1px solid #e0e3ef;background:white;position:sticky;bottom:0; }

    /* ── TOAST ───────────────────────────────────────────── */
    .toast {
      position: fixed; bottom: 70px; right: 24px;
      background: #1a2340; color: white; padding: 10px 18px;
      border-radius: 8px; font-size: 13px; z-index: 9999;
      transform: translateY(20px); opacity: 0;
      transition: all .3s; pointer-events: none;
    }
    .toast.show { transform: translateY(0); opacity: 1; }
  </style>
</head>
<body>

<!-- ═══════════════════════════════════════════════
     PAGE ACCUEIL
════════════════════════════════════════════════ -->
<div id="page-accueil">
  <!-- Header iA -->
  <div class="ia-topbar">
    <img src="{{ asset('assets/vip-logo.png') }}" class="ia-logo" alt="VIP GPI"/>
    <div class="ia-topbar-right">
      <button class="ia-btn-secondary" onclick="openProfilModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
        Profil
      </button>
      <button class="ia-btn-secondary" onclick="openValeursDefaut()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        Valeurs par défaut
      </button>
      <button class="ia-btn-secondary" onclick="openImpotModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        Gestion de l'impôt
      </button>
      <button class="ia-btn-secondary" onclick="openRenteConjModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
        Rente conjoint survivant
      </button>
      <button class="ia-btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4a2 2 0 0 0 2 2m6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1z"/></svg>
        Nouveautés
      </button>
    </div>
  </div>
  <div class="ia-bottombar"></div>

  <!-- Corps -->
  <div class="ia-landing-body">
    <h1 class="ia-landing-title">Mon parcours financier</h1>
    <div class="ia-two-col">
      <!-- Rechercher un client -->
      <div class="ia-search-section">
        <div class="ia-field-label">Rechercher un client existant</div>
        <div class="ia-search-wrap">
          <svg class="ia-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 26 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/></svg>
          <input type="text" placeholder="Commencez à taper le nom" id="accueil-search"/>
        </div>
      </div>
      <!-- Nouveau client -->
      <div class="ia-nouveau-section">
        <div class="ia-field-label">Nouveau client</div>
        <button class="ia-demarrer-btn" onclick="demarrerABF()">Démarrer</button>
      </div>
    </div>
    <!-- Derniers parcours -->
    <div class="ia-accordion">
      <div class="ia-accordion-header" onclick="toggleAccordion(this)">
        Derniers parcours financiers réalisés
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
      </div>
      <div class="ia-accordion-body">
        Aucun parcours récent dans cette démo.
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     MODAL PROFIL
════════════════════════════════════════════════ -->
<div id="modal-profil">
  <div style="background:white;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Profil</h4>
      <button onclick="closeProfilModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
    </div>
    <div style="padding:20px 24px">
      <div class="form-group">
        <label class="form-label">Titre professionnel</label>
        <input class="form-input" id="profil-titre-fr" type="text" value="Conseiller en sécurité financière"/>
      </div>
      <div class="form-group">
        <label class="form-label">Professional title</label>
        <input class="form-input" id="profil-titre-en" type="text" value="Financial Security Advisor"/>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
      <button class="btn btn-secondary" onclick="closeProfilModal()">Annuler</button>
      <button class="btn btn-primary" onclick="saveProfilModal()">Enregistrer</button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     MODAL GESTION DE L'IMPÔT
════════════════════════════════════════════════ -->
<div id="modal-impot">
  <div style="background:white;border-radius:14px;width:100%;max-width:680px;box-shadow:0 24px 64px rgba(0,0,0,.28);overflow:hidden;margin:20px;max-height:92vh;display:flex;flex-direction:column">
    <!-- Header -->
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <div>
        <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Paramètres fiscaux</h4>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Québec 2026 — modifiez les taux et plafonds utilisés dans les calculs</div>
      </div>
      <button onclick="closeImpotModal()" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;padding:0 4px;line-height:1">×</button>
    </div>
    <!-- Body -->
    <div style="padding:20px 24px;overflow-y:auto;flex:1" id="impot-params-body">

      <!-- Paliers fédéraux -->
      <div class="impot-section-title">Paliers d'imposition — Fédéral</div>
      <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px">
        <thead><tr style="background:#f0f2f8">
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Revenu jusqu'à</th>
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Taux marginal (%)</th>
        </tr></thead>
        <tbody id="impot-fed-brackets"></tbody>
      </table>
      <!-- Crédit personnel fédéral -->
      <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px;margin-top:8px">
        <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px">Montant personnel de base — Fédéral</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant max ($)</div>
            <input class="form-input" id="fp-fed-baseMax" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant min ($)</div>
            <input class="form-input" id="fp-fed-baseMin" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Seuil bas ($)</div>
            <input class="form-input" id="fp-fed-baseThreshLow" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Seuil haut ($)</div>
            <input class="form-input" id="fp-fed-baseThreshHigh" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
        <div style="margin-top:8px">
          <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux du crédit (%)</div>
          <input class="form-input" id="fp-fed-creditRate" type="text" style="font-size:12px;padding:5px 8px;width:100px"/>
        </div>
      </div>

      <!-- Paliers québécois -->
      <div class="impot-section-title" style="margin-top:18px">Paliers d'imposition — Québec</div>
      <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px">
        <thead><tr style="background:#f0f2f8">
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Revenu jusqu'à</th>
          <th style="padding:6px 10px;text-align:left;font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px">Taux marginal (%)</th>
        </tr></thead>
        <tbody id="impot-qc-brackets"></tbody>
      </table>
      <!-- Crédit personnel Québec -->
      <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px;margin-top:8px">
        <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px">Montant personnel de base — Québec</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Montant ($)</div>
            <input class="form-input" id="fp-qc-base" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux du crédit (%)</div>
            <input class="form-input" id="fp-qc-creditRate" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
      </div>

      <!-- Cotisations sociales -->
      <div class="impot-section-title" style="margin-top:18px">Cotisations sociales</div>
      <div style="background:#f8f9fd;border-radius:8px;padding:8px 14px">
        <!-- RRQ -->
        <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">RRQ</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr;gap:8px;margin-bottom:12px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Exemption ($)</div>
            <input class="form-input" id="fp-rrq-exemption" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond 1 ($)</div>
            <input class="form-input" id="fp-rrq-ceil1" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux 1 (%)</div>
            <input class="form-input" id="fp-rrq-rate1" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond 2 ($)</div>
            <input class="form-input" id="fp-rrq-ceil2" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux 2 (%)</div>
            <input class="form-input" id="fp-rrq-rate2" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
        <!-- AE -->
        <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">Assurance-emploi (AE)</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond ($)</div>
            <input class="form-input" id="fp-ae-ceil" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux (%)</div>
            <input class="form-input" id="fp-ae-rate" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
        <!-- RQAP -->
        <div style="font-size:11px;font-weight:700;color:var(--navy);margin-bottom:6px">RQAP</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Plafond ($)</div>
            <input class="form-input" id="fp-rqap-ceil" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:3px">Taux (%)</div>
            <input class="form-input" id="fp-rqap-rate" type="text" style="font-size:12px;padding:5px 8px"/>
          </div>
        </div>
      </div>

    </div>
    <!-- Footer -->
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#f8f9fd;flex-shrink:0">
      <button class="btn btn-secondary" onclick="impotResetParams()" style="font-size:12px">Rétablir 2026</button>
      <div style="display:flex;gap:8px">
        <button class="btn btn-secondary" onclick="closeImpotModal()">Annuler</button>
        <button class="btn btn-primary" onclick="impotSaveParams()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     PAGE VALEURS PAR DÉFAUT
════════════════════════════════════════════════ -->
<div id="page-valeurs-defaut" style="display:none">
  <div class="vd-header">
    <div style="font-size:20px;font-weight:800;color:#003DA5">Valeurs par défaut</div>
    <div style="display:flex;gap:8px">
      <button class="btn btn-secondary" onclick="closeValeursDefaut()">Annuler</button>
      <button class="btn btn-primary" onclick="closeValeursDefaut()">Enregistrer</button>
    </div>
  </div>
  <div class="vd-body">
    <!-- Province -->
    <div class="form-group">
      <label class="form-label">Province d'imposition</label>
      <select class="form-select" id="vd-province" style="max-width:300px">
        <option>Alberta</option><option>Colombie-Britannique</option>
        <option>Île-du-Prince-Édouard</option><option>Manitoba</option>
        <option>Nouveau-Brunswick</option><option>Nouvelle-Écosse</option>
        <option>Nunavut</option><option>Ontario</option>
        <option selected>Québec</option><option>Saskatchewan</option>
        <option>Terre-Neuve-et-Labrador</option>
        <option>Territoires du Nord-Ouest</option><option>Yukon</option>
      </select>
    </div>
    <hr class="vd-divider"/>
    <!-- Fonds d'urgence -->
    <div class="vd-section-title">Fonds d'urgence</div>
    <div class="vd-radio-group" style="margin-bottom:12px">
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-revenu" value="income" checked/><label for="vd-fu-revenu">Revenu mensuel</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-dep" value="expenses"/><label for="vd-fu-dep">Dépenses mensuelles</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-montant" value="amount"/><label for="vd-fu-montant">Montant fixe</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-fu" id="vd-fu-aucun" value="none"/><label for="vd-fu-aucun">Aucun</label></div>
    </div>
    <div class="vd-inline">
      <div class="input-sfx" style="max-width:80px"><input class="form-input" id="vd-fu-mois" type="text" value="3"/></div>
      <span style="font-size:13px;color:#4a5568">Mois</span>
    </div>
    <hr class="vd-divider"/>
    <!-- Décès -->
    <div class="vd-section-title">Décès</div>
    <div class="vd-section-subtitle">Frais funéraires</div>
    <div class="input-sfx" style="max-width:160px"><input class="form-input" id="vd-funerailles" type="text" value="10 000"/><span class="sfx">$</span></div>
    <div class="vd-section-subtitle">Remplacement du revenu</div>
    <div class="vd-inline">
      <div class="vd-radio-group">
        <div class="vd-radio-pill"><input type="radio" name="vd-deces-rr" id="vd-dc-familial" value="family" checked/><label for="vd-dc-familial">Familial</label></div>
        <div class="vd-radio-pill"><input type="radio" name="vd-deces-rr" id="vd-dc-indiv" value="individual"/><label for="vd-dc-indiv">Individuel</label></div>
      </div>
      <div class="input-sfx" style="max-width:90px"><input class="form-input" id="vd-deces-pct" type="text" value="70"/><span class="sfx">%</span></div>
      <span style="font-size:13px;color:#4a5568">du revenu</span>
    </div>
    <div class="vd-section-subtitle">Salaire</div>
    <div class="vd-inline">
      <div class="vd-radio-group">
        <div class="vd-radio-pill"><input type="radio" name="vd-deces-sal" id="vd-dc-brut" value="gross" checked/><label for="vd-dc-brut">Brut</label></div>
        <div class="vd-radio-pill"><input type="radio" name="vd-deces-sal" id="vd-dc-net" value="net"/><label for="vd-dc-net">Net</label></div>
      </div>
      <div class="vd-radio-group">
        <div class="vd-radio-pill"><input type="radio" name="vd-deces-freq" id="vd-dc-annuel" value="yearly" checked/><label for="vd-dc-annuel">Annuel</label></div>
        <div class="vd-radio-pill"><input type="radio" name="vd-deces-freq" id="vd-dc-mensuel" value="monthly"/><label for="vd-dc-mensuel">Mensuel</label></div>
      </div>
    </div>
    <hr class="vd-divider"/>
    <!-- Invalidité -->
    <div class="vd-section-title">Invalidité</div>
    <div class="vd-section-subtitle">Approche de calcul</div>
    <div class="vd-radio-group" style="margin-bottom:12px">
      <div class="vd-radio-pill"><input type="radio" name="vd-inv-type" id="vd-inv-rr" value="incomeReplacement" checked/><label for="vd-inv-rr">Remplacement du revenu</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-inv-type" id="vd-inv-dep" value="expensesCoverage"/><label for="vd-inv-dep">Dépenses courantes</label></div>
    </div>
    <div class="vd-inline">
      <div class="vd-radio-group">
        <div class="vd-radio-pill"><input type="radio" name="vd-inv-sal" id="vd-inv-brut" value="gross" checked/><label for="vd-inv-brut">Brut</label></div>
        <div class="vd-radio-pill"><input type="radio" name="vd-inv-sal" id="vd-inv-net" value="net"/><label for="vd-inv-net">Net</label></div>
      </div>
      <div class="input-sfx" style="max-width:90px"><input class="form-input" id="vd-inv-pct" type="text" value="70"/><span class="sfx">%</span></div>
      <span style="font-size:13px;color:#4a5568">du revenu</span>
    </div>
    <hr class="vd-divider"/>
    <!-- Maladie grave -->
    <div class="vd-section-title">Maladie grave</div>
    <div class="vd-section-subtitle">Niveau de protection</div>
    <div class="vd-radio-group">
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-aucun" value="none"/><label for="vd-mg-aucun">Aucun</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-base" value="base"/><label for="vd-mg-base">Base</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-confort" value="comfort" checked/><label for="vd-mg-confort">Confort</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-mg" id="vd-mg-sup" value="premium"/><label for="vd-mg-sup">Supérieur</label></div>
    </div>
    <hr class="vd-divider"/>
    <!-- Retraite -->
    <div class="vd-section-title">Retraite</div>
    <div class="vd-section-subtitle">Objectif</div>
    <div class="vd-inline">
      <div class="input-sfx" style="max-width:90px"><input class="form-input" id="vd-ret-pct" type="text" value="70"/><span class="sfx">%</span></div>
      <span style="font-size:13px;color:#4a5568">du revenu net</span>
      <div class="vd-radio-group">
        <div class="vd-radio-pill"><input type="radio" name="vd-ret-freq" id="vd-ret-annuel" value="yearly" checked/><label for="vd-ret-annuel">Annuel</label></div>
        <div class="vd-radio-pill"><input type="radio" name="vd-ret-freq" id="vd-ret-mensuel" value="monthly"/><label for="vd-ret-mensuel">Mensuel</label></div>
      </div>
    </div>
    <div class="vd-section-subtitle">Approche de calcul du sommaire</div>
    <div class="vd-radio-group">
      <div class="vd-radio-pill"><input type="radio" name="vd-ret-calc" id="vd-ret-moy" value="average" checked/><label for="vd-ret-moy">Moyenne</label></div>
      <div class="vd-radio-pill"><input type="radio" name="vd-ret-calc" id="vd-ret-total" value="total"/><label for="vd-ret-total">Total</label></div>
    </div>
    <hr class="vd-divider"/>
    <!-- Inflation et rendement -->
    <div class="vd-section-title">Inflation et rendement</div>
    <div class="vd-section-subtitle">Inflation</div>
    <div class="input-sfx" style="max-width:120px;margin-bottom:20px"><input class="form-input" id="vd-inflation" type="text" value="2,10"/><span class="sfx">%</span></div>
    <table class="vd-portfolio-table">
      <thead><tr><th>Portefeuille</th><th>Rendement net</th></tr></thead>
      <tbody>
        <tr><td>Prudent</td><td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-prudent" type="text" value="3,00"/><span class="sfx">%</span></div></td></tr>
        <tr><td>Modéré</td><td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-modere" type="text" value="3,30"/><span class="sfx">%</span></div></td></tr>
        <tr><td>Équilibré</td><td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-equilibre" type="text" value="3,70"/><span class="sfx">%</span></div></td></tr>
        <tr><td>Croissance</td><td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-croissance" type="text" value="4,00"/><span class="sfx">%</span></div></td></tr>
        <tr><td>Audacieux</td><td><div class="input-sfx" style="max-width:110px"><input class="form-input" id="vd-p-audacieux" type="text" value="4,30"/><span class="sfx">%</span></div></td></tr>
      </tbody>
    </table>
    <div style="margin-top:20px;font-size:13px;color:#4a5568">
      <button style="background:none;border:1px solid var(--border);border-radius:6px;padding:7px 14px;font-size:13px;cursor:pointer;margin-right:8px">↻ Réinitialiser</button>
      aux <a href="https://app.institutpf.org/?locale=fr#/guidelines" target="_blank" style="color:#003DA5">normes de l'Institut de planification financière</a>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     MODAL HYPOTHÈSES
════════════════════════════════════════════════ -->
<div id="modal-hypotheses" style="display:none;position:fixed;inset:0;z-index:700;background:rgba(14,16,48,.55);align-items:center;justify-content:center">
  <div style="background:white;border-radius:14px;width:100%;max-width:520px;box-shadow:0 24px 64px rgba(0,0,0,.28);overflow:hidden;margin:20px;max-height:92vh;display:flex;flex-direction:column">
    <!-- Header -->
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Hypothèses pour ce parcours</h4>
      <button onclick="closeHypothesesModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;line-height:1;padding:0 4px">×</button>
    </div>
    <!-- Body -->
    <div style="padding:20px 24px;overflow-y:auto;flex:1">
      <!-- Reset -->
      <div style="margin-bottom:18px">
        <button class="btn btn-secondary btn-sm" onclick="resetHypotheses()" style="display:inline-flex;align-items:center;gap:6px">
          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 26 24" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
          Réinitialiser
        </button>
        <span style="font-size:12px;color:var(--muted);margin-left:8px">aux valeurs par défaut</span>
      </div>
      <!-- Inflation -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
        <label class="form-label" style="margin:0">Inflation</label>
        <div class="input-sfx" style="max-width:100px">
          <input class="form-input" id="hyp-inflation" type="text" value="2,10" style="text-align:right"/>
          <span class="sfx">%</span>
        </div>
      </div>
      <!-- Espérance de vie -->
      <div style="padding:12px 0;border-bottom:1px solid var(--border)">
        <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:10px">Espérance de vie</div>
        <div style="display:flex;gap:16px;flex-wrap:wrap">
          <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:180px">
            <label class="form-label" style="margin:0;flex:1" id="hyp-ev-client-label">Client</label>
            <div class="input-sfx" style="max-width:90px">
              <input class="form-input" id="hyp-ev-client" type="text" value="94" style="text-align:right" maxlength="3" oninput="this.value=this.value.replace(/\D/g,'')"/>
              <span class="sfx">ans</span>
            </div>
          </div>
          <div id="hyp-ev-conj-wrap" style="display:flex;align-items:center;gap:8px;flex:1;min-width:180px">
            <label class="form-label" style="margin:0;flex:1" id="hyp-ev-conj-label">Conjoint(e)</label>
            <div class="input-sfx" style="max-width:90px">
              <input class="form-input" id="hyp-ev-conj" type="text" value="96" style="text-align:right" maxlength="3" oninput="this.value=this.value.replace(/\D/g,'')"/>
              <span class="sfx">ans</span>
            </div>
          </div>
        </div>
      </div>
      <!-- Rendements portefeuilles -->
      <div style="padding:12px 0">
        <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:10px">Rendement net par portefeuille</div>
        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <thead>
            <tr style="border-bottom:1px solid var(--border)">
              <th style="text-align:left;padding:6px 8px;font-weight:600;color:var(--muted);font-size:12px">Portefeuille</th>
              <th style="text-align:right;padding:6px 8px;font-weight:600;color:var(--muted);font-size:12px">Rendement net</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Prudent</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-prudent" type="text" value="3,00" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Modéré</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-modere" type="text" value="3,30" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Équilibré</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-equilibre" type="text" value="3,70" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px">Croissance</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-croissance" type="text" value="4,00" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
            <tr>
              <td style="padding:8px">Audacieux</td>
              <td style="padding:8px;text-align:right"><div class="input-sfx" style="max-width:90px;margin-left:auto"><input class="form-input" id="hyp-port-audacieux" type="text" value="4,30" style="text-align:right"/><span class="sfx">%</span></div></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Footer -->
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd;flex-shrink:0">
      <button class="btn btn-secondary" onclick="closeHypothesesModal()">Annuler</button>
      <button class="btn btn-primary" onclick="saveHypotheses()">Enregistrer</button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     MODAL RENTE CONJOINT SURVIVANT
════════════════════════════════════════════════ -->
<div id="modal-rente-conj">
  <div style="background:white;border-radius:14px;width:100%;max-width:620px;box-shadow:0 24px 64px rgba(0,0,0,.28);overflow:hidden;margin:20px;max-height:92vh;display:flex;flex-direction:column">
    <!-- Header -->
    <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <div>
        <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Rente de conjoint survivant — RRQ/RPC</h4>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Montants maximaux utilisés pour les suggestions automatiques dans la section Décès</div>
      </div>
      <button onclick="closeRenteConjModal()" style="background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer;padding:0 4px;line-height:1">×</button>
    </div>
    <!-- Body -->
    <div style="padding:20px 24px;overflow-y:auto;flex:1">
      <!-- Régime -->
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;font-size:13px">
        <span style="font-weight:600;color:var(--navy)">Régime :</span>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="rc-regime" id="rc-regime-rrq" value="rrq" checked onchange="rcToggleRegime()"/> RRQ (Québec)</label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="rc-regime" id="rc-regime-cpp" value="cpp" onchange="rcToggleRegime()"/> CPP / RPC (autres provinces)</label>
      </div>
      <!-- Année + source -->
      <div id="rc-rrq-header" style="display:flex;align-items:center;gap:12px;margin-bottom:18px;background:#f0f4ff;border-radius:8px;padding:10px 14px;font-size:13px">
        <span style="color:var(--muted)">Année en vigueur :</span>
        <input class="form-input" id="rc-annee" type="text" value="2026" style="width:80px;text-align:center" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
        <span style="color:var(--muted);font-size:12px">(Source : RRQ — Montants maximaux)</span>
      </div>
      <!-- Section CPP (cachée par défaut) -->
      <div id="rc-cpp-section" style="display:none;margin-bottom:18px">
        <div style="background:#f0f4ff;border-radius:8px;padding:14px;font-size:13px">
          <div style="font-weight:600;color:var(--navy);margin-bottom:10px">Paramètres CPP / RPC</div>
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
            <span style="color:var(--muted);white-space:nowrap">Portion fixe mensuelle :</span>
            <div class="input-sfx" style="max-width:130px"><input class="form-input" id="rc-cpp-fixed" type="text" value="217,83"/><span class="sfx">$</span></div>
          </div>
          <div style="background:#eef2ff;border-radius:6px;padding:8px 12px;font-size:12px;color:var(--muted);line-height:1.6">
            <strong style="color:var(--navy)">Formule appliquée :</strong><br/>
            &bull; Survivant &lt; 65 ans : <em>portion fixe + 37,5 % × rente mensuelle du défunt</em><br/>
            &bull; Survivant ≥ 65 ans : <em>60 % × rente mensuelle du défunt</em>
          </div>
        </div>
      </div>
      <!-- Section RRQ -->
      <div id="rc-rrq-section">
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr style="background:#f0f2f8;border-bottom:2px solid var(--border)">
            <th style="padding:9px 12px;text-align:left;font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px">Situation du conjoint survivant</th>
            <th style="padding:9px 12px;text-align:right;font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px">Montant max. mensuel</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 12px">Moins de 45 ans — sans enfant à charge</td>
            <td style="padding:10px 12px;text-align:right">
              <div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-m45-sans" type="text" value="719,50"/><span class="sfx">$</span></div>
            </td>
          </tr>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 12px">Moins de 45 ans — avec enfant(s) à charge</td>
            <td style="padding:10px 12px;text-align:right">
              <div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-m45-avec" type="text" value="1 129,95"/><span class="sfx">$</span></div>
            </td>
          </tr>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 12px">Moins de 45 ans — invalide</td>
            <td style="padding:10px 12px;text-align:right">
              <div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-m45-inv" type="text" value="1 134,61"/><span class="sfx">$</span></div>
            </td>
          </tr>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 12px">45 à 65 ans</td>
            <td style="padding:10px 12px;text-align:right">
              <div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-45-65" type="text" value="1 173,58"/><span class="sfx">$</span></div>
            </td>
          </tr>
          <tr>
            <td style="padding:10px 12px">65 ans et plus — sans rente de retraite</td>
            <td style="padding:10px 12px;text-align:right">
              <div class="input-sfx" style="max-width:130px;margin-left:auto"><input class="form-input" id="rc-65plus" type="text" value="881,48"/><span class="sfx">$</span></div>
            </td>
          </tr>
        </tbody>
      </table>
      <div style="margin-top:14px;font-size:12px;color:var(--muted);background:#fff8e6;border-left:3px solid var(--gold);padding:8px 12px;border-radius:0 6px 6px 0">
        Ces montants sont utilisés pour suggérer automatiquement la rente de conjoint survivant lors de l'analyse Décès, selon l'âge du survivant et la présence d'enfants à charge.
      </div>
      </div><!-- /rc-rrq-section -->
    </div>
    <!-- Footer -->
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-shrink:0">
      <button onclick="rcReset()" style="background:none;border:1px solid var(--border);border-radius:6px;padding:7px 14px;font-size:13px;cursor:pointer">↻ Réinitialiser</button>
      <div style="display:flex;gap:10px">
        <button class="btn btn-secondary" onclick="closeRenteConjModal()">Annuler</button>
        <button class="btn btn-primary" onclick="saveRenteConjModal()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<!-- TOP BAR -->
<div class="topbar">
  <div class="topbar-logo">
    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
      <rect width="32" height="32" rx="6" fill="#C9A050"/>
      <text x="16" y="22" text-anchor="middle" font-size="14" font-weight="800" fill="#0E1030" font-family="sans-serif">VG</text>
    </svg>
    <span>VIP GPI — ABF</span>
  </div>
  <div class="topbar-right">
    <span id="abf-topbar-date"></span>
    <button id="btn-hypotheses" onclick="openHypothesesModal()" title="Hypothèses" style="background:none;border:1px solid rgba(170,179,204,.35);border-radius:6px;color:#aab3cc;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;line-height:1" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(170,179,204,.35)';this.style.color='#aab3cc'">
      <svg viewBox="64 64 896 896" width="14" height="14" fill="currentColor"><path d="M924.8 625.7l-65.5-56c3.1-19 4.7-38.4 4.7-57.8s-1.6-38.8-4.7-57.8l65.5-56a32.03 32.03 0 0 0 9.3-35.2l-.9-2.6a443.74 443.74 0 0 0-79.7-137.9l-1.8-2.1a32.12 32.12 0 0 0-35.1-9.5l-81.3 28.9c-30-24.6-63.5-44-99.7-57.6l-15.7-85a32.05 32.05 0 0 0-25.8-25.7l-2.7-.5c-52.1-9.4-106.9-9.4-159 0l-2.7.5a32.05 32.05 0 0 0-25.8 25.7l-15.8 85.4a351.86 351.86 0 0 0-99 57.4l-81.9-29.1a32 32 0 0 0-35.1 9.5l-1.8 2.1a446.02 446.02 0 0 0-79.7 137.9l-.9 2.6c-4.5 12.5-.8 26.5 9.3 35.2l66.3 56.6c-3.1 18.8-4.6 38-4.6 57.1 0 19.2 1.5 38.4 4.6 57.1L99 625.5a32.03 32.03 0 0 0-9.3 35.2l.9 2.6c18.1 50.4 44.9 96.9 79.7 137.9l1.8 2.1a32.12 32.12 0 0 0 35.1 9.5l81.9-29.1c29.8 24.5 63.1 43.9 99 57.4l15.8 85.4a32.05 32.05 0 0 0 25.8 25.7l2.7.5a449.4 449.4 0 0 0 159 0l2.7-.5a32.05 32.05 0 0 0 25.8-25.7l15.7-85a350 350 0 0 0 99.7-57.6l81.3 28.9a32 32 0 0 0 35.1-9.5l1.8-2.1c34.8-41.1 61.6-87.5 79.7-137.9l.9-2.6c4.5-12.3.8-26.3-9.3-35zM788.3 465.9c2.5 15.1 3.8 30.6 3.8 46.1s-1.3 31-3.8 46.1l-6.6 40.1 74.7 63.9a370.03 370.03 0 0 1-42.6 73.6L721 702.8l-31.4 25.8c-23.9 19.6-50.5 35-79.3 45.8l-38.1 14.3-17.9 97a377.5 377.5 0 0 1-85 0l-17.9-97.2-37.8-14.5c-28.5-10.8-55-26.2-78.7-45.7l-31.4-25.9-93.4 33.2c-17-22.9-31.2-47.6-42.6-73.6l75.5-64.5-6.5-40c-2.4-14.9-3.7-30.3-3.7-45.5 0-15.3 1.2-30.6 3.7-45.5l6.5-40-75.5-64.5c11.3-26.1 25.6-50.7 42.6-73.6l93.4 33.2 31.4-25.9c23.7-19.5 50.2-34.9 78.7-45.7l37.9-14.3 17.9-97.2c28.1-3.2 56.8-3.2 85 0l17.9 97 38.1 14.3c28.7 10.8 55.4 26.2 79.3 45.8l31.4 25.8 92.8-32.9c17 22.9 31.2 47.6 42.6 73.6L781.8 426l6.5 39.9zM512 326c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm79.2 255.2A111.6 111.6 0 0 1 512 614c-29.9 0-58-11.7-79.2-32.8A111.6 111.6 0 0 1 400 502c0-29.9 11.7-58 32.8-79.2C454 401.6 482.1 390 512 390c29.9 0 58 11.6 79.2 32.8A111.6 111.6 0 0 1 624 502c0 29.9-11.7 58-32.8 79.2z"/></svg>
      Hypothèses
    </button>
    <a href="{{ url("/abf/abf-cases") }}" style="background:none;border:1px solid rgba(170,179,204,.35);border-radius:6px;color:#aab3cc;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:12px;padding:5px 10px;line-height:1;text-decoration:none" onmouseover="this.style.borderColor='var(--gold)';this.style.color='var(--gold)'" onmouseout="this.style.borderColor='rgba(170,179,204,.35)';this.style.color='#aab3cc'">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
      Dossiers
    </a>
    <span id="abf-topbar-user">👤 <strong>Jean Tremblay</strong></span>
  </div>
</div>

<!-- LAYOUT -->
<div class="layout">

  <!-- SIDEBAR -->
  <nav class="sidebar">
    <div class="nav-group">
      <div class="nav-group-title">
        <svg viewBox="0 0 26 24"><path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM10.148 17.531l-5.531-5.508 2.133-2.133 3.398 3.398 7.195-7.195 2.133 2.109-9.328 9.328z"/></svg>
        Situation actuelle
      </div>
      <button class="nav-item active" onclick="goTo('infos-perso',this)">
        <span class="dot"></span> Informations personnelles
      </button>
      <button class="nav-item locked" onclick="goTo('objectifs',this)">
        <span class="dot"></span> Objectifs
      </button>
      <button class="nav-item locked" onclick="goTo('actifs-passifs',this)">
        <span class="dot"></span> Actifs et passifs
      </button>
      <button class="nav-item locked" onclick="goTo('revenu-epargne',this)">
        <span class="dot"></span> Revenu et épargne
      </button>
      <button class="nav-item locked" onclick="goTo('fonds-urgence',this)">
        <span class="dot"></span> Fonds d'urgence
      </button>
      <button class="nav-item locked" onclick="goTo('deces',this)">
        <span class="dot"></span> Décès
      </button>
      <button class="nav-item locked" onclick="goTo('invalidite',this)">
        <span class="dot"></span> Invalidité
      </button>
      <button class="nav-item locked" onclick="goTo('maladie-grave',this)">
        <span class="dot"></span> Maladie grave
      </button>
      <button class="nav-item locked" onclick="goTo('projets',this)">
        <span class="dot"></span> Projets
      </button>
      <button class="nav-item locked" onclick="goTo('retraite',this)">
        <span class="dot"></span> Retraite
      </button>
    </div>

    <div class="nav-group">
      <div class="nav-group-title">
        <svg viewBox="0 0 26 24"><path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM10.148 17.531l-5.531-5.508 2.133-2.133 3.398 3.398 7.195-7.195 2.133 2.109-9.328 9.328z"/></svg>
        Résultats
      </div>
      <button class="nav-item locked" onclick="goTo('recommandations',this)">
        <span class="dot"></span> Recommandations
      </button>
      <button class="nav-item locked" onclick="goTo('rapport',this)">
        <span class="dot"></span> Rapport
      </button>
    </div>
  </nav>

  <!-- MAIN -->
  <main class="main">

    <!-- ── PAGE: Informations personnelles ── -->
    <div id="page-infos-perso" class="page active">
      <div class="page-title">Informations personnelles</div>
      <div class="page-subtitle">Renseignements du client principal</div>

      <!-- Client -->
      <div class="card">
        <div class="card-header">Client</div>
        <div class="card-body">
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">Prénom</label>
              <input class="form-input" id="client-prenom" type="text" value="WIGALIE" placeholder="Prénom"/>
            </div>
            <div class="col form-group">
              <label class="form-label required">Nom</label>
              <input class="form-input" id="client-nom" type="text" value="RAPHAEL" placeholder="Nom de famille"/>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">Date de naissance</label>
              <div class="date-row">
                <input class="form-input" id="client-ddn-jour" type="text" value="17" placeholder="Jour" style="max-width:70px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                <select class="form-select" id="client-ddn-mois">
                  <option value="">Mois</option>
                  <option>Janvier</option><option selected>Février</option><option>Mars</option>
                  <option>Avril</option><option>Mai</option><option>Juin</option>
                  <option>Juillet</option><option>Août</option><option>Septembre</option>
                  <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                </select>
                <input class="form-input" type="text" value="2005" placeholder="Année" style="max-width:90px" id="client-naissance-annee" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
              </div>
            </div>
            <div class="col form-group">
              <label class="form-label">Sexe</label>
              <div class="radio-group">
                <div class="radio-pill"><input type="radio" name="sexe" id="masculin" value="M"/><label for="masculin">Masculin</label></div>
                <div class="radio-pill"><input type="radio" name="sexe" id="feminin" value="F" checked/><label for="feminin">Féminin</label></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">État civil</label>
              <select class="form-select" id="client-etat-civil" onchange="syncConjointInfo()">
                <option>Marié(e)</option><option selected>Célibataire</option>
                <option>Divorcé(e)</option><option>Séparé(e)</option>
                <option>Conjoint(e) de fait</option><option>Union civile</option><option>Veuf/veuve</option>
              </select>
            </div>
            <div class="col form-group">
              <label class="form-label required">Province d'imposition</label>
              <select class="form-select" id="client-province">
                <option>Alberta</option><option>Colombie-Britannique</option>
                <option>Ontario</option><option selected>Québec</option>
                <option>Saskatchewan</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label">Courriel personnel</label>
              <input class="form-input" type="email" value="wigalieraphael@icloud.com"/>
            </div>
            <div class="col form-group">
              <label class="form-label required">Réside au Canada depuis</label>
              <input class="form-input" type="text" placeholder="Année (ex: 2010)" id="client-canada-depuis" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <label class="form-label required">Usage de tabac</label>
              <div class="radio-group">
                <div class="radio-pill"><input type="radio" name="tabac" id="tabac-oui" value="oui"/><label for="tabac-oui">Oui</label></div>
                <div class="radio-pill"><input type="radio" name="tabac" id="tabac-non" value="non" checked/><label for="tabac-non">Non</label></div>
              </div>
            </div>
            <div class="col form-group">
              <label class="form-label">Langue</label>
              <div class="radio-group">
                <div class="radio-pill"><input type="radio" name="langue" id="fr" value="fr" checked/><label for="fr">Français</label></div>
                <div class="radio-pill"><input type="radio" name="langue" id="en" value="en"/><label for="en">Anglais</label></div>
              </div>
            </div>
          </div>
        </div>
        <!-- Informations supplémentaires -->
        <button class="collapse-toggle" onclick="toggleCollapse(this)">
          Informations supplémentaires
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <div class="collapse-body">
          <!-- Téléphones -->
          <div class="row">
            <div class="col form-group">
              <label class="form-label">Cellulaire</label>
              <input class="form-input" type="tel" value="(438) 449-0965" placeholder="(514) 000-0000"/>
            </div>
            <div class="col form-group">
              <label class="form-label">Téléphone domicile</label>
              <input class="form-input" type="tel" placeholder="(514) 000-0000"/>
            </div>
          </div>
          <!-- Adresse structurée -->
          <div style="margin-bottom:4px">
            <label class="form-label required" style="margin-bottom:10px;display:block">Adresse</label>
            <div class="row">
              <div class="col form-group" style="max-width:120px">
                <label class="form-label">N° civique</label>
                <input class="form-input" type="text" id="client-addr-civique" value="11952" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Rue</label>
                <input class="form-input" type="text" id="client-addr-rue" value="AV JUBINVILLE" oninput="syncConjointInfo()"/>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Type d'unité</label>
                <select class="form-select" id="client-addr-type-unite" onchange="syncConjointInfo()">
                  <option value="">—</option><option selected>Appartement</option><option>Suite</option>
                  <option>Bureau</option><option>Unité</option>
                </select>
              </div>
              <div class="col form-group" style="max-width:100px">
                <label class="form-label">Numéro</label>
                <input class="form-input" type="text" id="client-addr-numero" value="2" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Case postale</label>
                <input class="form-input" type="text" id="client-addr-case" placeholder="—" oninput="syncConjointInfo()"/>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Ville</label>
                <input class="form-input" type="text" id="client-addr-ville" value="MONTRÉAL-NORD" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Province</label>
                <input class="form-input" type="text" id="client-addr-province" value="Québec" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Code postal</label>
                <input class="form-input" type="text" id="client-addr-postal" value="H1G 3T2" oninput="syncConjointInfo()"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Pays</label>
                <input class="form-input" type="text" id="client-addr-pays" value="Canada" disabled style="background:#f8f9fd"/>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Type de plan -->
      <div class="card">
        <div class="card-header">Type de plan</div>
        <div class="card-body">
          <div class="radio-group">
            <div class="radio-pill">
              <input type="radio" name="plan" id="individuel" value="individuel" checked
                onchange="document.getElementById('conjoint-section').style.display='none'"/>
              <label for="individuel">Individuel</label>
            </div>
            <div class="radio-pill">
              <input type="radio" name="plan" id="conjoint" value="conjoint"
                onchange="document.getElementById('conjoint-section').style.display='block';syncConjointInfo()"/>
              <label for="conjoint">Conjoint</label>
            </div>
          </div>
        </div>
      </div>

      <!-- Section conjoint(e) — masquée par défaut -->
      <div id="conjoint-section" style="display:none">
        <div class="card">
          <div class="card-header" style="background:#f0f3fa;display:flex;align-items:center;justify-content:space-between">
            <span>Conjoint(e)</span>
            <div style="display:flex;gap:12px;font-size:12px">
              <a href="#" style="color:var(--gold);text-decoration:none;font-weight:600">Supprimer le conjoint</a>
              <a href="#" style="color:var(--navy);text-decoration:none;font-weight:600">Changer le conjoint</a>
            </div>
          </div>
          <div class="card-body">
            <!-- Recherche client existant -->
            <div class="row" style="margin-bottom:8px">
              <div class="col-full form-group">
                <label class="form-label">Rechercher un client existant</label>
                <div style="position:relative">
                  <input class="form-input" type="text" placeholder="Commencez à taper le nom…"
                    style="padding-left:36px"/>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"
                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%)">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                  </svg>
                </div>
              </div>
            </div>
            <!-- Prénom / Nom -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Prénom</label>
                <input class="form-input" id="conjoint-prenom" type="text" placeholder="Prénom"/>
              </div>
              <div class="col form-group">
                <label class="form-label required">Nom</label>
                <input class="form-input" id="conjoint-nom" type="text" placeholder="Nom de famille"/>
              </div>
            </div>
            <!-- Date de naissance / Sexe -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Date de naissance</label>
                <div class="date-row">
                  <input class="form-input" id="conjoint-ddn-jour" type="text" placeholder="Jour" style="max-width:70px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                  <select class="form-select" id="conjoint-ddn-mois">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" type="text" placeholder="Année" style="max-width:90px" id="conjoint-naissance-annee" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Sexe</label>
                <div class="radio-group">
                  <div class="radio-pill"><input type="radio" name="co-sexe" id="co-masculin"/><label for="co-masculin">Masculin</label></div>
                  <div class="radio-pill"><input type="radio" name="co-sexe" id="co-feminin"/><label for="co-feminin">Féminin</label></div>
                </div>
              </div>
            </div>
            <!-- État civil / Province -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">État civil</label>
                <select class="form-select" id="conjoint-etat-civil">
                  <option value="">Sélectionnez…</option>
                  <option>Marié(e)</option><option>Célibataire</option>
                  <option>Divorcé(e)</option><option>Séparé(e)</option>
                  <option>Conjoint(e) de fait</option><option>Union civile</option><option>Veuf/veuve</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label required">Province d'imposition</label>
                <select class="form-select" id="conjoint-province">
                  <option>Alberta</option><option>Colombie-Britannique</option>
                  <option>Ontario</option><option selected>Québec</option>
                  <option>Saskatchewan</option>
                </select>
              </div>
            </div>
            <!-- Courriel / Année Canada -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Courriel personnel</label>
                <input class="form-input" type="email" placeholder="courriel@exemple.com"/>
              </div>
              <div class="col form-group">
                <label class="form-label required">Réside au Canada depuis</label>
                <input class="form-input" type="text" placeholder="Année (ex: 2010)" id="conjoint-canada-depuis" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
              </div>
            </div>
            <!-- Tabac / Langue -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Usage de tabac</label>
                <div class="radio-group">
                  <div class="radio-pill"><input type="radio" name="co-tabac" id="co-tabac-oui"/><label for="co-tabac-oui">Oui</label></div>
                  <div class="radio-pill"><input type="radio" name="co-tabac" id="co-tabac-non" checked/><label for="co-tabac-non">Non</label></div>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Langue</label>
                <div class="radio-group">
                  <div class="radio-pill"><input type="radio" name="co-langue" id="co-fr" checked/><label for="co-fr">Français</label></div>
                  <div class="radio-pill"><input type="radio" name="co-langue" id="co-en"/><label for="co-en">Anglais</label></div>
                </div>
              </div>
            </div>
          </div>
          <!-- Adresse conjoint structurée -->
          <div class="card-body" style="padding-top:0">
            <label class="form-label required" style="margin-bottom:10px;display:block">Adresse</label>
            <div class="row">
              <div class="col form-group" style="max-width:120px">
                <label class="form-label">N° civique</label>
                <input class="form-input" type="text" id="conjoint-addr-civique"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Rue</label>
                <input class="form-input" type="text" id="conjoint-addr-rue"/>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Type d'unité</label>
                <select class="form-select" id="conjoint-addr-type-unite">
                  <option value="">—</option><option>Appartement</option><option>Suite</option>
                  <option>Bureau</option><option>Unité</option>
                </select>
              </div>
              <div class="col form-group" style="max-width:100px">
                <label class="form-label">Numéro</label>
                <input class="form-input" type="text" id="conjoint-addr-numero"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Case postale</label>
                <input class="form-input" type="text" id="conjoint-addr-case" placeholder="—"/>
              </div>
            </div>
            <div class="row" style="margin-bottom:0">
              <div class="col form-group">
                <label class="form-label">Ville</label>
                <input class="form-input" type="text" id="conjoint-addr-ville"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Province</label>
                <input class="form-input" type="text" id="conjoint-addr-province"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Code postal</label>
                <input class="form-input" type="text" id="conjoint-addr-postal"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Pays</label>
                <input class="form-input" type="text" id="conjoint-addr-pays" value="Canada" disabled style="background:#f8f9fd"/>
              </div>
            </div>
          </div>
          <!-- Infos supp. conjoint collapsible -->
          <button class="collapse-toggle" onclick="toggleCollapse(this)">
            Informations supplémentaires
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div class="collapse-body">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Cellulaire</label>
                <input class="form-input" type="tel" placeholder="(514) 000-0000"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Téléphone domicile</label>
                <input class="form-input" type="tel" placeholder="(514) 000-0000"/>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enfants -->
      <div class="card">
        <div class="card-header">Enfant(s) et personne(s) à charge</div>
        <div class="card-body">
          <div id="enfants-list" class="list-empty">Aucun enfant ou personne à charge ajouté.</div>
          <button class="btn btn-primary btn-sm" style="margin-top:12px" onclick="openEnfantModal()">
            <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
            Ajouter
          </button>
        </div>
      </div>

      <!-- Renseignements légaux -->
      <div class="card" style="overflow:visible">
        <div class="card-header">Renseignements légaux</div>
        <div class="card-body" style="overflow:visible">
          <div id="legal-list" class="list-empty">Aucun document légal ajouté.</div>
          <!-- Bouton Ajouter + dropdown menu -->
          <div style="position:relative;display:inline-block;margin-top:12px" id="legal-menu-wrapper">
            <button class="btn btn-primary btn-sm" onclick="toggleLegalMenu(event)">
              <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter
            </button>
            <div id="legal-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:200;
              background:white;border:1px solid var(--border);border-radius:8px;
              box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:260px;overflow:hidden">
              <ul style="list-style:none;padding:4px 0;margin:0">
                <li><button class="legal-menu-item" onclick="openLegalModal('Contrat de mariage')">Contrat de mariage</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Contrat de vie commune')">Contrat de vie commune</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Acte d\'union civile')">Acte d'union civile</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Jugement de divorce')">Jugement de divorce</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Jugement de séparation de corps')">Jugement de séparation de corps</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Testament')">Testament</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Mandat de protection')">Mandat de protection</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Ordonnance de pension alimentaire')">Ordonnance de pension alimentaire</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Convention d\'achat/vente')">Convention d'achat/vente</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Engagement financier envers quelqu\'un')">Engagement financier envers quelqu'un</button></li>
                <li><button class="legal-menu-item" onclick="openLegalModal('Autre')">Autre</button></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- ── MODAL : Enfant ou personne à charge ── -->
      <div id="modal-enfant" style="display:none;position:fixed;inset:0;z-index:1000;
        background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:560px;
          box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <!-- Modal header -->
          <div style="padding:20px 24px 16px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between">
            <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Enfant ou personne à charge</h4>
            <button onclick="closeEnfantModal()" style="background:none;border:none;font-size:20px;
              color:var(--muted);cursor:pointer;line-height:1;padding:0 4px">×</button>
          </div>
          <!-- Modal body -->
          <div style="padding:20px 24px;max-height:70vh;overflow-y:auto">
            <!-- Recherche client -->
            <div class="form-group" style="margin-bottom:16px">
              <label class="form-label">Rechercher un client existant</label>
              <div style="position:relative">
                <input class="form-input" type="text" placeholder="Commencez à taper le nom…" style="padding-left:36px"/>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"
                  style="position:absolute;left:10px;top:50%;transform:translateY(-50%)">
                  <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
              </div>
            </div>
            <!-- Prénom / Nom -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label required">Prénom</label>
                <input class="form-input" id="enf-prenom" type="text"/>
              </div>
              <div class="col form-group">
                <label class="form-label required">Nom</label>
                <input class="form-input" id="enf-nom" type="text"/>
              </div>
            </div>
            <!-- Sexe / Date naissance -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Sexe</label>
                <select class="form-select" id="enf-sexe">
                  <option value="">Sélectionnez…</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">Date de naissance</label>
                <div class="date-row">
                  <input class="form-input" id="enf-jour" type="text" placeholder="Jour" style="max-width:65px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                  <select class="form-select" id="enf-mois">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" id="enf-annee" type="text" placeholder="Année" style="max-width:80px" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
                </div>
              </div>
            </div>
            <!-- Relation / À la charge -->
            <div class="row">
              <div class="col form-group">
                <label class="form-label" id="enf-relation-label">Relation avec le client</label>
                <select class="form-select" id="enf-relation">
                  <option value="">Sélectionnez…</option>
                  <option value="child">Enfant</option>
                  <option value="dependent">Autre</option>
                  <option value="fathermother">Père-Mère</option>
                  <option value="grandparent">Grand-parent</option>
                  <option value="grandchild">Petit-enfant</option>
                  <option value="sibling">Frère-Sœur</option>
                  <option value="otherrelative">Parenté</option>
                  <option value="exspouse">Ex-conjoint(e)</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">À la charge de</label>
                <select class="form-select" id="enf-charge">
                  <option value="">Sélectionnez…</option>
                </select>
              </div>
            </div>
          </div>
          <!-- Modal footer -->
          <div style="padding:14px 24px;border-top:1px solid var(--border);
            display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeEnfantModal()">Annuler</button>
            <button class="btn btn-primary" id="enf-submit" onclick="saveEnfant()">Enregistrer</button>
          </div>
        </div>
      </div>
    </div>

      <!-- ── MODAL : Document légal ── -->
      <div id="modal-legal" style="display:none;position:fixed;inset:0;z-index:1000;
        background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:520px;
          box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <!-- Modal header -->
          <div style="padding:20px 24px 16px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between">
            <h4 id="modal-legal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Document légal</h4>
            <button onclick="closeLegalModal()" style="background:none;border:none;font-size:20px;
              color:var(--muted);cursor:pointer;line-height:1;padding:0 4px">×</button>
          </div>
          <!-- Modal body -->
          <div style="padding:20px 24px;max-height:70vh;overflow-y:auto">
            <!-- Propriétaire -->
            <div class="form-group">
              <label class="form-label required">Propriétaire</label>
              <select class="form-select" id="leg-proprietaire">
                <option value="">Sélectionnez…</option>
              </select>
            </div>
            <!-- Facultatif section -->
            <div class="modal-facultatif-title">Facultatif</div>
            <!-- Date -->
            <div class="form-group">
              <label class="form-label">Date</label>
              <div class="date-row">
                <input class="form-input" id="leg-jour" type="text" placeholder="Jour" style="max-width:65px" maxlength="2" oninput="this.value=this.value.replace(/\D/g,'').slice(0,2)"/>
                <select class="form-select" id="leg-mois">
                  <option value="">Mois</option>
                  <option>Janvier</option><option>Février</option><option>Mars</option>
                  <option>Avril</option><option>Mai</option><option>Juin</option>
                  <option>Juillet</option><option>Août</option><option>Septembre</option>
                  <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                </select>
                <input class="form-input" id="leg-annee" type="text" placeholder="Année" style="max-width:80px" maxlength="4" oninput="this.value=this.value.replace(/\D/g,'').slice(0,4)"/>
              </div>
            </div>
            <!-- Type -->
            <div class="form-group">
              <label class="form-label">Type</label>
              <select class="form-select" id="leg-type">
                <option value="">Sélectionnez…</option>
                <option value="enfants">Enfants</option>
                <option value="conjoint">Conjoint</option>
              </select>
            </div>
            <!-- Note -->
            <div class="form-group">
              <label class="form-label">Note</label>
              <textarea class="form-input" id="leg-note" rows="3" style="resize:vertical"></textarea>
            </div>
          </div>
          <!-- Modal footer -->
          <div style="padding:14px 24px;border-top:1px solid var(--border);
            display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeLegalModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveLegal()">Enregistrer</button>
          </div>
        </div>
      </div>

    <!-- ── PAGE: Objectifs ── -->
    <div id="page-objectifs" class="page">
      <div class="page-title">Description des objectifs</div>

      <!-- Info banner -->
      <div style="display:flex;gap:10px;align-items:flex-start;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#1e40af;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#3b82f6" style="flex-shrink:0;margin-top:1px"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>Les objectifs sélectionnés seront affichés dans le rapport. Veuillez cliquer sur ceux-ci pour les adapter à la situation de votre client.</span>
      </div>

      <!-- Category template rendered via JS -->
      <div id="objectives-container"></div>
    </div>

    <!-- ── PAGE: Actifs et passifs ── -->
    <div id="page-actifs-passifs" class="page">
      <div class="page-title">Actifs et passifs</div>
      <div class="page-subtitle">Bilan patrimonial du client</div>

      <div style="display:flex;gap:20px;align-items:start">
      <div style="flex:1;min-width:0"><!-- cards start -->

      <!-- ACTIFS -->
      <div class="card" style="overflow:visible">
        <div class="card-header">Actifs</div>
        <div class="card-body" style="overflow:visible">
          <div id="actifs-list" class="list-empty">Aucun actif ajouté.</div>
          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">

            <!-- Placements -->
            <div style="position:relative" id="placement-menu-wrap">
              <button class="btn btn-primary btn-sm" onclick="toggleApMenu(event,'placement-dropdown')">
                <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                Ajouter un placement
              </button>
              <div id="placement-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:300;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:250px;overflow:hidden">
                <ul style="list-style:none;padding:4px 0;margin:0">
                  <li><button class="legal-menu-item" onclick="openPlacementModal('Compte bancaire')">Compte bancaire</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('Non enregistré')">Non enregistré</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('CELI')">CELI</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('CELIAPP')">CELIAPP</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REEE')">REEE</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER')">REER</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER conjoint')">REER conjoint</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER collectif')">REER collectif</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RVER')">RVER</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RPAC')">RPAC</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RPA à cotisations déterminées')">RPA à cotisations déterminées</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('FERR')">FERR</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('FRV')">FRV</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('CRI')">CRI</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('REER Immobilisé')">REER Immobilisé</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RPDB')">RPDB</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('RRS')">RRS</button></li>
                  <li><button class="legal-menu-item" onclick="openPlacementModal('Autre actif enregistré')">Autre actif enregistré</button></li>
                </ul>
              </div>
            </div>

            <!-- Biens -->
            <div style="position:relative" id="bien-menu-wrap">
              <button class="btn btn-primary btn-sm" onclick="toggleApMenu(event,'bien-dropdown')">
                <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                Ajouter un bien
              </button>
              <div id="bien-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:300;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:220px;overflow:hidden">
                <ul style="list-style:none;padding:4px 0;margin:0">
                  <li><button class="legal-menu-item" onclick="openBienModal('Résidence principale')">Résidence principale</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Résidence secondaire')">Résidence secondaire</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Véhicule')">Véhicule</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Immeuble locatif')">Immeuble locatif</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Terrain')">Terrain</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Objet de valeur')">Objet de valeur</button></li>
                  <li><button class="legal-menu-item" onclick="openBienModal('Autre bien')">Autre bien</button></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PASSIFS -->
      <div class="card" style="overflow:visible">
        <div class="card-header">Passifs</div>
        <div class="card-body" style="overflow:visible">
          <div id="passifs-list" class="list-empty">Aucun passif ajouté.</div>
          <div style="margin-top:14px;position:relative;display:inline-block" id="passif-menu-wrap">
            <button class="btn btn-primary btn-sm" onclick="toggleApMenu(event,'passif-dropdown')">
              <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
              Ajouter un passif
            </button>
            <div id="passif-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;z-index:300;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:230px;overflow:hidden">
              <ul style="list-style:none;padding:4px 0;margin:0">
                <li><button class="legal-menu-item" onclick="openPassifModal('Carte de crédit')">Carte de crédit</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Marge de crédit')">Marge de crédit</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Compte à payer')">Compte à payer</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt automobile')">Prêt automobile</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt personnel')">Prêt personnel</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt hypothécaire')">Prêt hypothécaire</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt commercial')">Prêt commercial</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt pour investissement')">Prêt pour investissement</button></li>
                <li><button class="legal-menu-item" onclick="openPassifModal('Prêt étudiant')">Prêt étudiant</button></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      </div><!-- cards end -->

      <!-- ── AP SIDEBAR (inline sticky) ── -->
      <div id="ap-sidebar">
        <div class="card">
          <div class="ap-sidebar-section">
            <div class="ap-sb-total">Valeur nette</div>
            <div class="ap-sb-total-val" id="ap-total-vn">0 $</div>
          </div>
          <div class="ap-sidebar-section">
            <div style="font-size:12px;font-weight:700;color:var(--navy);margin-bottom:8px" id="ap-client-name">WIGALIE</div>
            <div class="ap-sb-row"><span class="ap-sb-label">Valeur nette</span><span class="ap-sb-val" id="ap-client-vn">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Actifs</span><span class="ap-sb-val" style="color:var(--valid)" id="ap-client-actifs">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Passifs</span><span class="ap-sb-val" style="color:#ef4444" id="ap-client-passifs">0 $</span></div>
          </div>
          <div class="ap-sidebar-section" id="ap-conjoint-block" style="display:none">
            <div style="font-size:12px;font-weight:700;color:var(--navy);margin-bottom:8px" id="ap-conjoint-name">Conjoint(e)</div>
            <div class="ap-sb-row"><span class="ap-sb-label">Valeur nette</span><span class="ap-sb-val" id="ap-conjoint-vn">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Actifs</span><span class="ap-sb-val" style="color:var(--valid)" id="ap-conjoint-actifs">0 $</span></div>
            <div class="ap-sb-row"><span class="ap-sb-label">Passifs</span><span class="ap-sb-val" style="color:#ef4444" id="ap-conjoint-passifs">0 $</span></div>
          </div>
        </div>
      </div>

      </div><!-- flex end -->

      <!-- ── MODAL : Placement ── -->
      <div id="modal-placement" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="plac-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closePlacementModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="form-group">
              <label class="form-label">Description</label>
              <input class="form-input" id="plac-description" type="text"/>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="plac-proprietaire"><option value="">Sélectionnez…</option></select>
              </div>
              <div class="col form-group">
                <label class="form-label">Valeur</label>
                <div class="input-sfx"><input class="form-input" id="plac-valeur" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Portefeuille</label>
                <select class="form-select" id="plac-portefeuille" onchange="syncRendement()">
                  <option value="prudent">Prudent</option>
                  <option value="moderate">Modéré</option>
                  <option value="balanced" selected>Équilibré</option>
                  <option value="growth">Croissance</option>
                  <option value="aggressive">Audacieux</option>
                </select>
              </div>
              <div class="col form-group">
                <label class="form-label">Rendement</label>
                <div class="input-sfx"><input class="form-input" id="plac-rendement" type="text" value="3,70"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div id="plac-legislation-row" style="display:none" class="form-group">
              <label class="form-label">Législation</label>
              <select class="form-select" id="plac-legislation">
                <option value="">Sélectionnez…</option>
                <option value="ab">Alberta</option>
                <option value="bc">Colombie-Britannique</option>
                <option value="pe">Île-du-Prince-Édouard</option>
                <option value="mb">Manitoba</option>
                <option value="nb">Nouveau-Brunswick</option>
                <option value="ns">Nouvelle-Écosse</option>
                <option value="nu">Nunavut</option>
                <option value="on">Ontario</option>
                <option value="qc">Québec</option>
                <option value="sk">Saskatchewan</option>
                <option value="nl">Terre-Neuve-et-Labrador</option>
                <option value="nt">Territoires du Nord-Ouest</option>
                <option value="yt">Yukon</option>
              </select>
            </div>
            <div id="plac-date-ouverture-row" style="display:none" class="form-group">
              <label class="form-label">Date d'ouverture <span style="color:var(--gold)">*</span></label>
              <input class="form-input" id="plac-date-ouverture" type="text" placeholder="Année (ex: 2023)" oninput="placDateOuvertureChange()"/>
            </div>
            <div class="modal-facultatif-title">Facultatif</div>
            <div class="form-group">
              <label class="form-label">Catégorie d'actif</label>
              <select class="form-select" id="plac-categorie">
                <option value="">Sélectionnez…</option>
                <option>Actions</option><option>Fonds communs de placement</option>
                <option>Fonds distincts</option><option>Obligations</option>
                <option>Placements garantis</option><option>Autre</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Institution</label>
              <select class="form-select" id="plac-institution"><option value="">Sélectionnez…</option></select>
            </div>
            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea class="form-input" id="plac-notes" rows="2" style="resize:vertical"></textarea>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closePlacementModal()">Annuler</button>
            <button class="btn btn-primary" id="plac-save-btn" onclick="savePlacement()">Enregistrer</button>
          </div>
        </div>
      </div>

      <!-- ── MODAL : Bien ── -->
      <div id="modal-bien" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:540px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="bien-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closeBienModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="form-group">
              <label class="form-label">Description</label>
              <input class="form-input" id="bien-description" type="text"/>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="bien-proprietaire" onchange="bienPropChange()"><option value="">Sélectionnez…</option></select>
              </div>
              <div class="col form-group">
                <label class="form-label">Valeur</label>
                <div class="input-sfx"><input class="form-input" id="bien-valeur" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
            </div>
            <div id="bien-parts-row" style="display:none" class="row">
              <div class="col form-group">
                <label class="form-label">Part de <span id="bien-part-label-client">client</span></label>
                <div class="input-sfx"><input class="form-input" id="bien-part-client" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Part de <span id="bien-part-label-conjoint">conjoint</span></label>
                <div class="input-sfx"><input class="form-input" id="bien-part-conjoint" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Coût d'acquisition</label>
                <div class="input-sfx"><input class="form-input" id="bien-cout" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Taux de croissance</label>
                <div class="input-sfx"><input class="form-input" id="bien-croissance" type="text" placeholder="0"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea class="form-input" id="bien-notes" rows="2" style="resize:vertical"></textarea>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeBienModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveBien()">Enregistrer</button>
          </div>
        </div>
      </div>

      <!-- ── MODAL : Passif ── -->
      <div id="modal-passif" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:580px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="pass-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closePassifModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Description</label>
                <input class="form-input" id="pass-description" type="text"/>
              </div>
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="pass-proprietaire" onchange="passifPropChange()"><option value="">Sélectionnez…</option></select>
              </div>
            </div>
            <div id="pass-parts-row" style="display:none" class="row">
              <div class="col form-group">
                <label class="form-label">Part de <span id="pass-part-label-client">client</span></label>
                <div class="input-sfx"><input class="form-input" id="pass-part-client" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Part de <span id="pass-part-label-conjoint">conjoint</span></label>
                <div class="input-sfx"><input class="form-input" id="pass-part-conjoint" type="number" min="0" max="100" step="0.01" value="50"/><span class="sfx">%</span></div>
              </div>
            </div>
            <!-- Section calcul -->
            <div style="border:1px solid var(--border);border-radius:8px;margin-bottom:16px;overflow:hidden">
              <div style="padding:10px 14px;background:#f8f9fd;font-size:12px;font-weight:700;color:var(--navy);border-bottom:1px solid var(--border)">Sélectionnez la valeur à calculer</div>
              <div style="padding:12px 14px">
                <div class="calc-tabs">
                  <button class="calc-tab active" onclick="setCalcType('solde',this)">Solde</button>
                  <button class="calc-tab" onclick="setCalcType('amortissement',this)">Amortissement</button>
                  <button class="calc-tab" onclick="setCalcType('taux',this)">Taux</button>
                  <button class="calc-tab" onclick="setCalcType('paiement',this)">Paiement</button>
                </div>
                <div class="row">
                  <div class="col form-group">
                    <label class="form-label">Solde</label>
                    <div class="input-sfx"><input class="form-input" id="pass-solde" type="text" placeholder="0"/><span class="sfx">$</span></div>
                  </div>
                  <div class="col form-group">
                    <label class="form-label">Amortissement</label>
                    <div style="display:flex;gap:6px">
                      <input class="form-input" id="pass-amort-val" type="text" placeholder="0" style="max-width:70px"/>
                      <select class="form-select" id="pass-amort-unit">
                        <option value="month" selected>Mois</option>
                        <option value="year">Années</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col form-group" style="margin-bottom:0">
                    <label class="form-label">Taux</label>
                    <div class="input-sfx"><input class="form-input" id="pass-taux" type="text" placeholder="0,00"/><span class="sfx">%</span></div>
                  </div>
                  <div class="col form-group" style="margin-bottom:0">
                    <label class="form-label">Paiement</label>
                    <div style="display:flex;gap:6px">
                      <div class="input-sfx" style="flex:1"><input class="form-input" id="pass-paiement" type="text" placeholder="0,00"/><span class="sfx">$</span></div>
                      <select class="form-select" id="pass-paiement-freq" style="max-width:130px">
                        <option value="monthly" selected>Mensuel</option>
                        <option value="yearly">Annuel</option>
                        <option value="biweekly">Aux deux semaines</option>
                        <option value="weekly">Hebdomadaire</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Date de renouvellement</label>
              <div style="display:flex;gap:8px">
                <select class="form-select" id="pass-renouvellement-mois" style="max-width:160px">
                  <option value="">Mois</option>
                  <option>Janvier</option><option>Février</option><option>Mars</option>
                  <option>Avril</option><option>Mai</option><option>Juin</option>
                  <option>Juillet</option><option>Août</option><option>Septembre</option>
                  <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                </select>
                <input class="form-input" id="pass-renouvellement-annee" type="text" placeholder="Année" style="max-width:90px"/>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Institution</label>
              <select class="form-select" id="pass-institution"><option value="">Sélectionnez…</option></select>
            </div>
            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea class="form-input" id="pass-notes" rows="2" style="resize:vertical"></textarea>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closePassifModal()">Annuler</button>
            <button class="btn btn-primary" onclick="savePassif()">Enregistrer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── PAGE: Revenu et épargne ── -->
    <div id="page-revenu-epargne" class="page">
      <div class="page-title">Revenu et épargne</div>
      <div style="display:flex;gap:20px;align-items:start">

        <!-- ── MAIN COLUMN ── -->
        <div style="flex:1;min-width:0">

          <!-- REVENU CARD -->
          <div class="card" style="overflow:visible">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
              <span>Revenu</span>
              <div id="revenu-add-wrap">
                <button class="btn btn-primary"
                  style="font-size:12px;padding:6px 14px;display:flex;align-items:center;gap:6px"
                  onclick="toggleRevenuDropdown()">
                  <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:3"><path d="M12 5v14M5 12h14"/></svg>
                  Ajouter un revenu
                  <svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:none;stroke:currentColor;stroke-width:3"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div id="revenu-dropdown">
                  <button class="dd-item" onclick="openRevenuModal('Revenu d\'emploi')">Emploi</button>
                  <button class="dd-item" onclick="openRevenuModal('Autre revenu')">Autre</button>
                </div>
              </div>
            </div>
            <table class="re-table">
              <thead>
                <tr>
                  <th>Propriétaire</th>
                  <th>Type</th>
                  <th>Description</th>
                  <th>Revenu brut</th>
                  <th>Fréquence</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="revenu-list">
                <tr data-revenu-annuel="9600" data-owner="client">
                  <td id="re-prefill-owner">WIGALIE</td>
                  <td>Autre</td>
                  <td>AIDE SOCIALE</td>
                  <td>800 $</td>
                  <td>Mensuelle</td>
                  <td class="col-action">
                    <button class="re-action-btn" title="Modifier" onclick="showToast('Modification non disponible dans la démo')">
                      <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button class="re-action-btn del" title="Supprimer" onclick="reDeleteRow(this)">
                      <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- ÉPARGNE CARD -->
          <div class="card" style="overflow:visible">
            <div class="card-header">Épargne</div>
            <!-- Empty state: shown when no actifs in actifs-list -->
            <div id="epargne-empty" class="card-body" style="text-align:center;padding:34px 20px">
              <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:none;stroke:var(--border);stroke-width:1.5;margin-bottom:10px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <div style="color:var(--muted);font-size:13px;font-weight:600">Aucun actif disponible</div>
              <div style="color:var(--muted);font-size:12px;margin-top:4px">Au moins un actif est requis pour ajouter une épargne.</div>
            </div>
            <!-- Tabs section: shown when actifs exist -->
            <div id="epargne-tabs-wrap" style="display:none">
              <div class="re-tab-bar">
                <button class="re-tab active" id="etab-client" onclick="switchEpargneTab('client',this)">—</button>
                <button class="re-tab" id="etab-conjoint" onclick="switchEpargneTab('conjoint',this)" style="display:none">—</button>
              </div>
              <!-- Client panel -->
              <div id="epanel-client" class="card-body" style="overflow:visible;padding-top:14px">
                <div style="position:relative;display:inline-block" id="ep-btn-client-wrap">
                  <button class="btn btn-primary btn-sm" onclick="toggleEpargneDropdown('client')">
                    <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                    Ajouter une épargne
                  </button>
                  <div id="ep-dd-client" style="display:none;position:fixed;top:0;left:0;z-index:9999;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:240px;overflow:hidden">
                    <ul id="ep-dd-client-list" style="list-style:none;padding:4px 0;margin:0"></ul>
                  </div>
                </div>
                <div id="ep-list-client" style="margin-top:10px"></div>
              </div>
              <!-- Conjoint panel -->
              <div id="epanel-conjoint" class="card-body" style="display:none;overflow:visible;padding-top:14px">
                <div style="position:relative;display:inline-block" id="ep-btn-conjoint-wrap">
                  <button class="btn btn-primary btn-sm" onclick="toggleEpargneDropdown('conjoint')">
                    <svg viewBox="0 0 26 24" width="14" height="14" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
                    Ajouter une épargne
                  </button>
                  <div id="ep-dd-conjoint" style="display:none;position:fixed;top:0;left:0;z-index:9999;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:240px;overflow:hidden">
                    <ul id="ep-dd-conjoint-list" style="list-style:none;padding:4px 0;margin:0"></ul>
                  </div>
                </div>
                <div id="ep-list-conjoint" style="margin-top:10px"></div>
              </div>
            </div>
          </div>

          <!-- DROITS DE COTISATION CARD -->
          <div class="card" style="overflow:visible">
            <div class="card-header" style="display:flex;align-items:center;gap:8px">
              Droits de cotisation
              <span class="info-tooltip-wrap">
                <span class="info-tooltip-icon">i</span>
                <span class="info-tooltip-bubble">
                  Votre client peut déterminer ses droits REER ou CELI inutilisés en accédant à son compte en ligne de l'Agence du revenu du Canada (Mon dossier ARC). Les droits REER inutilisés se retrouvent également sur son dernier avis de cotisation fédéral. Vous trouverez les détails de vos droits de participation à un CELIAPP sur votre avis de cotisation ou de nouvelle cotisation.
                </span>
              </span>
            </div>
            <table class="re-table">
              <thead>
                <tr>
                  <th style="width:55%"></th>
                  <th id="dc-client-col">WIGALIE</th>
                  <th id="dc-conjoint-col" style="display:none"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Droits REER/RPAC inutilisés</td>
                  <td>
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-client-reer" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                  <td id="dc-conjoint-reer-cell" style="display:none">
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-conjoint-reer" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Droits CELI inutilisés</td>
                  <td>
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-client-celi" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                  <td id="dc-conjoint-celi-cell" style="display:none">
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-conjoint-celi" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Droits CELIAPP inutilisés</td>
                  <td>
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-client-celiapp" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                  <td id="dc-conjoint-celiapp-cell" style="display:none">
                    <div class="dc-input-cell">
                      <div class="input-sfx" style="flex:1">
                        <input class="form-input" id="dc-conjoint-celiapp" type="text" placeholder="0"/>
                        <span class="sfx">$</span>
                      </div>
                      <button class="re-sync-btn" title="Synchroniser" onclick="showToast('Synchronisation non disponible dans la démo')">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div><!-- /main column -->

        <!-- ── RE SIDEBAR (inline sticky) ── -->
        <div id="re-sidebar">
          <div class="card">
            <div class="ap-sidebar-section">
              <div class="ap-sb-total">Flux monétaire</div>
              <div class="calc-tabs" style="margin-top:10px">
                <button class="calc-tab active" id="re-tab-annuel" onclick="setReTab('annuel',this)">Annuel</button>
                <button class="calc-tab" id="re-tab-mensuel" onclick="setReTab('mensuel',this)">Mensuel</button>
              </div>
            </div>
            <!-- Client block -->
            <div class="ap-sidebar-section" id="re-client-block">
              <div style="margin-bottom:10px">
                <div style="font-size:12px;font-weight:700;color:var(--navy)" id="re-client-name">WIGALIE</div>
              </div>
              <!-- Donut placeholder -->
              <div class="re-donut-wrap">
                <svg id="re-client-donut" width="90" height="90" viewBox="0 0 90 90">
                  <circle cx="45" cy="45" r="32" fill="none" stroke="#e5e7ef" stroke-width="14"/>
                  <circle id="re-client-donut-arc" cx="45" cy="45" r="32" fill="none"
                    stroke="var(--gold)" stroke-width="14"
                    stroke-dasharray="201" stroke-dashoffset="201"
                    transform="rotate(-90 45 45)" style="transition:stroke-dashoffset .4s"/>
                </svg>
                <div style="font-size:18px;font-weight:800;color:var(--navy);margin-top:4px" id="re-client-total-label">0 $</div>
                <div style="font-size:11px;color:var(--muted)" id="re-client-freq-label">annuel</div>
              </div>
              <!-- Legend rows -->
              <div style="margin-top:10px">
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--gold);flex-shrink:0"></span>
                    Revenu brut
                  </span>
                  <span class="ap-sb-val" id="re-client-revenu">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#f97316;flex-shrink:0"></span>
                    Impôt estimé
                  </span>
                  <span class="ap-sb-val" id="re-client-impot">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px;border-top:1px solid var(--border);padding-top:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--navy)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--navy);flex-shrink:0"></span>
                    Revenu net
                  </span>
                  <span class="ap-sb-val" style="font-weight:700;color:var(--navy)" id="re-client-net">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                    Épargne
                  </span>
                  <span class="ap-sb-val" id="re-client-epargne">0 $</span>
                </div>
                <div class="ap-sb-row">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
                    Dépenses
                  </span>
                  <span class="ap-sb-val" id="re-client-depenses">0 $</span>
                </div>
              </div>
            </div>
            <!-- Conjoint block -->
            <div class="ap-sidebar-section" id="re-conjoint-block" style="display:none">
              <div style="margin-bottom:10px">
                <div style="font-size:12px;font-weight:700;color:var(--navy)" id="re-conjoint-name">Conjoint(e)</div>
              </div>
              <div class="re-donut-wrap">
                <svg width="90" height="90" viewBox="0 0 90 90">
                  <circle cx="45" cy="45" r="32" fill="none" stroke="#e5e7ef" stroke-width="14"/>
                  <circle id="re-conjoint-donut-arc" cx="45" cy="45" r="32" fill="none"
                    stroke="var(--navy)" stroke-width="14"
                    stroke-dasharray="201" stroke-dashoffset="201"
                    transform="rotate(-90 45 45)" style="transition:stroke-dashoffset .4s"/>
                </svg>
                <div style="font-size:18px;font-weight:800;color:var(--navy);margin-top:4px" id="re-conjoint-total-label">0 $</div>
                <div style="font-size:11px;color:var(--muted)" id="re-conjoint-freq-label">annuel</div>
              </div>
              <div style="margin-top:10px">
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--navy);flex-shrink:0"></span>
                    Revenu brut
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-revenu">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#f97316;flex-shrink:0"></span>
                    Impôt estimé
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-impot">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px;border-top:1px solid var(--border);padding-top:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--navy)">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--gold);flex-shrink:0"></span>
                    Revenu net
                  </span>
                  <span class="ap-sb-val" style="font-weight:700;color:var(--navy)" id="re-conjoint-net">0 $</span>
                </div>
                <div class="ap-sb-row" style="margin-bottom:4px">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>
                    Épargne
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-epargne">0 $</span>
                </div>
                <div class="ap-sb-row">
                  <span style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted)">
                    <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>
                    Dépenses
                  </span>
                  <span class="ap-sb-val" id="re-conjoint-depenses">0 $</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /re-sidebar -->

      </div><!-- /flex -->

      <!-- ── MODAL : Revenu ── -->
      <div id="modal-revenu" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="revenu-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Revenu d'emploi</h4>
            <button onclick="closeRevenuModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:72vh;overflow-y:auto">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Propriétaire</label>
                <select class="form-select" id="revenu-proprietaire"><option value="">Sélectionnez…</option></select>
              </div>
              <div class="col form-group">
                <label class="form-label">Revenu annuel brut</label>
                <div class="input-sfx">
                  <input class="form-input" id="revenu-montant" type="text" placeholder="0"/>
                  <span class="sfx">$</span>
                </div>
              </div>
            </div>
            <!-- Emploi-specific fields -->
            <div id="revenu-emploi-fields">
              <div class="form-group">
                <label class="form-label">Profession principale</label>
                <input class="form-input" id="revenu-profession" type="text" placeholder="Ex : Infirmière, Technicien…"/>
              </div>
              <div class="form-group">
                <label class="form-label">Employeur</label>
                <input class="form-input" id="revenu-employeur" type="text" placeholder="Nom de l'employeur"/>
              </div>
              <div class="form-group">
                <label class="form-label">Date d'embauche</label>
                <div style="display:flex;gap:8px">
                  <select class="form-select" id="revenu-embauche-mois" style="max-width:160px">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" id="revenu-embauche-annee" type="text" placeholder="Année" style="max-width:90px"/>
                </div>
              </div>
            </div>
            <!-- Autre-specific fields -->
            <div id="revenu-autre-fields" style="display:none">
              <div class="form-group">
                <label class="form-label">Description</label>
                <input class="form-input" id="revenu-description" type="text" placeholder="Ex : Aide sociale, Pension alimentaire…"/>
              </div>
              <div class="row">
                <div class="col form-group">
                  <label class="form-label">Fréquence</label>
                  <select class="form-select" id="revenu-frequence">
                    <option value="onetime">Une fois</option>
                    <option value="52">Hebdomadaire</option>
                    <option value="26">Aux deux semaines</option>
                    <option value="12" selected>Mensuelle</option>
                    <option value="1">Annuelle</option>
                  </select>
                </div>
                <div class="col form-group">
                  <label class="form-label">Portion imposable</label>
                  <div class="input-sfx"><input class="form-input" id="revenu-portion-imposable" type="text" value="100,00"/><span class="sfx">%</span></div>
                </div>
              </div>
              <div class="row">
                <div class="col form-group">
                  <label class="form-label">Indexé à l'inflation</label>
                  <div style="display:flex;gap:8px;margin-top:4px">
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                      <input type="radio" name="revenu-indexe" id="revenu-indexe-oui" value="yes"> Oui
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                      <input type="radio" name="revenu-indexe" id="revenu-indexe-non" value="no" checked> Non
                    </label>
                  </div>
                </div>
                <div class="col form-group">
                  <label class="form-label">Taux d'indexation supplémentaire</label>
                  <div class="input-sfx"><input class="form-input" id="revenu-taux-indexation" type="text" value="0,00"/><span class="sfx">%</span></div>
                </div>
              </div>
              <div class="row">
                <div class="col form-group">
                  <label class="form-label">Début</label>
                  <div style="display:flex;gap:6px">
                    <select class="form-select" id="revenu-debut-mois" style="max-width:140px">
                      <option value="">Mois</option>
                      <option>Janvier</option><option>Février</option><option>Mars</option>
                      <option>Avril</option><option>Mai</option><option>Juin</option>
                      <option>Juillet</option><option>Août</option><option>Septembre</option>
                      <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                    </select>
                    <input class="form-input" id="revenu-debut-annee" type="text" placeholder="Année" style="max-width:80px"/>
                  </div>
                </div>
                <div class="col form-group">
                  <label class="form-label">Fin</label>
                  <select class="form-select" id="revenu-fin-type">
                    <option value="retirement">Retraite</option>
                    <option value="death">Décès</option>
                    <option value="age">Âge</option>
                    <option value="date">Date</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Épargner le revenu dans un placement non enregistré</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="revenu-autosave" id="revenu-autosave-oui" value="yes"> Oui
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="revenu-autosave" id="revenu-autosave-non" value="no" checked> Non
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeRevenuModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveRevenu()">Enregistrer</button>
          </div>
        </div>
      </div>

      <!-- Modal : Épargne -->
      <div id="modal-epargne" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
        <div style="background:white;border-radius:12px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
          <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h4 id="ep-modal-title" style="font-size:16px;font-weight:700;color:var(--navy);margin:0"></h4>
            <button onclick="closeEpargneModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
          </div>
          <div style="padding:20px 24px;max-height:70vh;overflow-y:auto">
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Montant</label>
                <div class="input-sfx"><input class="form-input" id="ep-montant" type="text" placeholder="0"/><span class="sfx">$</span></div>
              </div>
              <div class="col form-group">
                <label class="form-label">Fréquence</label>
                <select class="form-select" id="ep-frequence">
                  <option value="onetime">Une fois</option>
                  <option value="52">Hebdomadaire</option>
                  <option value="26">Aux deux semaines</option>
                  <option value="24">Bi-mensuelle</option>
                  <option value="12" selected>Mensuel</option>
                  <option value="1">Annuel</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Indexé à l'inflation</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="ep-indexe" id="ep-indexe-oui" value="yes"> Oui
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                    <input type="radio" name="ep-indexe" id="ep-indexe-non" value="no" checked> Non
                  </label>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Taux d'indexation supplémentaire</label>
                <div class="input-sfx"><input class="form-input" id="ep-taux-indexation" type="text" value="0,00"/><span class="sfx">%</span></div>
              </div>
            </div>
            <div class="row">
              <div class="col form-group">
                <label class="form-label">Début</label>
                <div style="display:flex;gap:6px">
                  <select class="form-select" id="ep-debut-mois" style="max-width:140px">
                    <option value="">Mois</option>
                    <option>Janvier</option><option>Février</option><option>Mars</option>
                    <option>Avril</option><option>Mai</option><option>Juin</option>
                    <option>Juillet</option><option>Août</option><option>Septembre</option>
                    <option>Octobre</option><option>Novembre</option><option>Décembre</option>
                  </select>
                  <input class="form-input" id="ep-debut-annee" type="text" placeholder="Année" style="max-width:80px"/>
                </div>
              </div>
              <div class="col form-group">
                <label class="form-label">Fin</label>
                <select class="form-select" id="ep-fin-type">
                  <option value="retirement">Retraite</option>
                  <option value="death">Décès</option>
                  <option value="age">Âge</option>
                  <option value="date">Date</option>
                </select>
              </div>
            </div>
          </div>
          <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
            <button class="btn btn-secondary" onclick="closeEpargneModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveEpargne()">Enregistrer</button>
          </div>
        </div>
      </div>
    </div><!-- /page-revenu-epargne -->

    <!-- ── PAGE: Fonds d'urgence ── -->
    <div id="page-fonds-urgence" class="page">
      <div class="page-title">Fonds d'urgence</div>

      <div style="display:flex;gap:20px;align-items:start">
        <!-- Colonne gauche -->
        <div style="flex:1;min-width:0">

          <!-- Section Objectif -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Objectif</div>
            <div class="card-body">
              <!-- Radio type -->
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="income" checked onchange="fuTypeChange()"/> Revenu mensuel</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="expenses" onchange="fuTypeChange()"/> Dépenses mensuelles</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="amount" onchange="fuTypeChange()"/> Montant fixe</label>
                <label class="fu-radio-pill"><input type="radio" name="fu-type" value="none" onchange="fuTypeChange()"/> Aucun</label>
              </div>

              <!-- Revenu mensuel: mois seulement -->
              <div id="fu-row-income" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <input class="form-input" id="fu-months" type="text" value="3" style="width:60px;text-align:center" oninput="fuCalc()"/>
                <span style="font-size:13px;color:var(--text)">mois de revenu familial net, correspondant à <strong id="fu-montant-cible-income">0 $</strong></span>
              </div>

              <!-- Dépenses mensuelles: montant + mois -->
              <div id="fu-row-expenses" style="display:none;align-items:center;gap:10px;flex-wrap:wrap">
                <div class="input-sfx" style="max-width:160px">
                  <input class="form-input" id="fu-dep-mensuel" type="text" placeholder="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
                <span style="font-size:13px;color:var(--muted)">/mois ×</span>
                <input class="form-input" id="fu-months-dep" type="text" value="3" style="width:60px;text-align:center" oninput="fuCalc()"/>
                <span style="font-size:13px;color:var(--text)">mois, correspondant à <strong id="fu-montant-cible-dep">0 $</strong></span>
              </div>

              <!-- Montant fixe -->
              <div id="fu-row-amount" style="display:none;align-items:center;gap:10px">
                <div class="input-sfx" style="max-width:180px">
                  <input class="form-input" id="fu-montant-fixe" type="text" placeholder="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Section Actifs alloués -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Actifs alloués au fonds d'urgence</div>
            <div class="card-body" id="fu-actifs-body"></div>
          </div>

          <!-- Section Marge de crédit -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Marge de crédit</div>
            <div class="card-body">
              <div class="form-group" style="max-width:220px">
                <label class="form-label">Montant disponible</label>
                <div class="input-sfx">
                  <input class="form-input" id="fu-marge" type="text" value="0" oninput="fuCalc()"/>
                  <span class="sfx">$</span>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /col gauche -->

        <!-- Colonne droite: Résumé sticky -->
        <div style="width:260px;flex-shrink:0;position:sticky;top:80px">
          <div class="card" id="fu-resume-card">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Résumé</div>
            <div class="card-body" style="padding:0">
              <table style="width:100%;border-collapse:collapse;font-size:13px">
                <tr style="border-bottom:1px solid var(--border)">
                  <td style="padding:10px 14px;color:var(--muted)">Objectif</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:600" id="fu-r-objectif">—</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                  <td style="padding:10px 14px;color:var(--muted)">Actifs alloués</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:600" id="fu-r-actifs">0 $</td>
                </tr>
                <tr style="border-bottom:1px solid var(--border)">
                  <td style="padding:10px 14px;color:var(--muted)">Marge de crédit</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:600" id="fu-r-marge">0 $</td>
                </tr>
                <tr>
                  <td style="padding:10px 14px;font-weight:700">Écart</td>
                  <td style="padding:10px 14px;text-align:right;font-weight:700;font-size:15px" id="fu-r-ecart">—</td>
                </tr>
              </table>
              <!-- Barre de couverture -->
              <div style="padding:12px 14px;border-top:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:6px">
                  <span>Couverture</span><span id="fu-r-pct">0 %</span>
                </div>
                <div style="height:10px;background:#e5e7eb;border-radius:5px;overflow:hidden">
                  <div id="fu-r-bar" style="height:100%;width:0%;border-radius:5px;transition:width .4s,background .4s"></div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /col droite -->

      </div><!-- /flex row -->
    </div><!-- /page-fonds-urgence -->

<!-- ── PAGE: Décès ── -->
<div id="page-deces" class="page">
  <div class="page-title">Protection en cas de décès</div>
  <div style="display:flex;gap:20px;align-items:start">

    <!-- Colonne gauche -->
    <div style="flex:1;min-width:0">

      <!-- Assurance vie -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          Assurance vie
          <button class="btn btn-primary btn-sm" onclick="openDecesAvModal()">+ Ajouter</button>
        </div>
        <div class="card-body" id="deces-av-list" style="padding:0">
          <p style="padding:14px;font-size:13px;color:var(--muted);margin:0" id="deces-av-empty">Aucune assurance vie enregistrée.</p>
        </div>
      </div>

      <!-- Prestation de décès RRQ/RPC -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">
          <span style="display:flex;align-items:center;gap:6px">
            Prestation de décès RRQ/RPC
            <span class="abf-tooltip-wrap">
              <span class="abf-tooltip-icon">&#9432;</span>
              <span class="abf-tooltip-box">La prestation par défaut est la prestation maximale et doit être ajustée à partir du relevé du Régime de rentes du Québec (RRQ) ou du Régime de pensions du Canada (RPC).</span>
            </span>
          </span>
        </div>
        <div class="card-body" id="deces-rrq-body">
          <!-- Rempli par decesInit() -->
        </div>
      </div>

      <!-- Actifs à liquider -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Actifs à liquider en cas de décès</div>
        <div class="card-body" id="deces-actifs-body"></div>
      </div>

      <!-- Passifs à rembourser -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Passifs à rembourser en cas de décès</div>
        <div class="card-body" id="deces-passifs-body"></div>
      </div>

      <!-- Dépenses prévues -->
      <div class="card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)" id="deces-dep-header">
          Dépenses prévues si [client] décède
        </div>
        <div class="card-body" style="padding-top:0">
          <!-- Tabs (couple seulement) -->
          <div id="deces-dep-tabs" style="display:none;border-bottom:1px solid var(--border);margin-bottom:12px;display:none">
            <button class="deces-person-tab active" id="deces-dep-tab-client" onclick="switchDecesDepTab('client',this)">CLIENT</button>
            <button class="deces-person-tab" id="deces-dep-tab-conjoint" onclick="switchDecesDepTab('conjoint',this)">CONJOINT</button>
          </div>
          <div id="deces-dep-list" style="margin-top:12px">
            <!-- pré-rempli par decesInit() avec Frais funéraires 25 000 $ -->
          </div>
          <div id="deces-dep-list-conjoint" style="display:none;margin-top:12px"></div>
          <div style="position:relative;margin-top:10px">
            <button class="btn btn-primary btn-sm" onclick="toggleDecesDep()">+ Ajouter une dépense</button>
            <div id="deces-dep-dd" style="display:none;position:fixed;z-index:9999;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.15);padding:4px 0;min-width:200px">
              <div class="deces-dep-item" onclick="addDecesDep('Frais funéraires',25000)">Frais funéraires</div>
              <div class="deces-dep-item" onclick="addDecesDep('Fonds d\'urgence',0)">Fonds d'urgence</div>
              <div class="deces-dep-item" onclick="addDecesDep('Héritage',0)">Héritage</div>
              <div class="deces-dep-item" onclick="addDecesDep('Impôts',0)">Impôts</div>
              <div class="deces-dep-item" onclick="addDecesDep('Dons',0)">Dons</div>
              <div class="deces-dep-item" onclick="addDecesDep('Frais juridiques',0)">Frais juridiques</div>
              <div class="deces-dep-item" onclick="addDecesDep('Autre',0)">Autre</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Remplacement du revenu -->
      <div class="card" id="deces-rr-card" style="margin-bottom:16px">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Remplacement du revenu en cas de décès</div>
        <div class="card-body">
          <!-- Type / Brut-Net / Annuel-Mensuel -->
          <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px">
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">TYPE</div>
              <div style="display:flex;gap:6px" id="deces-rr-type-group">
                <label class="fu-radio-pill" id="deces-rr-familial-pill" style="display:none"><input type="radio" name="deces-rr-type" value="familial" onchange="decesCalc()"/> Familial</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-type" value="individuel" checked onchange="decesCalc()"/> Individuel</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-type" value="aucun" onchange="decesCalc()"/> Aucun</label>
              </div>
            </div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">REVENU</div>
              <div style="display:flex;gap:6px">
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-brutnnet" value="brut" checked onchange="decesCalc()"/> Brut</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-brutnnet" value="net" onchange="decesCalc()"/> Net</label>
              </div>
            </div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">FRÉQUENCE</div>
              <div style="display:flex;gap:6px">
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-freq" value="annuel" checked onchange="decesCalc()"/> Annuel</label>
                <label class="fu-radio-pill"><input type="radio" name="deces-rr-freq" value="mensuel" onchange="decesCalc()"/> Mensuel</label>
              </div>
            </div>
          </div>

          <!-- Person tabs for income replacement (couple seulement) -->
          <div id="deces-rr-person-tabs" style="display:none;margin:0 -20px 16px;padding:0 20px;border-bottom:1px solid var(--border)">
            <button class="deces-rr-person-tab active" id="deces-rr-tab-client" onclick="switchDecesRrTab('c',this)">CLIENT</button>
            <button class="deces-rr-person-tab" id="deces-rr-tab-conjoint" onclick="switchDecesRrTab('j',this)">CONJOINT</button>
          </div>

          <div id="deces-rr-form">
            <!-- ── Panel Client ───────────────────────────────── -->
            <div id="deces-rr-panel-c">
              <div id="deces-rr-panel-c-title" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--navy);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>
              <div style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus actuels</div>
              <div id="deces-revenus-table-c" style="margin-bottom:16px;background:#f8f9fd;border-radius:6px;padding:10px 14px;font-size:13px"></div>

              <div style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus visés</div>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px;font-size:13px">
                <span id="deces-rr-beneficiaire-label-c">Le bénéficiaire désire recevoir</span>
                <input class="form-input" id="deces-rr-pct-c" type="text" value="70" style="width:80px;text-align:center" oninput="decesCalc()"/>
                <div style="display:flex;gap:4px">
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-c" value="pct" checked onchange="decesCalc()"/> %</label>
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-c" value="montant" onchange="decesCalc()"/> $</label>
                </div>
                <span><span id="deces-rr-du-revenu-c">du revenu</span>, soit <strong id="deces-rr-vise-label-c">0 $</strong> pendant</span>
                <div class="input-sfx" style="max-width:100px">
                  <input class="form-input" id="deces-rr-duree-c" type="text" value="10" oninput="decesCalc()"/>
                  <span class="sfx" style="font-size:12px">ans</span>
                </div>
              </div>

              <div style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus disponibles</div>
              <div style="background:#f8f9fd;border-radius:6px;padding:10px 14px;margin-bottom:16px">
                <div id="deces-revenu-dispo-auto-c"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px">
                  <span>Autres revenus</span>
                  <div class="input-sfx" style="max-width:130px"><input class="form-input" id="deces-autres-revenus-c" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
                </div>
              </div>

              <div style="font-size:13px">
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu annuel manquant</span>
                  <strong id="deces-rr-manquant-c">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu manquant projeté <span id="deces-rr-projete-duree-c" style="font-size:11px"></span></span>
                  <strong id="deces-rr-projete-c">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0">
                  <span style="color:var(--muted)">Rendement</span>
                  <div class="input-sfx" style="max-width:100px"><input class="form-input" id="deces-rr-taux-c" type="text" value="3.70" oninput="decesCalc()"/><span class="sfx">%</span></div>
                </div>
              </div>
            </div>

            <!-- ── Panel Conjoint (hidden until couple mode) ─── -->
            <div id="deces-rr-panel-j" style="display:none">
              <div id="deces-rr-panel-j-title" style="display:none;font-size:13px;font-weight:700;color:white;background:var(--gold);border-radius:6px;padding:7px 12px;margin-bottom:12px;text-align:center"></div>
              <div id="deces-lbl-j-actuels" style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus actuels</div>
              <div id="deces-revenus-table-j" style="margin-bottom:16px;background:#f8f9fd;border-radius:6px;padding:10px 14px;font-size:13px"></div>

              <div id="deces-lbl-j-vises" style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus visés</div>
              <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px;font-size:13px">
                <span id="deces-rr-beneficiaire-label-j">Le bénéficiaire désire recevoir</span>
                <input class="form-input" id="deces-rr-pct-j" type="text" value="70" style="width:80px;text-align:center" oninput="decesCalc()"/>
                <div style="display:flex;gap:4px">
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-j" value="pct" checked onchange="decesCalc()"/> %</label>
                  <label class="fu-radio-pill" style="padding:5px 10px;font-size:12px"><input type="radio" name="deces-rr-target-j" value="montant" onchange="decesCalc()"/> $</label>
                </div>
                <span><span id="deces-rr-du-revenu-j">du revenu</span>, soit <strong id="deces-rr-vise-label-j">0 $</strong> pendant</span>
                <div class="input-sfx" style="max-width:100px">
                  <input class="form-input" id="deces-rr-duree-j" type="text" value="10" oninput="decesCalc()"/>
                  <span class="sfx" style="font-size:12px">ans</span>
                </div>
              </div>

              <div id="deces-lbl-j-dispos" style="font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase">Revenus disponibles</div>
              <div style="background:#f8f9fd;border-radius:6px;padding:10px 14px;margin-bottom:16px">
                <div id="deces-revenu-dispo-auto-j"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13px">
                  <span>Autres revenus</span>
                  <div class="input-sfx" style="max-width:130px"><input class="form-input" id="deces-autres-revenus-j" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
                </div>
              </div>

              <div style="font-size:13px">
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu annuel manquant</span>
                  <strong id="deces-rr-manquant-j">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border)">
                  <span style="color:var(--muted)">Revenu manquant projeté <span id="deces-rr-projete-duree-j" style="font-size:11px"></span></span>
                  <strong id="deces-rr-projete-j">0 $</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0">
                  <span style="color:var(--muted)">Rendement</span>
                  <div class="input-sfx" style="max-width:100px"><input class="form-input" id="deces-rr-taux-j" type="text" value="3.70" oninput="decesCalc()"/><span class="sfx">%</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /col gauche -->

    <!-- Colonne droite: Résumé sticky -->
    <div style="width:300px;flex-shrink:0;position:sticky;top:80px">
      <div class="card">
        <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Résumé</div>
        <div id="deces-resume-body" style="padding:16px 14px"></div>
      </div>
    </div>

  </div>
</div><!-- /page-deces -->

<!-- Modal: Assurance vie -->
<div id="modal-deces-av" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;width:560px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Assurance vie</h4>
      <button onclick="closeDecesAvModal()" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">×</button>
    </div>
    <div style="padding:20px 24px">
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Type</label>
          <select class="form-select" id="deces-av-type">
            <option value="">Sélectionnez...</option>
            <option value="Collective">Collective</option>
            <option value="Temporaire">Temporaire</option>
            <option value="Entière">Entière</option>
            <option value="Universelle">Universelle</option>
            <option value="Avec participation">Avec participation</option>
          </select>
        </div>
        <div class="col form-group">
          <label class="form-label">Assuré</label>
          <select class="form-select" id="deces-av-owner">
            <option value="">Sélectionnez...</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Montant assuré</label>
          <div class="input-sfx"><input class="form-input" id="deces-av-montant" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
        <div class="col form-group">
          <label class="form-label">Prime annuelle</label>
          <div class="input-sfx"><input class="form-input" id="deces-av-prime" type="text" placeholder="0"/><span class="sfx">$</span></div>
        </div>
      </div>
      <div class="row">
        <div class="col form-group">
          <label class="form-label">Assureur</label>
          <select class="form-select" id="deces-av-assureur">
            <option value="">Sélectionnez...</option>
            <option>Assomption vie</option><option>Banque Laurentienne</option><option>Banque Nationale</option>
            <option>Beneva</option><option>BMO Assurance</option><option>Canada Vie (Great West, London Life)</option>
            <option>Chevaliers de Colomb</option><option>CIBC</option><option>Desjardins Assurances</option>
            <option>Empire Vie</option><option>Financière Sun Life</option><option>Foresters</option>
            <option>Humania</option><option>iA Groupe financier</option>
            <option>iA Groupe financier (anciennement L'Excellence)</option>
            <option>Ivari</option><option>La Capitale</option><option>La Croix Bleue</option>
            <option>Manuvie (Standard Life, First National)</option><option>Médic Construction</option>
            <option>Primerica</option><option>RBC Assurances</option><option>SSQ Assurance</option>
            <option>Tangerine</option><option>TD</option><option>Transamerica</option>
            <option>Union Vie</option><option>Autre</option>
          </select>
        </div>
        <div class="col form-group">
          <label class="form-label">Date d'émission</label>
          <input class="form-input" id="deces-av-date" type="text" placeholder="AAAA-MM-JJ"/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Bénéficiaires</label>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="conjoint"/> Conjoint</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="enfants"/> Enfants</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="succession"/> Succession</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="parents"/> Parents</label>
          <label class="fu-radio-pill"><input type="radio" name="deces-av-benef" value="autre"/> Autre</label>
        </div>
      </div>
      <div class="form-group" style="margin-top:8px">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
          <input type="checkbox" id="deces-av-exclure"/> Exclure de l'analyse décès
        </label>
      </div>
      <div class="form-group">
        <label class="form-label">Notes</label>
        <textarea class="form-input" id="deces-av-notes" rows="3" style="resize:vertical"></textarea>
      </div>
    </div>
    <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
      <button class="btn btn-secondary" onclick="closeDecesAvModal()">Annuler</button>
      <button class="btn btn-primary" onclick="saveDecesAv()">Enregistrer</button>
    </div>
  </div>
</div>

    <div id="page-invalidite" class="page">
      <div class="page-title">Invalidité</div>
      <div class="page-subtitle">Analyse des besoins en cas d'invalidité</div>
      <div style="display:flex;gap:20px;align-items:start">

        <!-- Colonne gauche -->
        <div style="flex:1;min-width:0">

          <!-- Assurance invalidité -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
              Assurance invalidité
              <button class="btn btn-primary btn-sm" onclick="openInvalAvModal()">+ Ajouter</button>
            </div>
            <div class="card-body" id="inval-av-list" style="padding:0">
              <p style="padding:14px;font-size:13px;color:var(--muted);margin:0">Aucune assurance invalidité enregistrée.</p>
            </div>
          </div>

          <!-- Autres sources de revenu -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Autres sources de revenu</div>
            <div class="card-body">
              <div id="inval-autres-revenus-rows"></div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Êtes-vous couvert par l'assurance-emploi?</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label class="fu-radio-pill"><input type="radio" name="inval-ae" value="oui" onchange="invaliditeCalc()"/> Oui</label>
                  <label class="fu-radio-pill"><input type="radio" name="inval-ae" value="non" checked onchange="invaliditeCalc()"/> Non</label>
                </div>
              </div>
            </div>
          </div>

          <!-- Approche de calcul -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Approche de calcul</div>
            <div class="card-body">
              <div style="display:flex;gap:8px;flex-wrap:wrap">
                <label class="fu-radio-pill"><input type="radio" name="inval-approche" value="remplacement" checked onchange="invaliditeApproche()"/> Remplacement du revenu</label>
                <label class="fu-radio-pill"><input type="radio" name="inval-approche" value="depenses" onchange="invaliditeApproche()"/> Dépenses courantes</label>
              </div>
            </div>
          </div>

          <!-- Remplacement du revenu -->
          <div id="inval-rr-section" class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
              <span>Remplacement du revenu en cas d'invalidité</span>
              <div style="display:flex;gap:2px">
                <button id="inval-bn-brut" class="toggle-btn active" onclick="setInvalBrutNet('brut')">Brut</button>
                <button id="inval-bn-net" class="toggle-btn" onclick="setInvalBrutNet('net')">Net</button>
              </div>
            </div>
            <div class="card-body" id="inval-rr-body"></div>
          </div>

          <!-- Dépenses courantes -->
          <div id="inval-dep-section" class="card" style="margin-bottom:16px;display:none">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Dépenses courantes mensuelles</div>
            <div class="card-body">
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Total des dépenses mensuelles</label>
                <div class="input-sfx" style="max-width:200px"><input class="form-input" id="inval-dep-total" type="text" placeholder="0" oninput="invaliditeCalc()"/><span class="sfx">$/mois</span></div>
              </div>
            </div>
          </div>

          <!-- Informations supplémentaires -->
          <div class="card" style="margin-bottom:16px">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:space-between" onclick="toggleInvalInfo()">
              <span>Informations supplémentaires <span style="color:var(--muted);font-weight:400;font-size:12px">(facultatif)</span></span>
              <span id="inval-info-chevron" style="font-size:16px;color:var(--muted);transition:transform .2s">▼</span>
            </div>
            <div class="card-body" id="inval-info-body" style="display:none">
              <div class="form-group">
                <label class="form-label">Niveau de travail</label>
                <div style="display:flex;align-items:center;gap:12px;margin-top:4px">
                  <span style="font-size:12px;color:var(--muted);white-space:nowrap">Physique</span>
                  <input type="range" id="inval-travail-slider" min="0" max="10" value="5" style="flex:1;accent-color:var(--navy)"/>
                  <span style="font-size:12px;color:var(--muted);white-space:nowrap">Administratif</span>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Nombre d'heures travaillées</label>
                <div style="display:flex;gap:8px">
                  <input class="form-input" id="inval-heures-val" type="text" placeholder="40" style="max-width:80px"/>
                  <select class="form-select" id="inval-heures-freq">
                    <option value="semaine" selected>Par semaine</option>
                    <option value="mois">Par mois</option>
                    <option value="annee">Par année</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Exercez-vous un sport ou un loisir à risque?</label>
                <div style="display:flex;gap:8px;margin-top:4px">
                  <label class="fu-radio-pill"><input type="radio" name="inval-sport" value="oui"/> Oui</label>
                  <label class="fu-radio-pill"><input type="radio" name="inval-sport" value="non" checked/> Non</label>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Délai de carence souhaité</label>
                <div style="display:flex;gap:8px">
                  <input class="form-input" id="inval-carence-val" type="text" placeholder="90" style="max-width:80px"/>
                  <select class="form-select" id="inval-carence-unit">
                    <option value="jours" selected>Jours</option>
                    <option value="semaines">Semaines</option>
                    <option value="mois">Mois</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Période de couverture souhaitée</label>
                <div style="display:flex;gap:8px">
                  <select class="form-select" id="inval-couverture-type" style="max-width:180px">
                    <option value="semaines">Semaines</option>
                    <option value="annees" selected>Années</option>
                    <option value="age">Âge maximum</option>
                  </select>
                  <input class="form-input" id="inval-couverture-val" type="text" placeholder="2" style="max-width:80px"/>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /col gauche -->

        <!-- Résumé sidebar -->
        <div style="width:300px;flex-shrink:0;position:sticky;top:80px">
          <div class="card">
            <div class="card-header" style="font-weight:700;font-size:13px;padding:12px 16px;border-bottom:1px solid var(--border)">Résumé</div>
            <div id="inval-resume-body" style="padding:16px 14px;font-size:13px;color:var(--muted)">Complétez les informations pour voir le résumé.</div>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal: Assurance invalidité -->
    <div id="modal-inval-av" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(14,16,48,.45);align-items:center;justify-content:center">
      <div style="background:white;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;margin:20px">
        <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <h4 style="font-size:16px;font-weight:700;color:var(--navy);margin:0">Assurance invalidité</h4>
          <button onclick="closeInvalAvModal()" style="background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;padding:0 4px">×</button>
        </div>
        <div style="padding:20px 24px">
          <div class="form-group">
            <label class="form-label">Description</label>
            <input class="form-input" id="inval-av-desc" type="text" placeholder="ex. Police individuelle"/>
          </div>
          <div class="form-group">
            <label class="form-label">Montant mensuel</label>
            <div class="input-sfx"><input class="form-input" id="inval-av-montant" type="text" placeholder="0"/><span class="sfx">$/mois</span></div>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Propriétaire</label>
            <select class="form-select" id="inval-av-proprietaire"><option value="">Sélectionnez…</option></select>
          </div>
        </div>
        <div style="padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#f8f9fd">
          <button class="btn btn-secondary" onclick="closeInvalAvModal()">Annuler</button>
          <button class="btn btn-primary" onclick="saveInvalAv()">Enregistrer</button>
        </div>
      </div>
    </div>

    <div id="page-maladie-grave" class="page">
      <div class="page-title">Maladie grave</div>
      <div class="page-subtitle">Analyse des besoins en cas de maladie grave</div>
      <div class="card"><div class="card-body">
        <div class="row">
          <div class="col form-group"><label class="form-label">Couverture maladie grave existante</label><input class="form-input" type="text" placeholder="0 $"/></div>
          <div class="col form-group"><label class="form-label">Besoin estimé</label><input class="form-input" type="text" placeholder="0 $"/></div>
        </div>
      </div></div>
    </div>

    <div id="page-projets" class="page">
      <div class="page-title">Projets</div>
      <div class="page-subtitle">Projets financiers à court et moyen terme</div>
      <div class="card"><div class="card-body">
        <div class="list-empty">Aucun projet ajouté.</div>
        <button class="btn btn-secondary btn-sm" style="margin-top:12px">
          <svg viewBox="0 0 26 24" width="15" height="15" fill="currentColor"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
          Ajouter un projet
        </button>
      </div></div>
    </div>

    <div id="page-retraite" class="page">
      <div class="page-title">Retraite</div>
      <div class="page-subtitle">Planification de la retraite</div>
      <div class="card"><div class="card-body">
        <div class="row">
          <div class="col form-group"><label class="form-label">Âge de retraite visé</label><input class="form-input" type="number" placeholder="65"/></div>
          <div class="col form-group"><label class="form-label">Revenu désiré à la retraite</label><input class="form-input" type="text" placeholder="0 $/an"/></div>
        </div>
        <div class="row">
          <div class="col form-group"><label class="form-label">RRQ estimé</label><input class="form-input" type="text" placeholder="0 $/an"/></div>
          <div class="col form-group"><label class="form-label">Régime de retraite employeur</label><input class="form-input" type="text" placeholder="0 $/an"/></div>
        </div>
      </div></div>
    </div>

    <!-- ── PAGE: Recommandations ── -->
    <div id="page-recommandations" class="page">
      <div class="page-title">Recommandations</div>
      <div class="page-subtitle">Analyse et recommandations basées sur le profil du client</div>

      <div class="score-grid">
        <div class="score-card">
          <div class="val" style="color:var(--gold)">Équilibré</div>
          <div class="lbl">Profil d'investisseur</div>
        </div>
        <div class="score-card">
          <div class="val">75 / 160</div>
          <div class="lbl">Score investisseur</div>
        </div>
        <div class="score-card">
          <div class="val" style="color:#22c55e">Brouillon</div>
          <div class="lbl">Statut du dossier</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Recommandations prioritaires</div>
        <div class="card-body">
          <div class="recommendation-item">
            <div class="rec-icon" style="background:#ef4444">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <div>
              <div style="font-weight:600;font-size:13px;margin-bottom:3px">Assurance vie insuffisante</div>
              <div style="font-size:12px;color:var(--muted)">Couverture actuelle estimée à 0 $. Besoin calculé : 250 000 $.</div>
            </div>
          </div>
          <div class="recommendation-item">
            <div class="rec-icon" style="background:#f59e0b">
              <svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
            </div>
            <div>
              <div style="font-weight:600;font-size:13px;margin-bottom:3px">Fonds d'urgence à consolider</div>
              <div style="font-size:12px;color:var(--muted)">Recommandation de 3 à 6 mois de dépenses en liquidités accessibles.</div>
            </div>
          </div>
          <div class="recommendation-item">
            <div class="rec-icon" style="background:#3b82f6">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <div>
              <div style="font-weight:600;font-size:13px;margin-bottom:3px">Planification retraite à initier</div>
              <div style="font-size:12px;color:var(--muted)">Cotisations REER et CELI à optimiser selon le profil Équilibré.</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── PAGE: Rapport ── -->
    <div id="page-rapport" class="page">
      <div class="page-title">Rapport</div>
      <div class="page-subtitle">Génération du rapport PDF</div>
      <div class="card">
        <div class="card-body" style="text-align:center;padding:40px 20px">
          <svg width="56" height="56" viewBox="0 0 24 24" fill="none" style="margin-bottom:16px">
            <rect width="24" height="24" rx="6" fill="#f0f3fa"/>
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="#0E1030" stroke-width="1.5" fill="none"/>
            <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="#C9A050" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <div style="font-size:15px;font-weight:700;color:var(--navy);margin-bottom:8px">Rapport ABF — WIGALIE RAPHAEL</div>
          <div style="font-size:13px;color:var(--muted);margin-bottom:24px">Générez le rapport PDF complet incluant toutes les sections de l'analyse</div>
          <button class="btn btn-gold" onclick="showToast('Génération du PDF en cours…')">
            <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            Télécharger le PDF
          </button>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- BOTTOM BAR -->
<div class="bottom-bar">
  <button class="btn btn-secondary" onclick="goPrev()">← Précédent</button>
  <button class="btn btn-primary" onclick="goNext()">Suivant →</button>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
  const pages = [
    'infos-perso','objectifs','actifs-passifs','revenu-epargne',
    'fonds-urgence','deces','invalidite','maladie-grave',
    'projets','retraite','recommandations','rapport'
  ];
  let current = 0;

  function syncConjointInfo() {
    if (!document.getElementById('conjoint')?.checked) return;
    // État civil : même valeur que le client
    const clientEC = document.getElementById('client-etat-civil');
    const conjointEC = document.getElementById('conjoint-etat-civil');
    if (clientEC && conjointEC) conjointEC.value = clientEC.value;
    // Adresse : sync champ par champ
    const addrFields = ['civique','rue','type-unite','numero','case','ville','province','postal'];
    addrFields.forEach(f => {
      const src = document.getElementById('client-addr-' + f);
      const dst = document.getElementById('conjoint-addr-' + f);
      if (src && dst) dst.value = src.value;
    });
  }

  function goTo(id, btn) {
    // hide all pages (clear inline style too so it doesn't override the CSS class)
    document.querySelectorAll('.page').forEach(p => { p.classList.remove('active'); p.style.display = ''; });
    document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
    // show target
    document.getElementById('page-' + id)?.classList.add('active');
    if(btn) btn.classList.add('active');
    // update current index
    current = pages.indexOf(id);
    if (id === 'objectifs') renderObjectives();
    if (id === 'actifs-passifs') updateApSidebar();
    if (id === 'revenu-epargne') updateReSidebar();
    if (id === 'fonds-urgence') { fuRenderActifs(); fuCalc(); }
    if (id === 'deces') decesInit();
    if (id === 'invalidite') invaliditeInit();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function validateCurrentPage() {
    // Objectifs : au moins 3 sélectionnés
    if (pages[current] === 'objectifs') {
      const total = Object.values(objState).flat().filter(i => i.checked).length;
      if (total < 3) {
        showToast('Veuillez sélectionner au moins 3 objectifs');
        return false;
      }
      return true;
    }
    if (pages[current] !== 'infos-perso') return true;
    let valid = true;
    const errors = [];

    function checkText(id) {
      const el = document.getElementById(id);
      if (!el) return;
      const empty = !el.value.trim();
      el.classList.toggle('input-error', empty);
      if (empty) { valid = false; if (!errors.length) el.scrollIntoView({behavior:'smooth', block:'center'}); errors.push(id); }
      else el.classList.remove('input-error');
    }
    function checkSelect(id) {
      const el = document.getElementById(id);
      if (!el) return;
      const empty = !el.value;
      el.classList.toggle('input-error', empty);
      if (empty) { valid = false; if (!errors.length) el.scrollIntoView({behavior:'smooth', block:'center'}); errors.push(id); }
      else el.classList.remove('input-error');
    }
    function checkRadio(name) {
      const checked = document.querySelector(`input[name="${name}"]:checked`);
      const group = document.querySelector(`input[name="${name}"]`)?.closest('.radio-group');
      if (group) group.classList.toggle('radio-error', !checked);
      if (!checked) { valid = false; errors.push(name); }
      else if (group) group.classList.remove('radio-error');
    }

    // Client
    checkText('client-prenom');
    checkText('client-nom');
    checkText('client-ddn-jour');
    checkSelect('client-ddn-mois');
    checkText('client-naissance-annee');
    checkSelect('client-etat-civil');
    checkSelect('client-province');
    checkText('client-canada-depuis');
    checkRadio('tabac');
    checkText('client-addr-ville');
    checkText('client-addr-postal');

    // Conjoint (si plan conjoint)
    if (document.getElementById('conjoint')?.checked) {
      checkText('conjoint-prenom');
      checkText('conjoint-nom');
      checkText('conjoint-ddn-jour');
      checkSelect('conjoint-ddn-mois');
      checkText('conjoint-naissance-annee');
      checkSelect('conjoint-etat-civil');
      checkSelect('conjoint-province');
      checkText('conjoint-canada-depuis');
      checkRadio('co-tabac');
      checkText('conjoint-addr-ville');
      checkText('conjoint-addr-postal');
    }

    if (!valid) showToast('Veuillez remplir tous les champs obligatoires (*)');
    return valid;
  }

  function goNext() {
    if (current < pages.length - 1) {
      if (!validateCurrentPage()) return;
      const navItems = document.querySelectorAll('.nav-item');
      // mark current done, unlock next
      navItems[current]?.classList.add('done');
      navItems[current + 1]?.classList.remove('locked');
      goTo(pages[current + 1], navItems[current + 1]);
    }
  }

  function goPrev() {
    if (current > 0) {
      goTo(pages[current - 1], document.querySelectorAll('.nav-item')[current - 1]);
    }
  }

  function toggleCollapse(btn) {
    btn.classList.toggle('open');
    btn.nextElementSibling.classList.toggle('open');
  }

  /* ── HELPERS : read client / conjoint names ─────────── */
  function getClientPrenom() {
    return (document.getElementById('client-prenom')?.value || '').trim() || 'le client';
  }
  function getConjointPrenom() {
    const conjointActive = document.getElementById('conjoint')?.checked;
    if (!conjointActive) return null;
    const v = (document.getElementById('conjoint-prenom')?.value || '').trim();
    return v || 'Conjoint(e)';
  }

  /* ── MODAL ENFANT ───────────────────────────────────── */
  let _editingEnfantEl = null;

  function openEnfantModal(editEl) {
    _editingEnfantEl = editEl || null;
    const clientPrenom = getClientPrenom();
    // Update label
    document.getElementById('enf-relation-label').textContent = 'Relation avec ' + clientPrenom;
    // Populate À la charge de
    const chargeSelect = document.getElementById('enf-charge');
    chargeSelect.innerHTML = '<option value="">Sélectionnez…</option>';
    chargeSelect.innerHTML += '<option value="client">' + clientPrenom + '</option>';
    const conjointPrenom = getConjointPrenom();
    if (conjointPrenom) {
      chargeSelect.innerHTML += '<option value="conjoint">' + conjointPrenom + '</option>';
      chargeSelect.innerHTML += '<option value="both">' + clientPrenom + ' et ' + conjointPrenom + '</option>';
    }
    chargeSelect.innerHTML += '<option value="noone">Aucun</option>';

    // Pre-fill if editing
    if (editEl) {
      document.getElementById('enf-prenom').value   = editEl.dataset.enfPrenom   || '';
      document.getElementById('enf-nom').value      = editEl.dataset.enfNom      || '';
      document.getElementById('enf-sexe').value     = editEl.dataset.enfSexe     || '';
      document.getElementById('enf-jour').value     = editEl.dataset.enfJour     || '';
      document.getElementById('enf-mois').value     = editEl.dataset.enfMois     || '';
      document.getElementById('enf-annee').value    = editEl.dataset.enfAnnee    || '';
      document.getElementById('enf-relation').value = editEl.dataset.enfRelation || '';
      document.getElementById('enf-charge').value   = editEl.dataset.charge      || '';
      document.getElementById('enf-submit').textContent = 'Mettre à jour';
    } else {
      document.getElementById('enf-submit').textContent = 'Enregistrer';
    }

    document.getElementById('modal-enfant').classList.add('open');
    document.getElementById('enf-prenom').focus();
  }
  function closeEnfantModal() {
    _editingEnfantEl = null;
    document.getElementById('modal-enfant').classList.remove('open');
    document.getElementById('enf-submit').textContent = 'Enregistrer';
    // Reset fields
    ['enf-prenom','enf-nom','enf-sexe','enf-jour','enf-mois','enf-annee','enf-relation','enf-charge']
      .forEach(id => { const el = document.getElementById(id); if(el) el.value = ''; });
  }
  function _buildEnfantItemHTML(nomComplet, relLabel, ddn, sexeLabel, chargeLabel) {
    return `
      <div style="display:flex;align-items:center;gap:10px;flex:1">
        <div style="width:32px;height:32px;border-radius:50%;background:#eef1fc;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">👤</div>
        <div>
          <div style="font-weight:600;color:var(--navy)">${nomComplet}</div>
          <div style="color:var(--muted);font-size:11px;margin-top:2px">${relLabel} · Né(e) : ${ddn} · ${sexeLabel}${chargeLabel ? ' · À charge : ' + chargeLabel : ''}</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:4px;flex-shrink:0">
        <button onclick="openEnfantModal(this.closest('.enfant-item'))" title="Modifier" style="background:none;border:none;color:var(--muted);cursor:pointer;padding:2px 4px;line-height:1;font-size:14px" onmouseover="this.style.color='var(--navy)'" onmouseout="this.style.color='var(--muted)'">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>
        <button onclick="this.closest('.enfant-item').remove()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>
      </div>`;
  }
  function saveEnfant() {
    const prenom = document.getElementById('enf-prenom').value.trim();
    const nom    = document.getElementById('enf-nom').value.trim();
    if (!prenom && !nom) { document.getElementById('enf-prenom').focus(); return; }
    const sexe     = document.getElementById('enf-sexe');
    const jour     = document.getElementById('enf-jour').value;
    const mois     = document.getElementById('enf-mois');
    const annee    = document.getElementById('enf-annee').value;
    const relation = document.getElementById('enf-relation');
    const charge   = document.getElementById('enf-charge');

    const nomComplet  = [prenom, nom].filter(Boolean).join(' ');
    const moisText    = mois.options[mois.selectedIndex]?.text;
    const ddn         = [jour, moisText !== 'Mois' ? moisText : '', annee].filter(Boolean).join(' ') || '—';
    const sexeLabel   = sexe.options[sexe.selectedIndex]?.value ? sexe.options[sexe.selectedIndex]?.text : '—';
    const relLabel    = relation.options[relation.selectedIndex]?.value ? relation.options[relation.selectedIndex]?.text : '—';
    const chargeVal   = charge.options[charge.selectedIndex]?.value || '';
    const chargeLabel = charge.options[charge.selectedIndex]?.text || '';

    if (_editingEnfantEl) {
      // Update existing item
      _editingEnfantEl.dataset.enfPrenom   = prenom;
      _editingEnfantEl.dataset.enfNom      = nom;
      _editingEnfantEl.dataset.enfSexe     = sexe.value;
      _editingEnfantEl.dataset.enfJour     = jour;
      _editingEnfantEl.dataset.enfMois     = mois.value;
      _editingEnfantEl.dataset.enfAnnee    = annee;
      _editingEnfantEl.dataset.enfRelation = relation.value;
      _editingEnfantEl.dataset.charge      = chargeVal;
      _editingEnfantEl.innerHTML = _buildEnfantItemHTML(nomComplet, relLabel, ddn, sexeLabel, chargeLabel);
      closeEnfantModal();
      return;
    }

    const list = document.getElementById('enfants-list');
    if (list.classList.contains('list-empty')) { list.classList.remove('list-empty'); list.innerHTML = ''; }

    const item = document.createElement('div');
    item.className = 'enfant-item';
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;gap:12px';
    item.dataset.charge      = chargeVal;
    item.dataset.enfPrenom   = prenom;
    item.dataset.enfNom      = nom;
    item.dataset.enfSexe     = sexe.value;
    item.dataset.enfJour     = jour;
    item.dataset.enfMois     = mois.value;
    item.dataset.enfAnnee    = annee;
    item.dataset.enfRelation = relation.value;
    item.innerHTML = _buildEnfantItemHTML(nomComplet, relLabel, ddn, sexeLabel, chargeLabel);
    list.appendChild(item);
    closeEnfantModal();
  }
  // Close modal on backdrop click
  document.getElementById('modal-enfant')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-enfant')) closeEnfantModal();
  });

  /* ── LEGAL DROPDOWN ─────────────────────────────────── */
  function toggleLegalMenu(e) {
    e.stopPropagation();
    const dd = document.getElementById('legal-dropdown');
    dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
  }

  /* ── MODAL LÉGAL ─────────────────────────────────────── */
  function openLegalModal(name) {
    document.getElementById('legal-dropdown').style.display = 'none';
    // Set title
    document.getElementById('modal-legal-title').textContent = name;
    // Populate Propriétaire
    const propSelect = document.getElementById('leg-proprietaire');
    propSelect.innerHTML = '<option value="">Sélectionnez…</option>';
    const clientPrenom = getClientPrenom();
    propSelect.innerHTML += '<option value="client">' + clientPrenom + '</option>';
    const conjointPrenom = getConjointPrenom();
    if (conjointPrenom) {
      propSelect.innerHTML += '<option value="conjoint">' + conjointPrenom + '</option>';
    }
    // Reset fields
    ['leg-jour','leg-annee','leg-note'].forEach(id => { const el=document.getElementById(id); if(el) el.value=''; });
    document.getElementById('leg-mois').value = '';
    document.getElementById('leg-type').value = '';
    // Store doc type for save
    document.getElementById('modal-legal').dataset.docType = name;
    document.getElementById('modal-legal').classList.add('open');
    propSelect.focus();
  }
  function closeLegalModal() {
    document.getElementById('modal-legal').classList.remove('open');
  }
  function saveLegal() {
    const docType   = document.getElementById('modal-legal').dataset.docType || '';
    const propSel   = document.getElementById('leg-proprietaire');
    const propText  = propSel.options[propSel.selectedIndex]?.text || '';
    const jour      = document.getElementById('leg-jour').value.trim();
    const moisSel   = document.getElementById('leg-mois');
    const moisText  = moisSel.options[moisSel.selectedIndex]?.text || '';
    const annee     = document.getElementById('leg-annee').value.trim();
    const typeSel   = document.getElementById('leg-type');
    const typeText  = typeSel.options[typeSel.selectedIndex]?.text || '';
    const note      = document.getElementById('leg-note').value.trim();

    const list = document.getElementById('legal-list');
    if (list.classList.contains('list-empty')) { list.classList.remove('list-empty'); list.innerHTML = ''; }
    const item = document.createElement('div');
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px;gap:8px';
    item.innerHTML = `
      <span style="display:flex;align-items:center;gap:8px">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
          <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/>
        </svg>
        <span style="color:var(--text);font-weight:500">${docType}</span>
        ${propText ? `<span style="color:var(--muted);font-size:11px">· ${propText}</span>` : ''}
        ${typeText && typeSel.value ? `<span style="color:var(--muted);font-size:11px">· ${typeText}</span>` : ''}
      </span>
      <button onclick="this.closest('div[style]').remove()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>`;
    item.dataset.formJson = JSON.stringify({
      docType, propOwner: propSel.value, propText,
      jour, mois: moisSel.value, moisText, annee,
      legalType: typeSel.value, typeText, note,
    });
    list.appendChild(item);
    closeLegalModal();
  }
  // Close legal modal on backdrop click
  document.getElementById('modal-legal')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-legal')) closeLegalModal();
  });
  // Close all dropdowns when clicking outside
  document.addEventListener('click', () => {
    ['legal-dropdown','placement-dropdown','bien-dropdown','passif-dropdown']
      .forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
  });

  /* ── ACTIFS / PASSIFS ───────────────────────────────── */
  const AP_INSTITUTIONS = ['B2B','Banque Laurentienne','Banque Scotia','Beneva','BMO','BNC',
    'Canada Vie (Great West, London Life)','CIBC','Desjardins','Empire Vie','Fidelity',
    'Financière Sun Life','FTQ','HSBC','iA Groupe financier','IG gestion privée de patrimoine',
    'ING','Investia','Manuvie','Primerica','RBC','Scotia iTRADE','SSQ/La Capitale','Tangerine','TD','Autre'];
  const AP_RENDEMENT = { prudent:'3,00', moderate:'3,50', balanced:'3,70', growth:'4,00', aggressive:'5,00' };

  function apCloseDropdowns() {
    ['placement-dropdown','bien-dropdown','passif-dropdown']
      .forEach(id => { const el=document.getElementById(id); if(el) el.style.display='none'; });
  }
  function toggleApMenu(e, menuId) {
    e.stopPropagation();
    const wasOpen = document.getElementById(menuId)?.style.display === 'block';
    apCloseDropdowns();
    const dd = document.getElementById(menuId);
    if (dd) dd.style.display = wasOpen ? 'none' : 'block';
  }
  let _editingItem = null;

  function apFillProprietaire(selId) {
    const sel = document.getElementById(selId);
    sel.innerHTML = '<option value="">Sélectionnez…</option>';
    sel.innerHTML += '<option value="client">' + getClientPrenom() + '</option>';
    const c = getConjointPrenom();
    if (c) sel.innerHTML += '<option value="conjoint">' + c + '</option>';
  }
  function apFillBienProprietaire(selId) {
    const sel = document.getElementById(selId);
    const cn = getClientPrenom(), cj = getConjointPrenom();
    sel.innerHTML = '<option value="">Sélectionnez…</option>';
    sel.innerHTML += `<option value="client">${cn}</option>`;
    if (cj) {
      sel.innerHTML += `<option value="conjoint">${cj}</option>`;
      sel.innerHTML += `<option value="both">${cn} et ${cj}</option>`;
    }
  }
  function passifPropChange() {
    const val = document.getElementById('pass-proprietaire').value;
    const row = document.getElementById('pass-parts-row');
    row.style.display = val === 'both' ? '' : 'none';
    if (val === 'both') {
      document.getElementById('pass-part-label-client').textContent = getClientPrenom();
      document.getElementById('pass-part-label-conjoint').textContent = getConjointPrenom() || 'conjoint';
      const pc = document.getElementById('pass-part-client');
      const pj = document.getElementById('pass-part-conjoint');
      if (!pc.value) pc.value = '50';
      if (!pj.value) pj.value = '50';
    }
  }
  function bienPropChange() {
    const val = document.getElementById('bien-proprietaire').value;
    const row = document.getElementById('bien-parts-row');
    row.style.display = val === 'both' ? '' : 'none';
    if (val === 'both') {
      document.getElementById('bien-part-label-client').textContent = getClientPrenom();
      document.getElementById('bien-part-label-conjoint').textContent = getConjointPrenom() || 'conjoint';
      const pc = document.getElementById('bien-part-client');
      const pj = document.getElementById('bien-part-conjoint');
      if (!pc.value) pc.value = '50';
      if (!pj.value) pj.value = '50';
    }
  }
  function editApItem(item) {
    _editingItem = item;
    const mtype = item.dataset.modalType || '';
    const fdata = item.dataset.formJson ? JSON.parse(item.dataset.formJson) : null;
    const type  = item.dataset.aptype || '';
    if (mtype === 'bien')           openBienModal(type, fdata);
    else if (mtype === 'placement') openPlacementModal(type, fdata);
    else if (mtype === 'passif')    openPassifModal(type, fdata);
  }
  function apBuildItemInner(type, propText, valeurText, subText, iconColor, bgColor) {
    return `
      <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0">
        <div style="width:32px;height:32px;border-radius:8px;background:${bgColor};display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <div style="min-width:0">
          <div style="font-weight:600;color:var(--navy)">${type}</div>
          <div style="color:var(--muted);font-size:11px;margin-top:2px">${[propText,valeurText,subText].filter(Boolean).join(' · ')}</div>
        </div>
      </div>
      <div style="display:flex;gap:2px;flex-shrink:0">
        <button onclick="editApItem(this.closest('[data-valeur]'))" title="Modifier" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:15px;line-height:1;padding:2px 6px">✎</button>
        <button onclick="this.closest('[data-valeur]').remove();updateApSidebar();if(typeof updateEpargneSection==='function')updateEpargneSection()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>
      </div>`;
  }
  function apUpdateItem(item, type, propText, valeurText, subText, valeurNum, owner, modalType, formJson, partClient, partConjoint) {
    item.dataset.valeur    = valeurNum;
    item.dataset.owner     = owner || '';
    item.dataset.modalType = modalType || '';
    item.dataset.formJson  = formJson || '';
    item.dataset.aptype    = type;
    if (partClient   !== undefined) item.dataset.partClient   = partClient;
    if (partConjoint !== undefined) item.dataset.partConjoint = partConjoint;
    const isPassif  = item.closest('#passifs-list') !== null;
    const iconColor = isPassif ? '#ef4444' : 'var(--valid)';
    const bgColor   = isPassif ? '#fef2f2' : '#f0fdf4';
    item.innerHTML  = apBuildItemInner(type, propText, valeurText, subText, iconColor, bgColor);
    updateApSidebar();
    if (!isPassif && typeof updateEpargneSection === 'function') updateEpargneSection();
  }
  function apFillInstitution(selId) {
    const sel = document.getElementById(selId);
    sel.innerHTML = '<option value="">Sélectionnez…</option>';
    AP_INSTITUTIONS.forEach(i => sel.innerHTML += `<option>${i}</option>`);
  }

  // ── SIDEBAR ──────────────────────────────────────────
  function updateApSidebar() {
    const fmt = v => v.toLocaleString('fr-CA', {minimumFractionDigits:0,maximumFractionDigits:0}) + ' $';
    const colorVn = v => v < 0 ? '#ef4444' : 'var(--navy)';
    let totalActifs=0, totalPassifs=0, clientActifs=0, clientPassifs=0, conjointActifs=0, conjointPassifs=0;
    document.querySelectorAll('#actifs-list [data-valeur]').forEach(el => {
      const v = parseFloat(el.dataset.valeur)||0; totalActifs+=v;
      if (el.dataset.owner === 'both') {
        const pc = parseFloat(el.dataset.partClient  ?? 50)/100;
        const pj = parseFloat(el.dataset.partConjoint ?? 50)/100;
        clientActifs += v*pc; conjointActifs += v*pj;
      } else if (el.dataset.owner === 'conjoint') { conjointActifs+=v; } else { clientActifs+=v; }
    });
    document.querySelectorAll('#passifs-list [data-valeur]').forEach(el => {
      const v = parseFloat(el.dataset.valeur)||0; totalPassifs+=v;
      if (el.dataset.owner === 'both') {
        const pc = parseFloat(el.dataset.partClient  ?? 50)/100;
        const pj = parseFloat(el.dataset.partConjoint ?? 50)/100;
        clientPassifs += v*pc; conjointPassifs += v*pj;
      } else if (el.dataset.owner === 'conjoint') { conjointPassifs+=v; } else { clientPassifs+=v; }
    });
    const vn=totalActifs-totalPassifs, cvn=clientActifs-clientPassifs, jvn=conjointActifs-conjointPassifs;
    const set = (id,val,color) => { const el=document.getElementById(id); if(el){el.textContent=val;if(color!==undefined)el.style.color=color;} };
    set('ap-total-vn', fmt(vn), colorVn(vn));
    set('ap-client-name', getClientPrenom());
    set('ap-client-vn',      fmt(cvn),          colorVn(cvn));
    set('ap-client-actifs',  fmt(clientActifs));
    set('ap-client-passifs', fmt(clientPassifs));
    const conjoint = getConjointPrenom();
    const conjBlock = document.getElementById('ap-conjoint-block');
    if (conjBlock) conjBlock.style.display = conjoint ? 'block' : 'none';
    if (conjoint) {
      set('ap-conjoint-name',    conjoint);
      set('ap-conjoint-vn',      fmt(jvn),          colorVn(jvn));
      set('ap-conjoint-actifs',  fmt(conjointActifs));
      set('ap-conjoint-passifs', fmt(conjointPassifs));
    }
  }
  function apAddToList(listId, type, propText, valeurText, subText, valeurNum, owner, modalType, formJson, partClient, partConjoint) {
    const list = document.getElementById(listId);
    if (list.classList.contains('list-empty')) { list.classList.remove('list-empty'); list.innerHTML = ''; }
    const isPassif  = listId === 'passifs-list';
    const iconColor = isPassif ? '#ef4444' : 'var(--valid)';
    const bgColor   = isPassif ? '#fef2f2' : '#f0fdf4';
    const item = document.createElement('div');
    item.dataset.valeur    = valeurNum;
    item.dataset.owner     = owner || '';
    item.dataset.modalType = modalType || '';
    item.dataset.formJson  = formJson || '';
    item.dataset.aptype    = type;
    if (partClient   !== undefined) item.dataset.partClient   = partClient;
    if (partConjoint !== undefined) item.dataset.partConjoint = partConjoint;
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;gap:12px';
    item.innerHTML = apBuildItemInner(type, propText, valeurText, subText, iconColor, bgColor);
    list.appendChild(item);
    updateApSidebar();
    if (listId === 'actifs-list') updateEpargneSection();
  }

  // ── MODAL PLACEMENT ──────────────────────────────────
  const PLAC_BOTH_TYPES  = ['Compte bancaire','Non enregistré','REEE'];
  const PLAC_LEGIS_TYPES = ['CRI','FERR','FRV','REER Immobilisé'];
  function openPlacementModal(type, prefill) {
    if (!prefill) { apCloseDropdowns(); _editingItem = null; }
    document.getElementById('modal-placement').dataset.type = type;
    document.getElementById('plac-title').textContent = type;
    document.getElementById('plac-description').value = prefill?.description ?? type;
    if (PLAC_BOTH_TYPES.includes(type)) apFillBienProprietaire('plac-proprietaire');
    else apFillProprietaire('plac-proprietaire');
    document.getElementById('plac-proprietaire').value = prefill?.owner ?? '';
    const legisRow = document.getElementById('plac-legislation-row');
    if (legisRow) legisRow.style.display = PLAC_LEGIS_TYPES.includes(type) ? '' : 'none';
    document.getElementById('plac-legislation').value = prefill?.legislation ?? '';
    const dateOuvRow = document.getElementById('plac-date-ouverture-row');
    if (dateOuvRow) dateOuvRow.style.display = (type === 'CELIAPP') ? '' : 'none';
    const dateOuvInput = document.getElementById('plac-date-ouverture');
    if (dateOuvInput) dateOuvInput.value = prefill?.dateOuverture ?? '';
    const saveBtn = document.getElementById('plac-save-btn');
    if (saveBtn) saveBtn.disabled = (type === 'CELIAPP' && !prefill?.dateOuverture);
    apFillInstitution('plac-institution');
    document.getElementById('plac-institution').value = prefill?.institution ?? '';
    document.getElementById('plac-valeur').value = prefill?.valeur ?? '';
    document.getElementById('plac-notes').value = prefill?.notes ?? '';
    document.getElementById('plac-portefeuille').value = prefill?.portefeuille ?? 'balanced';
    document.getElementById('plac-rendement').value = prefill?.rendement ?? '3,70';
    document.getElementById('plac-categorie').value = prefill?.categorie ?? '';
    document.getElementById('modal-placement').classList.add('open');
    document.getElementById('plac-valeur').focus();
  }
  function syncRendement() {
    const v = document.getElementById('plac-portefeuille').value;
    document.getElementById('plac-rendement').value = AP_RENDEMENT[v] || '';
  }
  function closePlacementModal() { document.getElementById('modal-placement').classList.remove('open'); _editingItem = null; }
  function savePlacement() {
    const type   = document.getElementById('modal-placement').dataset.type || '';
    const prop   = document.getElementById('plac-proprietaire');
    const propVal = prop.value;
    const propTx = propVal ? prop.options[prop.selectedIndex].text : '';
    const valStr = document.getElementById('plac-valeur').value.trim();
    const valNum = parseFloat(valStr.replace(/\s/g,'').replace(',','.')) || 0;
    const portefeuille = document.getElementById('plac-portefeuille');
    const portTx = portefeuille.options[portefeuille.selectedIndex].text;
    const rendement   = document.getElementById('plac-rendement').value.trim();
    const institution = document.getElementById('plac-institution').value;
    const categorie   = document.getElementById('plac-categorie').value;
    const notes       = document.getElementById('plac-notes').value.trim();
    const descr       = document.getElementById('plac-description').value.trim();
    const sub = portTx + (rendement ? ' · '+rendement+'%' : '');
    const partClient = 50, partConjoint = 50; // placements toujours 50/50
    const legislation = document.getElementById('plac-legislation')?.value ?? '';
    const dateOuverture = document.getElementById('plac-date-ouverture')?.value.trim() ?? '';
    if (type === 'CELIAPP' && !dateOuverture) {
      showToast('La date d\'ouverture est requise pour un CELIAPP');
      return;
    }
    const formJson = JSON.stringify({description:descr, owner:propVal, valeur:valStr, portefeuille:portefeuille.value, rendement, institution, categorie, notes, legislation, dateOuverture});
    if (_editingItem) {
      apUpdateItem(_editingItem, type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'placement', formJson, partClient, partConjoint);
    } else {
      apAddToList('actifs-list', type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'placement', formJson, partClient, partConjoint);
    }
    closePlacementModal();
  }
  document.getElementById('modal-placement')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-placement')) closePlacementModal();
  });

  // ── MODAL BIEN ───────────────────────────────────────
  function openBienModal(type, prefill) {
    if (!prefill) { apCloseDropdowns(); _editingItem = null; }
    document.getElementById('modal-bien').dataset.type = type;
    document.getElementById('bien-title').textContent = type;
    document.getElementById('bien-description').value = prefill?.description ?? type;
    apFillBienProprietaire('bien-proprietaire');
    document.getElementById('bien-proprietaire').value = prefill?.owner ?? '';
    bienPropChange();
    if (prefill?.owner === 'both') {
      document.getElementById('bien-part-client').value   = prefill?.partClient   ?? '50';
      document.getElementById('bien-part-conjoint').value = prefill?.partConjoint ?? '50';
    }
    document.getElementById('bien-valeur').value     = prefill?.valeur     ?? '';
    document.getElementById('bien-cout').value       = prefill?.cout       ?? '';
    document.getElementById('bien-croissance').value = prefill?.croissance ?? '';
    document.getElementById('bien-notes').value      = prefill?.notes      ?? '';
    document.getElementById('modal-bien').classList.add('open');
    document.getElementById('bien-valeur').focus();
  }
  function closeBienModal() { document.getElementById('modal-bien').classList.remove('open'); _editingItem = null; }
  function saveBien() {
    const type   = document.getElementById('modal-bien').dataset.type || '';
    const prop   = document.getElementById('bien-proprietaire');
    const propVal = prop.value;
    const propTx  = propVal ? prop.options[prop.selectedIndex].text : '';
    const valStr  = document.getElementById('bien-valeur').value.trim();
    const valNum  = parseFloat(valStr.replace(/\s/g,'').replace(',','.')) || 0;
    const cout    = document.getElementById('bien-cout').value.trim();
    const croiss  = document.getElementById('bien-croissance').value.trim();
    const notes   = document.getElementById('bien-notes').value.trim();
    const descr   = document.getElementById('bien-description').value.trim();
    let partClient = 50, partConjoint = 50;
    if (propVal === 'both') {
      partClient   = parseFloat(document.getElementById('bien-part-client').value)   || 50;
      partConjoint = parseFloat(document.getElementById('bien-part-conjoint').value) || 50;
    }
    const sub = cout ? 'Coût : '+cout+' $' : '';
    const formJson = JSON.stringify({description:descr, owner:propVal, valeur:valStr, cout, croissance:croiss, notes, partClient, partConjoint});
    if (_editingItem) {
      apUpdateItem(_editingItem, type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'bien', formJson, partClient, partConjoint);
    } else {
      apAddToList('actifs-list', type, propTx, valStr ? valStr+' $' : '', sub, valNum, propVal, 'bien', formJson, partClient, partConjoint);
    }
    closeBienModal();
  }
  document.getElementById('modal-bien')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-bien')) closeBienModal();
  });

  // ── MODAL PASSIF ─────────────────────────────────────
  function openPassifModal(type, prefill) {
    if (!prefill) { apCloseDropdowns(); _editingItem = null; }
    document.getElementById('modal-passif').dataset.type = type;
    document.getElementById('pass-title').textContent = type;
    document.getElementById('pass-description').value = prefill?.description ?? type;
    apFillBienProprietaire('pass-proprietaire');
    document.getElementById('pass-proprietaire').value = prefill?.owner ?? '';
    passifPropChange();
    if (prefill?.owner === 'both') {
      document.getElementById('pass-part-client').value   = prefill?.partClient   ?? '50';
      document.getElementById('pass-part-conjoint').value = prefill?.partConjoint ?? '50';
    }
    apFillInstitution('pass-institution');
    document.getElementById('pass-institution').value        = prefill?.institution   ?? '';
    document.getElementById('pass-solde').value              = prefill?.solde         ?? '';
    document.getElementById('pass-amort-val').value          = prefill?.amortVal      ?? '';
    document.getElementById('pass-amort-unit').value         = prefill?.amortUnit     ?? 'month';
    document.getElementById('pass-taux').value               = prefill?.taux          ?? '';
    document.getElementById('pass-paiement').value           = prefill?.paiement      ?? '';
    document.getElementById('pass-paiement-freq').value      = prefill?.paiementFreq  ?? 'monthly';
    document.getElementById('pass-renouvellement-mois').value  = prefill?.renouvMois  ?? '';
    document.getElementById('pass-renouvellement-annee').value = prefill?.renouvAnnee ?? '';
    document.getElementById('pass-notes').value              = prefill?.notes         ?? '';
    document.querySelectorAll('.calc-tab').forEach((b,i) => b.classList.toggle('active', i===0));
    document.getElementById('modal-passif').classList.add('open');
    document.getElementById('pass-solde').focus();
  }
  function setCalcType(type, btn) {
    document.querySelectorAll('.calc-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }
  function closePassifModal() { document.getElementById('modal-passif').classList.remove('open'); _editingItem = null; }
  function savePassif() {
    const type   = document.getElementById('modal-passif').dataset.type || '';
    const prop   = document.getElementById('pass-proprietaire');
    const propVal = prop.value;
    const propTx  = propVal ? prop.options[prop.selectedIndex].text : '';
    const solde   = document.getElementById('pass-solde').value.trim();
    const soldeNum = parseFloat(solde.replace(/\s/g,'').replace(',','.')) || 0;
    const taux    = document.getElementById('pass-taux').value.trim();
    const paiement = document.getElementById('pass-paiement').value.trim();
    const freq    = document.getElementById('pass-paiement-freq');
    const freqTx  = freq.options[freq.selectedIndex].text;
    const amortVal   = document.getElementById('pass-amort-val').value.trim();
    const amortUnit  = document.getElementById('pass-amort-unit').value;
    const renouvMois = document.getElementById('pass-renouvellement-mois').value;
    const renouvAnnee = document.getElementById('pass-renouvellement-annee').value.trim();
    const institution = document.getElementById('pass-institution').value;
    const notes  = document.getElementById('pass-notes').value.trim();
    const descr  = document.getElementById('pass-description').value.trim();
    const subParts = [];
    if (taux) subParts.push(taux + ' %');
    if (paiement) subParts.push(paiement + ' $ ' + freqTx);
    let partClient = 50, partConjoint = 50;
    if (propVal === 'both') {
      partClient   = parseFloat(document.getElementById('pass-part-client').value)   || 50;
      partConjoint = parseFloat(document.getElementById('pass-part-conjoint').value) || 50;
    }
    const formJson = JSON.stringify({description:descr, owner:propVal, solde, amortVal, amortUnit, taux, paiement, paiementFreq:freq.value, renouvMois, renouvAnnee, institution, notes, partClient, partConjoint});
    if (_editingItem) {
      apUpdateItem(_editingItem, type, propTx, solde ? solde+' $' : '', subParts.join(' · '), soldeNum, propVal, 'passif', formJson, partClient, partConjoint);
    } else {
      apAddToList('passifs-list', type, propTx, solde ? solde+' $' : '', subParts.join(' · '), soldeNum, propVal, 'passif', formJson, partClient, partConjoint);
    }
    closePassifModal();
  }
  document.getElementById('modal-passif')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-passif')) closePassifModal();
  });

  /* ── OBJECTIFS ─────────────────────────────────────── */
  const OBJECTIVES = [
    {
      id: 'famille', label: 'Famille',
      icon: '👨‍👩‍👧',
      items: [
        { id: 'getMarried',                label: 'Se marier / s\'unir (conjoint de fait)', checked: true },
        { id: 'moveInTogether',            label: 'Prévoir une cohabitation', checked: false },
        { id: 'haveChildren',              label: 'Avoir des enfants', checked: true },
        { id: 'financeChildrenEducation',  label: 'Financer les études des enfants', checked: false },
        { id: 'buySellPropertyFamily',     label: 'Acheter / vendre une propriété', checked: false },
        { id: 'specialProjects',           label: 'Projets spéciaux', checked: true },
      ]
    },
    {
      id: 'travail', label: 'Travail',
      icon: '💼',
      items: [
        { id: 'returnToStudies',    label: 'Reprendre les études', checked: true },
        { id: 'getAPromotion',      label: 'Obtenir une promotion', checked: false },
        { id: 'changeCareers',      label: 'Changer de carrière', checked: false },
        { id: 'withdrawFromJob',    label: 'Se retirer du marché du travail', checked: false },
        { id: 'returnToJob',        label: 'Retourner sur le marché du travail', checked: false },
        { id: 'buySellBusiness',    label: 'Acheter / vendre une entreprise ou un commerce', checked: false },
      ]
    },
    {
      id: 'finances', label: 'Finances',
      icon: '💰',
      items: [
        { id: 'reduceTaxes',          label: 'Diminuer les impôts', checked: false },
        { id: 'repayDebts',           label: 'Rembourser les dettes', checked: false },
        { id: 'repayStudentLoan',     label: 'Rembourser le prêt / marge étudiant', checked: false },
        { id: 'reviewInvestments',    label: 'Revoir les placements actuels', checked: false },
        { id: 'reduceInsecurity',     label: 'Réduire l\'insécurité relative aux finances', checked: false },
        { id: 'buySellPropertyFin',   label: 'Acheter / vendre une propriété', checked: false },
        { id: 'capitalGain',          label: 'Amortir le gain en capital', checked: false },
        { id: 'emergencyFundFin',     label: 'Constituer un fonds d\'urgence', checked: false },
        { id: 'reviewSavings',        label: 'Revoir la méthode d\'épargne', checked: false },
        { id: 'maximizeTax',          label: 'Maximiser fiscalement les revenus de placement', checked: false },
      ]
    },
    {
      id: 'loisirs', label: 'Loisirs',
      icon: '🏖️',
      items: [
        { id: 'planTrip',        label: 'Planifier des voyages, sports et loisirs', checked: false },
        { id: 'volunteer',       label: 'Faire du bénévolat', checked: false },
        { id: 'associations',    label: 'S\'engager dans des associations', checked: false },
      ]
    },
    {
      id: 'retraite', label: 'Retraite',
      icon: '🌅',
      items: [
        { id: 'planRetirementAge',    label: 'Prévoir l\'âge de retraite', checked: false },
        { id: 'maintainLifeStyle',    label: 'Maintenir le niveau de vie', checked: false },
        { id: 'protectInflation',     label: 'Se protéger contre l\'inflation', checked: false },
        { id: 'stayAtHome',           label: 'Rester à domicile le plus longtemps possible', checked: false },
        { id: 'retirementOccupation', label: 'Prévoir une occupation pour la retraite', checked: false },
        { id: 'keepSellProperty',     label: 'Conserver, vendre ou transférer une propriété', checked: false },
        { id: 'diversifyIncome',      label: 'Diversifier vos sources de revenu', checked: false },
      ]
    },
    {
      id: 'protections', label: 'Protections',
      icon: '🛡️',
      items: [
        { id: 'repayLoansDeath',       label: 'Rembourser les emprunts en cas de décès', checked: false },
        { id: 'deathExpenses',         label: 'Prévoir les dépenses liées au décès', checked: true },
        { id: 'familyLifeStyleDeath',  label: 'Maintenir le niveau de vie familial en cas de décès', checked: false },
        { id: 'leaveInheritance',      label: 'Léguer un héritage', checked: false },
        { id: 'taxesAtDeath',          label: 'Prévoir les impôts au décès', checked: false },
        { id: 'makeDonations',         label: 'Planifier des dons', checked: false },
        { id: 'makeWill',              label: 'Rédiger un testament', checked: false },
        { id: 'childLifeInsurance',    label: 'Prévoir une assurance vie pour enfant(s)', checked: false },
        { id: 'protectionMandate',     label: 'Rédiger un mandat de protection', checked: false },
        { id: 'emergencyFundProt',     label: 'Constituer un fonds d\'urgence', checked: false },
        { id: 'lifeStyleDisability',   label: 'Maintenir le niveau de vie en cas d\'invalidité', checked: false },
        { id: 'lifeStyleIllness',      label: 'Maintenir le niveau de vie en cas de maladie grave', checked: false },
        { id: 'illnessExpenses',       label: 'Couvrir les frais associés à la maladie', checked: false },
        { id: 'childDisability',       label: 'Prévoir une couverture pour enfant(s) en cas de maladie grave', checked: false },
      ]
    },
    {
      id: 'autre', label: 'Autre',
      icon: '📋',
      items: []
    },
  ];

  // State: track checked + custom items
  const objState = {};
  OBJECTIVES.forEach(cat => {
    objState[cat.id] = cat.items.map(i => ({ ...i }));
  });

  function countChecked(catId) {
    return objState[catId].filter(i => i.checked).length;
  }

  function renderObjectives() {
    const container = document.getElementById('objectives-container');
    if (!container) return;
    container.innerHTML = '';
    container.style.cssText = 'display:grid;grid-template-columns:repeat(2,1fr);gap:12px;align-items:start';

    OBJECTIVES.forEach(cat => {
      const items = objState[cat.id];
      const checkedCount = items.filter(i => i.checked).length;
      const isOpen = true; // start all open

      const catDiv = document.createElement('div');
      catDiv.className = 'obj-category';
      catDiv.dataset.catId = cat.id;

      // Header
      const hdr = document.createElement('div');
      hdr.className = 'obj-cat-header open';
      hdr.innerHTML = `
        <div class="obj-cat-title">
          <span>${cat.icon}</span>
          <span>${cat.label}</span>
          <span class="obj-cat-badge ${checkedCount === 0 ? 'zero' : ''}">${checkedCount}</span>
        </div>
        <svg class="obj-cat-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="m6 9 6 6 6-6"/>
        </svg>`;
      hdr.addEventListener('click', () => {
        hdr.classList.toggle('open');
        body.classList.toggle('open');
      });

      // Body
      const body = document.createElement('div');
      body.className = 'obj-cat-body open';

      items.forEach((item, idx) => {
        const row = document.createElement('div');
        row.className = 'obj-item';

        const rowHdr = document.createElement('div');
        rowHdr.className = 'obj-item-header';

        // Checkbox SVGs
        const checkBtn = document.createElement('button');
        checkBtn.className = 'obj-check-btn' + (item.checked ? ' checked' : '');
        checkBtn.type = 'button';
        checkBtn.title = item.label;
        checkBtn.innerHTML = `
          <svg class="icon-unchecked" viewBox="0 0 26 24"><path d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
          <svg class="icon-checked" viewBox="0 0 26 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>`;

        checkBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          objState[cat.id][idx].checked = !objState[cat.id][idx].checked;
          renderObjectives();
        });

        const titleWrap = document.createElement('div');
        titleWrap.className = 'obj-item-title-wrap';
        titleWrap.innerHTML = `<span class="obj-item-title ${item.checked ? 'checked' : ''}">${item.label}</span>`;
        titleWrap.addEventListener('click', () => {
          expandBtn.classList.toggle('open');
          detail.classList.toggle('open');
        });

        const expandBtn = document.createElement('button');
        expandBtn.type = 'button';
        expandBtn.className = 'obj-expand-btn';
        expandBtn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>`;
        expandBtn.addEventListener('click', () => {
          expandBtn.classList.toggle('open');
          detail.classList.toggle('open');
        });

        rowHdr.appendChild(checkBtn);
        rowHdr.appendChild(titleWrap);
        rowHdr.appendChild(expandBtn);

        // Detail panel
        const detail = document.createElement('div');
        detail.className = 'obj-item-detail';
        detail.innerHTML = `
          <label>Notes / précisions</label>
          <textarea placeholder="Ajoutez des précisions pour cet objectif…">${item.note || ''}</textarea>`;
        detail.querySelector('textarea').addEventListener('input', (e) => {
          objState[cat.id][idx].note = e.target.value;
        });

        row.appendChild(rowHdr);
        row.appendChild(detail);
        body.appendChild(row);
      });

      // Add custom objective button
      const addBtn = document.createElement('button');
      addBtn.type = 'button';
      addBtn.className = 'obj-add-btn';
      addBtn.innerHTML = `
        <svg viewBox="0 0 26 24"><path d="M18 13.5h-4.5v4.5h-3v-4.5h-4.5v-3h4.5v-4.5h3v4.5h4.5v3z"/></svg>
        Ajouter un objectif personnalisé`;
      addBtn.addEventListener('click', () => {
        const label = prompt('Nom de l\'objectif personnalisé :');
        if (label && label.trim()) {
          objState[cat.id].push({ id: 'custom_' + Date.now(), label: label.trim(), checked: true, custom: true });
          renderObjectives();
        }
      });
      body.appendChild(addBtn);

      catDiv.appendChild(hdr);
      catDiv.appendChild(body);
      container.appendChild(catDiv);
    });
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', () => renderObjectives());

  /* ── REVENU ET ÉPARGNE ───────────────────────────────── */

  let reTabMode = 'annuel'; // 'annuel' | 'mensuel'

  function setReTab(mode, btn) {
    reTabMode = mode;
    document.querySelectorAll('#re-tab-annuel,#re-tab-mensuel').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    updateReSidebar();
  }

  function toggleRevenuDropdown() {
    document.getElementById('revenu-dropdown').classList.toggle('open');
  }

  // Close dropdown on outside click
  document.addEventListener('click', e => {
    const wrap = document.getElementById('revenu-add-wrap');
    if (wrap && !wrap.contains(e.target)) {
      document.getElementById('revenu-dropdown')?.classList.remove('open');
    }
  });

  function reFillProprietaire() {
    const sel = document.getElementById('revenu-proprietaire');
    if (!sel) return;
    sel.innerHTML = '';
    const client = getClientPrenom();
    const conj   = getConjointPrenom();
    sel.appendChild(Object.assign(document.createElement('option'), { value:'', textContent:'Sélectionnez…' }));
    sel.appendChild(Object.assign(document.createElement('option'), { value:'client', textContent: client }));
    if (conj) sel.appendChild(Object.assign(document.createElement('option'), { value:'conjoint', textContent: conj }));
  }

  function openRevenuModal(type) {
    document.getElementById('revenu-dropdown')?.classList.remove('open');
    const isEmploi = type === 'Revenu d\'emploi';
    document.getElementById('revenu-modal-title').textContent = type;
    document.getElementById('modal-revenu').dataset.type = type;
    document.getElementById('revenu-emploi-fields').style.display = isEmploi ? '' : 'none';
    document.getElementById('revenu-autre-fields').style.display  = isEmploi ? 'none' : '';
    // Reset fields
    ['revenu-montant','revenu-profession','revenu-employeur','revenu-embauche-annee','revenu-description',
     'revenu-portion-imposable','revenu-taux-indexation','revenu-debut-mois','revenu-debut-annee'].forEach(id => {
      const el = document.getElementById(id); if(el) el.value = id === 'revenu-portion-imposable' ? '100,00' : id === 'revenu-taux-indexation' ? '0,00' : '';
    });
    document.getElementById('revenu-embauche-mois').value = '';
    document.getElementById('revenu-frequence').value = '12';
    const nonEl = document.getElementById('revenu-indexe-non');
    if (nonEl) nonEl.checked = true;
    const autosaveNon = document.getElementById('revenu-autosave-non');
    if (autosaveNon) autosaveNon.checked = true;
    const finType = document.getElementById('revenu-fin-type');
    if (finType) finType.value = 'retirement';
    reFillProprietaire();
    document.getElementById('modal-revenu').classList.add('open');
    setTimeout(() => document.getElementById('revenu-montant')?.focus(), 50);
  }

  function closeRevenuModal() {
    document.getElementById('modal-revenu').classList.remove('open');
  }

  document.getElementById('modal-revenu')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-revenu')) closeRevenuModal();
  });

  function saveRevenu() {
    const type    = document.getElementById('modal-revenu').dataset.type || '';
    const isEmploi = type === 'Revenu d\'emploi';
    const propSel = document.getElementById('revenu-proprietaire');
    const owner   = propSel.value || 'client';
    const propTx  = owner === 'client' ? getClientPrenom() : (getConjointPrenom() || 'Conjoint(e)');
    const montant = (document.getElementById('revenu-montant').value.trim()) || '0';
    const montantNum = parseFloat(montant.replace(/\s/g,'').replace(',','.')) || 0;

    let description, frequence, freqFactor;
    if (isEmploi) {
      description = document.getElementById('revenu-profession').value.trim() || type;
      frequence   = 'Annuelle';
      freqFactor  = 1;
    } else {
      description = document.getElementById('revenu-description').value.trim() || type;
      const freqSel = document.getElementById('revenu-frequence');
      freqFactor  = parseInt(freqSel.value) || 1;
      const freqMap = {'1':'Annuelle','12':'Mensuelle','26':'Aux deux semaines','52':'Hebdomadaire'};
      frequence   = freqMap[freqSel.value] || 'Annuelle';
    }
    const annuel = montantNum * freqFactor;

    const r = computeImpot(annuel);
    const fmt = n => n.toLocaleString('fr-CA', {maximumFractionDigits:0}) + ' $';
    const netLabel = r ? `<span style="font-size:11px;color:#22c55e;margin-left:4px">(net ${fmt(r.net)})</span>` : '';

    const tbody = document.getElementById('revenu-list');
    const tr = document.createElement('tr');
    tr.dataset.revenuAnnuel = annuel;
    tr.dataset.owner = owner;
    tr.dataset.revenuType = isEmploi ? 'emploi' : 'autre';
    tr.dataset.formJson = JSON.stringify({ type, owner, isEmploi, description, montant, frequence, freqFactor, annuel });
    tr.innerHTML = `
      <td>${propTx}</td>
      <td>${isEmploi ? 'Emploi' : 'Autre'}</td>
      <td>${description}</td>
      <td>${montant} $${netLabel}</td>
      <td>${frequence}</td>
      <td class="col-action">
        <button class="re-action-btn" title="Détail fiscal" onclick="reToggleDetail(this)" style="color:var(--navy)">
          <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="m6 9 6 6 6-6"/></svg>
        </button>
        <button class="re-action-btn" title="Modifier" onclick="showToast('Modification non disponible dans la démo')">
          <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>
        <button class="re-action-btn del" title="Supprimer" onclick="reDeleteRow(this)">
          <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </button>
      </td>`;
    tbody.appendChild(tr);

    // Detail row (collapsed by default)
    const trDetail = document.createElement('tr');
    trDetail.className = 're-detail-row';
    trDetail.style.display = 'none';
    trDetail.innerHTML = r ? `<td colspan="6"><div class="re-detail-inner">
      <div class="re-detail-item"><span class="re-detail-lbl">RRQ</span><span class="re-detail-val deduction">${fmt(r.rrq)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">AE</span><span class="re-detail-val deduction">${fmt(r.ae)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">RQAP</span><span class="re-detail-val deduction">${fmt(r.rqap)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Impôt fédéral</span><span class="re-detail-val deduction">${fmt(r.fed)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Impôt Québec</span><span class="re-detail-val deduction">${fmt(r.qc)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Taux effectif</span><span class="re-detail-val">${r.taux.toFixed(1).replace('.',',')} %</span></div>
      <div class="re-detail-item" style="grid-column:1/3"><span class="re-detail-lbl">Net annuel</span><span class="re-detail-val net">${fmt(r.net)}</span></div>
      <div class="re-detail-item"><span class="re-detail-lbl">Net mensuel</span><span class="re-detail-val net">${fmt(r.net/12)}</span></div>
    </div></td>` : `<td colspan="6"><div style="padding:8px 14px;font-size:12px;color:var(--muted)">Calcul non disponible.</div></td>`;
    tbody.appendChild(trDetail);

    closeRevenuModal();
    updateReSidebar();
  }

  function reToggleDetail(btn) {
    const tr = btn.closest('tr');
    const detail = tr.nextElementSibling;
    if (!detail || !detail.classList.contains('re-detail-row')) return;
    const open = detail.style.display !== 'none';
    detail.style.display = open ? 'none' : '';
    const svg = btn.querySelector('svg');
    if (svg) svg.style.transform = open ? '' : 'rotate(180deg)';
  }

  function reDeleteRow(btn) {
    const tr = btn.closest('tr');
    const detail = tr.nextElementSibling;
    if (detail && detail.classList.contains('re-detail-row')) detail.remove();
    tr.remove();
    updateReSidebar();
  }

  function fmtMoney(n) {
    return n.toLocaleString('fr-CA', { maximumFractionDigits: 0 }) + ' $';
  }

  function updateEpargneSection() {
    const actifItems = document.querySelectorAll('#actifs-list [data-valeur]');
    const emptyDiv   = document.getElementById('epargne-empty');
    const tabsWrap   = document.getElementById('epargne-tabs-wrap');
    if (actifItems.length === 0) {
      if (emptyDiv)  emptyDiv.style.display  = '';
      if (tabsWrap)  tabsWrap.style.display  = 'none';
    } else {
      if (emptyDiv)  emptyDiv.style.display  = 'none';
      if (tabsWrap)  tabsWrap.style.display  = '';
      // Update tab labels
      const cn = getClientPrenom();
      const cj = getConjointPrenom();
      const tabC = document.getElementById('etab-client');
      const tabJ = document.getElementById('etab-conjoint');
      if (tabC) tabC.textContent = cn;
      if (tabJ) { tabJ.textContent = cj || 'Conjoint(e)'; tabJ.style.display = cj ? '' : 'none'; }
      // Build dropdowns
      epBuildDropdown('client');
      if (cj) epBuildDropdown('conjoint');
    }
  }
  function epBuildDropdown(who) {
    const ul = document.getElementById('ep-dd-' + who + '-list');
    if (!ul) return;
    ul.innerHTML = '';
    const actifs = document.querySelectorAll('#actifs-list [data-valeur][data-modal-type="placement"]');
    let count = 0;
    actifs.forEach(item => {
      const owner = item.dataset.owner || 'client';
      if (owner !== who && owner !== 'both') return;
      const type = item.dataset.aptype || '?';
      const name = who === 'client' ? getClientPrenom() : (getConjointPrenom() || 'Conjoint(e)');
      const label = type + ' (' + name + ')';
      const li = document.createElement('li');
      li.innerHTML = `<button class="legal-menu-item" onclick="openEpargneModal('${label.replace(/'/g,"\\'")}','${who}')">${label}</button>`;
      ul.appendChild(li);
      count++;
    });
    if (count === 0) {
      ul.innerHTML = '<li style="padding:10px 16px;color:var(--muted);font-size:13px">Aucun actif disponible</li>';
    }
  }
  function toggleEpargneDropdown(who) {
    const dd = document.getElementById('ep-dd-' + who);
    if (!dd) return;
    const wasOpen = dd.style.display === 'block';
    document.querySelectorAll('#ep-dd-client, #ep-dd-conjoint').forEach(d => d.style.display = 'none');
    if (!wasOpen) {
      epBuildDropdown(who);
      const btn = document.querySelector('#ep-btn-' + who + '-wrap button');
      if (btn) {
        const r = btn.getBoundingClientRect();
        dd.style.position = 'fixed';
        dd.style.top  = (r.bottom + 4) + 'px';
        dd.style.left = r.left + 'px';
        dd.style.zIndex = '9999';
      }
      dd.style.display = 'block';
    }
  }
  function switchEpargneTab(who, btn) {
    document.querySelectorAll('.re-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('epanel-client').style.display   = who === 'client'   ? '' : 'none';
    document.getElementById('epanel-conjoint').style.display = who === 'conjoint' ? '' : 'none';
  }
  let _epargneWho = 'client';
  function openEpargneModal(label, who) {
    document.getElementById('ep-dd-' + who).style.display = 'none';
    _epargneWho = who;
    document.getElementById('ep-modal-title').textContent = label;
    document.getElementById('modal-epargne').dataset.label = label;
    document.getElementById('ep-montant').value = '';
    document.getElementById('ep-frequence').value = '12';
    document.getElementById('ep-indexe-non').checked = true;
    document.getElementById('ep-taux-indexation').value = '0,00';
    document.getElementById('ep-debut-mois').value = '';
    document.getElementById('ep-debut-annee').value = '';
    document.getElementById('ep-fin-type').value = 'retirement';
    document.getElementById('modal-epargne').classList.add('open');
    document.getElementById('ep-montant').focus();
  }
  function closeEpargneModal() {
    document.getElementById('modal-epargne').classList.remove('open');
  }
  document.getElementById('modal-epargne')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-epargne')) closeEpargneModal();
  });
  function saveEpargne() {
    const label   = document.getElementById('modal-epargne').dataset.label || '';
    const montant = parseFloat((document.getElementById('ep-montant').value || '0').replace(',', '.')) || 0;
    const freqSel = document.getElementById('ep-frequence');
    const freqVal = freqSel.value;
    const freqTx  = freqSel.options[freqSel.selectedIndex].text;
    const list    = document.getElementById('ep-list-' + _epargneWho);
    if (!list) { closeEpargneModal(); return; }

    // Calcul du montant annuel
    const annuel = freqVal === 'onetime' ? montant : montant * parseFloat(freqVal);

    const item = document.createElement('div');
    item.dataset.montant = annuel; // montant annuel pour le flux monétaire
    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px';
    item.innerHTML = `
      <div>
        <div style="font-weight:600;color:var(--navy)">${label}</div>
        <div style="color:var(--muted);font-size:11px;margin-top:2px">${montant ? montant.toLocaleString('fr-CA') + ' $ · ' : ''}${freqTx}</div>
      </div>
      <div style="display:flex;gap:2px">
        <button onclick="editEpargneItem(this.parentElement.parentElement)" title="Modifier" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:15px;padding:2px 5px">✎</button>
        <button onclick="this.parentElement.parentElement.remove();updateReSidebar();" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;padding:0 4px">×</button>
      </div>`;
    list.appendChild(item);
    closeEpargneModal();
    updateReSidebar();
  }
  function editEpargneItem(item) { showToast('Modification disponible dans la version complète'); }

  const CELI_LIMITS = {
    2009:5000,2010:5000,2011:5000,2012:5000,
    2013:5500,2014:5500,2015:10000,
    2016:5500,2017:5500,2018:5500,
    2019:6000,2020:6000,2021:6000,2022:6000,
    2023:6500,2024:7000,2025:7000,2026:7000
  };
  function calcDroitsCeli(who) {
    const curY = new Date().getFullYear();
    const birthYear = parseInt(document.getElementById(who==='client'?'client-naissance-annee':'conjoint-naissance-annee')?.value) || null;
    const residYear = parseInt(document.getElementById(who==='client'?'client-canada-depuis':'conjoint-canada-depuis')?.value) || null;
    let startY = 2009;
    if (residYear && residYear > startY) startY = residYear;
    if (birthYear && birthYear + 18 > startY) startY = birthYear + 18;
    if (startY > curY) return 0;
    let room = 0;
    for (let y = startY; y <= curY; y++) room += CELI_LIMITS[y] || 7000;
    let celiActif = 0;
    document.querySelectorAll('#actifs-list [data-aptype="CELI"]').forEach(item => {
      if (item.dataset.owner === who || item.dataset.owner === 'both') celiActif += parseFloat(item.dataset.valeur) || 0;
    });
    return Math.max(0, room - celiActif);
  }
  function calcDroitsCeliapp(who) {
    const curY = new Date().getFullYear();
    let openingYear = null, celiappActif = 0;
    document.querySelectorAll('#actifs-list [data-aptype="CELIAPP"]').forEach(item => {
      if (item.dataset.owner === who || item.dataset.owner === 'both') {
        celiappActif += parseFloat(item.dataset.valeur) || 0;
        if (!openingYear) { try { const fd = JSON.parse(item.dataset.formJson||'{}'); if (fd.dateOuverture) openingYear = parseInt(fd.dateOuverture); } catch(e){} }
      }
    });
    if (!openingYear) return null;
    const yearsElig = Math.max(0, curY - openingYear + 1);
    const totalRoom = Math.min(yearsElig * 8000, 40000);
    return Math.max(0, totalRoom - celiappActif);
  }
  function calcDroitsReer(who) {
    const MAX_REER = 32490;
    let annuel = 0;
    document.querySelectorAll('#revenu-list tr').forEach(tr => {
      if (tr.dataset.owner === who || (!tr.dataset.owner && who === 'client')) annuel += parseFloat(tr.dataset.revenuAnnuel) || 0;
    });
    return Math.min(Math.floor(annuel * 0.18), MAX_REER);
  }
  function placDateOuvertureChange() {
    const val = document.getElementById('plac-date-ouverture')?.value.trim();
    const btn = document.getElementById('plac-save-btn');
    if (btn) btn.disabled = !val;
  }

  function updateReSidebar() {
    updateEpargneSection();
    const clientPrenom = getClientPrenom();
    const conjointPrenom = getConjointPrenom();

    // Update prefill row owner name
    const prefillOwner = document.getElementById('re-prefill-owner');
    if (prefillOwner) prefillOwner.textContent = clientPrenom;

    // Update droits de cotisation column headers
    const dcClientCol = document.getElementById('dc-client-col');
    if (dcClientCol) dcClientCol.textContent = clientPrenom;
    const dcConjointCol = document.getElementById('dc-conjoint-col');
    if (dcConjointCol) {
      dcConjointCol.textContent = conjointPrenom || '';
      dcConjointCol.style.display = conjointPrenom ? '' : 'none';
    }
    ['dc-conjoint-reer-cell','dc-conjoint-celi-cell','dc-conjoint-celiapp-cell'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = conjointPrenom ? '' : 'none';
    });

    // Sum annual revenue per owner
    let clientAnnuel = 0, conjointAnnuel = 0;
    document.querySelectorAll('#revenu-list tr').forEach(tr => {
      const val = parseFloat(tr.dataset.revenuAnnuel) || 0;
      if (tr.dataset.owner === 'conjoint') conjointAnnuel += val;
      else clientAnnuel += val;
    });

    const divisor = reTabMode === 'mensuel' ? 12 : 1;
    const freqLabel = reTabMode === 'mensuel' ? 'mensuel' : 'annuel';

    // Animate donut arc (circumference = 2π×32 ≈ 201)
    function setDonut(arcId, totalNum, maxVal) {
      const arc = document.getElementById(arcId);
      if (!arc) return;
      const c = 201;
      const ratio = maxVal > 0 ? Math.min(totalNum / maxVal, 1) : 0;
      arc.style.strokeDashoffset = c - ratio * c;
    }
    const allAnnuel = clientAnnuel + conjointAnnuel;

    // Somme épargne annuelle par propriétaire
    let clientEpargneAnnuel = 0, conjointEpargneAnnuel = 0;
    document.querySelectorAll('#ep-list-client [data-montant]').forEach(el => {
      clientEpargneAnnuel += parseFloat(el.dataset.montant) || 0;
    });
    document.querySelectorAll('#ep-list-conjoint [data-montant]').forEach(el => {
      conjointEpargneAnnuel += parseFloat(el.dataset.montant) || 0;
    });

    // Compute net via tax engine
    const clientImpot   = computeImpot(clientAnnuel);
    const clientNetAnnuel = clientImpot ? clientImpot.net : clientAnnuel;

    // Client sidebar
    document.getElementById('re-client-name').textContent = clientPrenom;
    const clientNetDisp  = clientNetAnnuel / divisor;
    const clientBrutDisp = clientAnnuel / divisor;
    const clientImpotDisp = clientImpot ? clientImpot.total / divisor : 0;
    const clientEpDisp   = clientEpargneAnnuel / divisor;
    const clientDepDisp  = Math.max(0, clientNetDisp - clientEpDisp);
    document.getElementById('re-client-revenu').textContent   = fmtMoney(clientBrutDisp);
    document.getElementById('re-client-impot').textContent    = fmtMoney(clientImpotDisp);
    document.getElementById('re-client-net').textContent      = fmtMoney(clientNetDisp);
    document.getElementById('re-client-epargne').textContent  = fmtMoney(clientEpDisp);
    document.getElementById('re-client-depenses').textContent = fmtMoney(clientDepDisp);
    document.getElementById('re-client-total-label').textContent = fmtMoney(clientNetDisp);
    document.getElementById('re-client-freq-label').textContent  = freqLabel;
    setDonut('re-client-donut-arc', clientNetAnnuel, clientNetAnnuel || 1);

    // Conjoint sidebar
    const conjBlock = document.getElementById('re-conjoint-block');
    if (conjBlock) {
      conjBlock.style.display = conjointPrenom ? '' : 'none';
      if (conjointPrenom) {
        const conjImpot     = computeImpot(conjointAnnuel);
        const conjNetAnnuel = conjImpot ? conjImpot.net : conjointAnnuel;
        document.getElementById('re-conjoint-name').textContent = conjointPrenom;
        const conjNetDisp   = conjNetAnnuel / divisor;
        const conjBrutDisp  = conjointAnnuel / divisor;
        const conjImpotDisp = conjImpot ? conjImpot.total / divisor : 0;
        const conjEpDisp    = conjointEpargneAnnuel / divisor;
        const conjDepDisp   = Math.max(0, conjNetDisp - conjEpDisp);
        document.getElementById('re-conjoint-revenu').textContent   = fmtMoney(conjBrutDisp);
        document.getElementById('re-conjoint-impot').textContent    = fmtMoney(conjImpotDisp);
        document.getElementById('re-conjoint-net').textContent      = fmtMoney(conjNetDisp);
        document.getElementById('re-conjoint-epargne').textContent  = fmtMoney(conjEpDisp);
        document.getElementById('re-conjoint-depenses').textContent = fmtMoney(conjDepDisp);
        document.getElementById('re-conjoint-total-label').textContent = fmtMoney(conjNetDisp);
        document.getElementById('re-conjoint-freq-label').textContent  = freqLabel;
        const allNet = clientNetAnnuel + conjNetAnnuel;
        setDonut('re-conjoint-donut-arc', conjNetAnnuel, allNet || 1);
        setDonut('re-client-donut-arc',   clientNetAnnuel, allNet || 1);
      }
    }

    // Auto-calcul droits de cotisation
    ['client','conjoint'].forEach(who => {
      const sfx = who;
      const reerInput    = document.getElementById('dc-' + sfx + '-reer');
      const celiInput    = document.getElementById('dc-' + sfx + '-celi');
      const celiappInput = document.getElementById('dc-' + sfx + '-celiapp');
      if (who === 'conjoint' && !conjointPrenom) return;
      if (reerInput && !reerInput.dataset.manualOverride)    reerInput.value    = calcDroitsReer(who).toLocaleString('fr-CA');
      if (celiInput && !celiInput.dataset.manualOverride)    celiInput.value    = calcDroitsCeli(who).toLocaleString('fr-CA');
      const celiappVal = calcDroitsCeliapp(who);
      if (celiappInput && !celiappInput.dataset.manualOverride && celiappVal !== null) celiappInput.value = celiappVal.toLocaleString('fr-CA');
    });
  }

  // ── PAGE ACCUEIL ──────────────────────────────────
  function demarrerABF() {
    document.getElementById('page-accueil').style.display = 'none';
  }
  // ── Gestion de l'impôt ────────────────────────
  function openImpotModal() {
    impotRenderParams();
    document.getElementById('modal-impot').classList.add('open');
  }
  function openImpotModalFor() { openImpotModal(); }
  function closeImpotModal() {
    document.getElementById('modal-impot').classList.remove('open');
  }

  // ── Hypothèses ────────────────────────────────
  let hypotheses = { evClient:94, evConj:96 };

  function openHypothesesModal() {
    // Mirror Valeurs par défaut data → Hypothèses inputs
    const copy = (from, to) => { const el = document.getElementById(from); if(el) document.getElementById(to).value = el.value; };
    copy('vd-inflation',   'hyp-inflation');
    copy('vd-p-prudent',   'hyp-port-prudent');
    copy('vd-p-modere',    'hyp-port-modere');
    copy('vd-p-equilibre', 'hyp-port-equilibre');
    copy('vd-p-croissance','hyp-port-croissance');
    copy('vd-p-audacieux', 'hyp-port-audacieux');
    // Espérance de vie (local)
    document.getElementById('hyp-ev-client').value = String(hypotheses.evClient);
    document.getElementById('hyp-ev-conj').value   = String(hypotheses.evConj);
    // Labels
    const cp = getClientPrenom(), jp = getConjointPrenom();
    const evClientLbl = document.getElementById('hyp-ev-client-label');
    const evConjWrap  = document.getElementById('hyp-ev-conj-wrap');
    const evConjLbl   = document.getElementById('hyp-ev-conj-label');
    if (evClientLbl) evClientLbl.textContent = cp || 'Client';
    if (evConjWrap)  evConjWrap.style.display = jp ? '' : 'none';
    if (evConjLbl)   evConjLbl.textContent = jp || 'Conjoint(e)';
    document.getElementById('modal-hypotheses').style.display = 'flex';
  }
  function closeHypothesesModal() {
    document.getElementById('modal-hypotheses').style.display = 'none';
  }
  function resetHypotheses() {
    // Reset vd-* fields to defaults then reopen
    const defaults = {'vd-inflation':'2,10','vd-p-prudent':'3,00','vd-p-modere':'3,30','vd-p-equilibre':'3,70','vd-p-croissance':'4,00','vd-p-audacieux':'4,30'};
    Object.entries(defaults).forEach(([id, v]) => { const el = document.getElementById(id); if(el) el.value = v; });
    hypotheses.evClient = 94; hypotheses.evConj = 96;
    openHypothesesModal();
  }
  function saveHypotheses() {
    // Write back to Valeurs par défaut fields
    const copy = (from, to) => { const el = document.getElementById(from); if(el) document.getElementById(to).value = el.value; };
    copy('hyp-inflation',    'vd-inflation');
    copy('hyp-port-prudent', 'vd-p-prudent');
    copy('hyp-port-modere',  'vd-p-modere');
    copy('hyp-port-equilibre','vd-p-equilibre');
    copy('hyp-port-croissance','vd-p-croissance');
    copy('hyp-port-audacieux','vd-p-audacieux');
    // Espérance de vie (local)
    hypotheses.evClient = parseInt(document.getElementById('hyp-ev-client')?.value||'94') || 94;
    hypotheses.evConj   = parseInt(document.getElementById('hyp-ev-conj')?.value||'96')   || 96;
    closeHypothesesModal();
    showToast('Hypothèses enregistrées');
  }
  document.getElementById('modal-hypotheses')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-hypotheses')) closeHypothesesModal();
  });

  // ── Rente conjoint survivant ───────────────────
  let rrqRenteParams = {
    regime: 'rrq',
    annee: 2026,
    m45SansEnfant: 719.50,
    m45AvecEnfant: 1129.95,
    m45Invalide:   1134.61,
    de45a65:       1173.58,
    de65plus:      881.48,
    cppFixedPortion: 217.83
  };
  function rcToggleRegime() {
    const isCpp = document.getElementById('rc-regime-cpp')?.checked;
    document.getElementById('rc-rrq-section').style.display = isCpp ? 'none' : '';
    document.getElementById('rc-rrq-header').style.display  = isCpp ? 'none' : '';
    document.getElementById('rc-cpp-section').style.display = isCpp ? '' : 'none';
  }
  function rcUpdatePanelHelpers() {
    // Show/hide invalide vs rente-defunt based on regime
    const isCpp = rrqRenteParams.regime === 'cpp';
    ['c','j'].forEach(sfx => {
      const invWrap = document.getElementById(`deces-invalide-${sfx}-wrap`);
      const defWrap = document.getElementById(`deces-rente-defunt-${sfx}-wrap`);
      if (invWrap) invWrap.style.display = isCpp ? 'none' : '';
      if (defWrap) defWrap.style.display = isCpp ? '' : 'none';
    });
  }
  function openRenteConjModal() {
    const isCpp = rrqRenteParams.regime === 'cpp';
    document.getElementById('rc-regime-cpp').checked = isCpp;
    document.getElementById('rc-regime-rrq').checked = !isCpp;
    rcToggleRegime();
    document.getElementById('rc-annee').value = rrqRenteParams.annee;
    const fmt = v => v.toLocaleString('fr-CA', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('rc-m45-sans').value = fmt(rrqRenteParams.m45SansEnfant);
    document.getElementById('rc-m45-avec').value = fmt(rrqRenteParams.m45AvecEnfant);
    document.getElementById('rc-m45-inv').value  = fmt(rrqRenteParams.m45Invalide);
    document.getElementById('rc-45-65').value    = fmt(rrqRenteParams.de45a65);
    document.getElementById('rc-65plus').value   = fmt(rrqRenteParams.de65plus);
    document.getElementById('rc-cpp-fixed').value = fmt(rrqRenteParams.cppFixedPortion);
    document.getElementById('modal-rente-conj').classList.add('open');
  }
  function closeRenteConjModal() {
    document.getElementById('modal-rente-conj').classList.remove('open');
  }
  function saveRenteConjModal() {
    const parse = id => parseFloat((document.getElementById(id)?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    rrqRenteParams.regime        = document.querySelector('input[name="rc-regime"]:checked')?.value || 'rrq';
    rrqRenteParams.annee         = parseInt(document.getElementById('rc-annee')?.value) || 2026;
    rrqRenteParams.m45SansEnfant = parse('rc-m45-sans');
    rrqRenteParams.m45AvecEnfant = parse('rc-m45-avec');
    rrqRenteParams.m45Invalide   = parse('rc-m45-inv');
    rrqRenteParams.de45a65       = parse('rc-45-65');
    rrqRenteParams.de65plus      = parse('rc-65plus');
    rrqRenteParams.cppFixedPortion = parse('rc-cpp-fixed');
    rcUpdatePanelHelpers();
    closeRenteConjModal();
    showToast('Paramètres rente mis à jour');
  }
  function rcReset() {
    rrqRenteParams = { regime:'rrq', annee:2026, m45SansEnfant:719.50, m45AvecEnfant:1129.95, m45Invalide:1134.61, de45a65:1173.58, de65plus:881.48, cppFixedPortion:217.83 };
    openRenteConjModal();
  }
  // Vérifie si le survivant (owner = 'client' ou 'conjoint') a des enfants à charge
  function survivorHasChildren(survivorOwner) {
    let found = false;
    document.querySelectorAll('#enfants-list .enfant-item[data-charge]').forEach(el => {
      const c = el.dataset.charge;
      if (c === survivorOwner || c === 'both') found = true;
    });
    return found;
  }

  function resetRenteConj(sfx) {
    const clientBirthYear = parseInt(document.getElementById('client-naissance-annee')?.value) || 0;
    const conjBirthYear   = parseInt(document.getElementById('conjoint-naissance-annee')?.value) || 0;
    const survivorOwner     = sfx === 'c' ? 'conjoint' : 'client';
    const survivorBirthYear = sfx === 'c' ? conjBirthYear : clientBirthYear;
    const hasChildren  = survivorHasChildren(survivorOwner);
    const isInvalide   = document.getElementById(`deces-invalide-${sfx}`)?.checked || false;
    const defuntRente  = parseFloat((document.getElementById(`deces-rente-defunt-${sfx}`)?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
    const sugg = getRenteConjSuggestion(survivorBirthYear, hasChildren, isInvalide, defuntRente) * 12;
    const field = document.getElementById(`deces-rente-conjoint-${sfx}`);
    if (field) { field.value = Math.round(sugg).toLocaleString('fr-CA'); decesCalc(); }
  }

  // Calcule le montant mensuel de rente suggéré (RRQ ou CPP)
  // deceasedMonthlyPension = rente de retraite mensuelle du défunt (pour CPP)
  // isInvalide = survivant invalide <45 ans (pour RRQ)
  function getRenteConjSuggestion(birthYear, hasChildren, isInvalide, deceasedMonthlyPension) {
    if (!birthYear) return 0;
    const age = new Date().getFullYear() - birthYear;
    if (rrqRenteParams.regime === 'cpp') {
      const d = deceasedMonthlyPension || 0;
      if (age >= 65) return 0.60 * d;
      return rrqRenteParams.cppFixedPortion + 0.375 * d;
    }
    // RRQ
    if (age >= 65) return rrqRenteParams.de65plus;
    if (age >= 45) return rrqRenteParams.de45a65;
    if (isInvalide) return rrqRenteParams.m45Invalide;
    return hasChildren ? rrqRenteParams.m45AvecEnfant : rrqRenteParams.m45SansEnfant;
  }
  function impotRenderParams() {
    const p = fiscalParams;
    const fmtN = n => n === Infinity ? '∞' : String(n);
    // Federal brackets
    const fedTbody = document.getElementById('impot-fed-brackets');
    if (fedTbody) {
      fedTbody.innerHTML = '';
      p.fed.brackets.forEach((b, i) => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `<td style="padding:5px 10px">
          <input class="form-input" id="fp-fed-max-${i}" type="text" value="${b.max === Infinity ? '' : b.max}"
            placeholder="∞ (dernier palier)" style="font-size:12px;padding:5px 8px;width:160px"/>
        </td><td style="padding:5px 10px">
          <input class="form-input" id="fp-fed-rate-${i}" type="text" value="${b.rate}"
            style="font-size:12px;padding:5px 8px;width:80px"/>
        </td>`;
        fedTbody.appendChild(tr);
      });
    }
    // Federal base amounts
    const setVal = (id, v) => { const el = document.getElementById(id); if (el) el.value = v; };
    setVal('fp-fed-baseMax',        p.fed.baseMax);
    setVal('fp-fed-baseMin',        p.fed.baseMin);
    setVal('fp-fed-baseThreshLow',  p.fed.baseThreshLow);
    setVal('fp-fed-baseThreshHigh', p.fed.baseThreshHigh);
    setVal('fp-fed-creditRate',     p.fed.creditRate);
    // Quebec brackets
    const qcTbody = document.getElementById('impot-qc-brackets');
    if (qcTbody) {
      qcTbody.innerHTML = '';
      p.qc.brackets.forEach((b, i) => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `<td style="padding:5px 10px">
          <input class="form-input" id="fp-qc-max-${i}" type="text" value="${b.max === Infinity ? '' : b.max}"
            placeholder="∞ (dernier palier)" style="font-size:12px;padding:5px 8px;width:160px"/>
        </td><td style="padding:5px 10px">
          <input class="form-input" id="fp-qc-rate-${i}" type="text" value="${b.rate}"
            style="font-size:12px;padding:5px 8px;width:80px"/>
        </td>`;
        qcTbody.appendChild(tr);
      });
    }
    // Quebec base
    setVal('fp-qc-base',       p.qc.base);
    setVal('fp-qc-creditRate', p.qc.creditRate);
    // Cotisations
    setVal('fp-rrq-exemption', p.rrq.exemption);
    setVal('fp-rrq-ceil1',     p.rrq.ceil1);
    setVal('fp-rrq-rate1',     p.rrq.rate1);
    setVal('fp-rrq-ceil2',     p.rrq.ceil2);
    setVal('fp-rrq-rate2',     p.rrq.rate2);
    setVal('fp-ae-ceil',       p.ae.ceil);
    setVal('fp-ae-rate',       p.ae.rate);
    setVal('fp-rqap-ceil',     p.rqap.ceil);
    setVal('fp-rqap-rate',     p.rqap.rate);
  }
  function impotSaveParams() {
    const getN = id => parseFloat((document.getElementById(id)?.value || '').replace(/\s/g,'').replace(',','.')) || 0;
    const p = fiscalParams;
    // Federal brackets
    p.fed.brackets.forEach((b, i) => {
      const maxVal = document.getElementById(`fp-fed-max-${i}`)?.value.trim();
      b.max  = (maxVal === '' || maxVal === '∞') ? Infinity : parseFloat(maxVal) || b.max;
      b.rate = parseFloat(document.getElementById(`fp-fed-rate-${i}`)?.value) || b.rate;
    });
    p.fed.baseMax        = getN('fp-fed-baseMax')        || p.fed.baseMax;
    p.fed.baseMin        = getN('fp-fed-baseMin')        || p.fed.baseMin;
    p.fed.baseThreshLow  = getN('fp-fed-baseThreshLow')  || p.fed.baseThreshLow;
    p.fed.baseThreshHigh = getN('fp-fed-baseThreshHigh') || p.fed.baseThreshHigh;
    p.fed.creditRate     = getN('fp-fed-creditRate')     || p.fed.creditRate;
    // Quebec brackets
    p.qc.brackets.forEach((b, i) => {
      const maxVal = document.getElementById(`fp-qc-max-${i}`)?.value.trim();
      b.max  = (maxVal === '' || maxVal === '∞') ? Infinity : parseFloat(maxVal) || b.max;
      b.rate = parseFloat(document.getElementById(`fp-qc-rate-${i}`)?.value) || b.rate;
    });
    p.qc.base       = getN('fp-qc-base')       || p.qc.base;
    p.qc.creditRate = getN('fp-qc-creditRate') || p.qc.creditRate;
    // Cotisations
    p.rrq.exemption = getN('fp-rrq-exemption');
    p.rrq.ceil1     = getN('fp-rrq-ceil1')  || p.rrq.ceil1;
    p.rrq.rate1     = getN('fp-rrq-rate1')  || p.rrq.rate1;
    p.rrq.ceil2     = getN('fp-rrq-ceil2')  || p.rrq.ceil2;
    p.rrq.rate2     = getN('fp-rrq-rate2')  || p.rrq.rate2;
    p.ae.ceil       = getN('fp-ae-ceil')    || p.ae.ceil;
    p.ae.rate       = getN('fp-ae-rate')    || p.ae.rate;
    p.rqap.ceil     = getN('fp-rqap-ceil')  || p.rqap.ceil;
    p.rqap.rate     = getN('fp-rqap-rate')  || p.rqap.rate;
    updateReSidebar();
    closeImpotModal();
    showToast('Paramètres fiscaux mis à jour');
  }
  function impotResetParams() {
    fiscalParams = JSON.parse(JSON.stringify(FISCAL_2026));
    fiscalParams.fed.brackets[4].max = Infinity;
    fiscalParams.qc.brackets[3].max  = Infinity;
    impotRenderParams();
  }
  // ── Paramètres fiscaux (modifiables via Gestion de l'impôt) ──
  const FISCAL_2026 = {
    fed: {
      brackets: [
        { max: 58523,   rate: 14   },
        { max: 117045,  rate: 20.5 },
        { max: 181440,  rate: 26   },
        { max: 258482,  rate: 29   },
        { max: Infinity, rate: 33  }
      ],
      baseMax: 16452, baseMin: 14829,
      baseThreshLow: 173205, baseThreshHigh: 235675,
      creditRate: 15
    },
    qc: {
      brackets: [
        { max: 54345,   rate: 14   },
        { max: 108680,  rate: 19   },
        { max: 132245,  rate: 24   },
        { max: Infinity, rate: 25.75 }
      ],
      base: 18952, creditRate: 14
    },
    rrq:  { exemption: 3500, ceil1: 74600, rate1: 5.4, ceil2: 85000, rate2: 1.0 },
    ae:   { ceil: 68900,  rate: 1.30  },
    rqap: { ceil: 103000, rate: 0.430 }
  };
  let fiscalParams = JSON.parse(JSON.stringify(FISCAL_2026));
  // Fix Infinity after JSON round-trip
  fiscalParams.fed.brackets[4].max = Infinity;
  fiscalParams.qc.brackets[3].max  = Infinity;

  function computeImpot(brut) {
    if (brut <= 0) return null;
    const p = fiscalParams;
    // RRQ
    const rrq = Math.max(0, Math.min(brut, p.rrq.ceil1) - p.rrq.exemption) * (p.rrq.rate1 / 100)
              + Math.max(0, Math.min(brut, p.rrq.ceil2) - p.rrq.ceil1)     * (p.rrq.rate2 / 100);
    // AE
    const ae   = Math.min(brut, p.ae.ceil)   * (p.ae.rate   / 100);
    // RQAP
    const rqap = Math.min(brut, p.rqap.ceil) * (p.rqap.rate / 100);
    // Fédéral
    let fed = 0, prev = 0;
    for (const b of p.fed.brackets) {
      const slice = Math.min(brut, b.max === Infinity ? brut : b.max) - prev;
      if (slice <= 0) break;
      fed += slice * (b.rate / 100);
      prev = b.max === Infinity ? brut : b.max;
      if (b.max === Infinity || brut <= b.max) break;
    }
    const baseFed = brut <= p.fed.baseThreshLow  ? p.fed.baseMax
                  : brut >= p.fed.baseThreshHigh ? p.fed.baseMin
                  : p.fed.baseMax - (brut - p.fed.baseThreshLow) / (p.fed.baseThreshHigh - p.fed.baseThreshLow) * (p.fed.baseMax - p.fed.baseMin);
    fed = Math.max(0, fed - baseFed * (p.fed.creditRate / 100));
    // Québec (cotisations déductibles du revenu imposable)
    const qcRev = brut - rrq - ae - rqap;
    let qc = 0; prev = 0;
    for (const b of p.qc.brackets) {
      const slice = Math.min(qcRev, b.max === Infinity ? qcRev : b.max) - prev;
      if (slice <= 0) break;
      qc += slice * (b.rate / 100);
      prev = b.max === Infinity ? qcRev : b.max;
      if (b.max === Infinity || qcRev <= b.max) break;
    }
    qc = Math.max(0, qc - p.qc.base * (p.qc.creditRate / 100));
    const total = rrq + ae + rqap + fed + qc;
    return { rrq, ae, rqap, fed, qc, total, net: brut - total, taux: total / brut * 100 };
  }

  // ── Profil ────────────────────────────────────
  function openProfilModal() {
    document.getElementById('modal-profil').classList.add('open');
  }
  function closeProfilModal() {
    document.getElementById('modal-profil').classList.remove('open');
  }
  function saveProfilModal() {
    closeProfilModal();
    showToast('Profil enregistré');
  }
  function openValeursDefaut() {
    document.getElementById('page-valeurs-defaut').style.display = 'block';
  }
  function closeValeursDefaut() {
    document.getElementById('page-valeurs-defaut').style.display = 'none';
  }
  function toggleAccordion(header) {
    const body = header.nextElementSibling;
    body.classList.toggle('open');
  }
  document.getElementById('modal-profil')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-profil')) closeProfilModal();
  });
  document.getElementById('modal-impot')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-impot')) closeImpotModal();
  });

  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  /* ── Fonds d'urgence ── */
  const FU_ELIGIBLE_TYPES = ['Compte bancaire','CELI','Non enregistré'];

  function fuTypeChange() {
    const type = document.querySelector('input[name="fu-type"]:checked')?.value || 'income';
    document.getElementById('fu-row-income').style.display   = type === 'income'   ? 'flex' : 'none';
    document.getElementById('fu-row-expenses').style.display = type === 'expenses' ? 'flex' : 'none';
    document.getElementById('fu-row-amount').style.display   = type === 'amount'   ? 'flex' : 'none';
    fuCalc();
  }

  function fuRenderActifs() {
    const body = document.getElementById('fu-actifs-body');
    if (!body) return;
    const ownerLabel = o => {
      if (o === 'conjoint') return getConjointPrenom() || 'Conjoint';
      if (o === 'both') return getClientPrenom() + ' & ' + (getConjointPrenom() || 'Conjoint');
      return getClientPrenom();
    };
    const items = [];
    document.querySelectorAll('#actifs-list [data-aptype]').forEach(el => {
      const type = el.dataset.aptype || '';
      if (!FU_ELIGIBLE_TYPES.includes(type)) return;
      const valeur = parseFloat(el.dataset.valeur) || 0;
      items.push({ nom: type, valeur, owner: ownerLabel(el.dataset.owner || 'client') });
    });
    if (items.length === 0) {
      body.style.padding = '';
      body.innerHTML = '<p style="font-size:13px;color:var(--muted)">Aucun compte bancaire, CELI ou placement non enregistré disponible. <a href="#" onclick="goTo(\'actifs-passifs\',document.querySelectorAll(\'.nav-item\')[2]);return false;">Ajouter un actif.</a></p>';
      return;
    }
    body.style.padding = '0';
    body.innerHTML = `
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr style="background:#f8f9fb;border-bottom:2px solid var(--border)">
            <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Description</th>
            <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Propriétaire</th>
            <th style="padding:10px 14px;text-align:right;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Valeur</th>
            <th style="padding:10px 14px;font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px">Montant alloué</th>
          </tr>
        </thead>
        <tbody>
          ${items.map(it => `
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 14px">${it.nom}</td>
            <td style="padding:10px 14px;color:var(--muted)">${it.owner}</td>
            <td style="padding:10px 14px;text-align:right;font-weight:600">${fmtMoney(it.valeur)}</td>
            <td style="padding:10px 14px">
              <div style="display:flex;align-items:center;gap:6px">
                <div class="input-sfx" style="flex:1;max-width:140px">
                  <input class="form-input fu-alloc-input" type="text" value="0" oninput="fuCalc()" style="padding-right:28px"/>
                  <span class="sfx">$</span>
                </div>
                <button onclick="this.closest('tr').querySelector('.fu-alloc-input').value='0';fuCalc()"
                  style="background:none;border:none;cursor:pointer;color:var(--muted);flex-shrink:0;padding:2px;line-height:1;display:flex;align-items:center" title="Effacer">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 24" width="18" height="18" fill="currentColor">
                    <path d="M12 0q-2.484 0-4.676 0.938t-3.82 2.566-2.566 3.82-0.938 4.676 0.938 4.676 2.566 3.82 3.82 2.566 4.676 0.938 4.676-0.938 3.82-2.566 2.566-3.82 0.938-4.676-0.938-4.676-2.566-3.82-3.82-2.566-4.676-0.938zM17.93 15.82l-2.109 2.109-3.82-3.82-3.82 3.82-2.109-2.109 3.82-3.82-3.82-3.82 2.109-2.109 3.82 3.82 3.82-3.82 2.109 2.109-3.82 3.82 3.82 3.82z"/>
                  </svg>
                </button>
              </div>
            </td>
          </tr>`).join('')}
        </tbody>
      </table>`;
    fuCalc();
  }

  function fuCalc() {
    const type = document.querySelector('input[name="fu-type"]:checked')?.value || 'income';
    let objectif = 0;

    if (type === 'none') {
      return;
    } else if (type === 'amount') {
      objectif = parseFloat((document.getElementById('fu-montant-fixe')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    } else if (type === 'income') {
      const months = parseFloat(document.getElementById('fu-months')?.value || '3') || 3;
      // Revenu net : somme du net par propriétaire (via computeImpot)
      const annuelNet = getRevenusByOwner('client', true).total + getRevenusByOwner('conjoint', true).total;
      const base = annuelNet / 12;
      objectif = months * base;
      const cible = document.getElementById('fu-montant-cible-income');
      if (cible) cible.textContent = fmtMoney(objectif);
    } else {
      // expenses: montant mensuel saisi × mois saisis
      const depMensuel = parseFloat((document.getElementById('fu-dep-mensuel')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
      const months = parseFloat(document.getElementById('fu-months-dep')?.value || '3') || 3;
      objectif = depMensuel * months;
      const cible = document.getElementById('fu-montant-cible-dep');
      if (cible) cible.textContent = fmtMoney(objectif);
    }

    // Somme des montants alloués saisis
    let actifsTotal = 0;
    document.querySelectorAll('#fu-actifs-body .fu-alloc-input').forEach(inp => {
      actifsTotal += parseFloat((inp.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    });

    const marge = parseFloat((document.getElementById('fu-marge')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const ecart = (actifsTotal + marge) - objectif;

    const card = document.getElementById('fu-resume-card');
    if (card) card.style.display = 'block';
    const el = (id) => document.getElementById(id);
    if (el('fu-r-objectif')) el('fu-r-objectif').textContent = fmtMoney(objectif);
    if (el('fu-r-actifs'))   el('fu-r-actifs').textContent   = fmtMoney(actifsTotal);
    if (el('fu-r-marge'))    el('fu-r-marge').textContent    = fmtMoney(marge);
    if (el('fu-r-ecart')) {
      el('fu-r-ecart').textContent = (ecart >= 0 ? '+' : '') + fmtMoney(ecart);
      el('fu-r-ecart').style.color = ecart >= 0 ? '#22c55e' : '#ef4444';
    }
    // Barre de couverture : rouge → or → vert
    const pct = objectif > 0 ? Math.min(100, Math.round((actifsTotal + marge) / objectif * 100)) : 0;
    const barColor = pct >= 100 ? '#22c55e' : pct >= 50 ? 'var(--gold)' : '#ef4444';
    const pctEl = el('fu-r-pct'), barEl = el('fu-r-bar');
    if (pctEl) pctEl.textContent = pct + ' %';
    if (barEl) { barEl.style.width = pct + '%'; barEl.style.background = barColor; }
  }

  /* ── DÉCÈS ── */
  let _decesAvItems = [];
  const ASSUREURS_LIST = []; // populated via select
  let _decesDepActiveTab = 'client';

  function switchDecesDepTab(who, btn) {
    _decesDepActiveTab = who;
    document.querySelectorAll('.deces-person-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('deces-dep-list').style.display = who === 'client' ? '' : 'none';
    document.getElementById('deces-dep-list-conjoint').style.display = who === 'conjoint' ? '' : 'none';
    // Update dep header
    const hdr = document.getElementById('deces-dep-header');
    if (hdr) {
      const prenom = who === 'client'
        ? (document.getElementById('client-prenom')?.value || 'le client')
        : (document.getElementById('conjoint-prenom')?.value || 'le conjoint');
      hdr.textContent = `Dépenses prévues si ${prenom} décède`;
    }
  }

  let _decesRrActiveTab = 'c';
  function switchDecesRrTab(sfx, btn) {
    _decesRrActiveTab = sfx;
    document.querySelectorAll('.deces-rr-person-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('deces-rr-panel-c').style.display = sfx === 'c' ? '' : 'none';
    document.getElementById('deces-rr-panel-j').style.display = sfx === 'j' ? '' : 'none';
    // Refresh panel-j's "Revenu du conjoint" from profile when switching to it
    if (sfx === 'j') {
      const dispoJ = document.getElementById('deces-revenu-dispo-j');
      if (dispoJ) {
        let total = 0;
        document.querySelectorAll('#revenu-list tr[data-revenu-annuel]').forEach(tr => {
          total += parseFloat(tr.dataset.revenuAnnuel) || 0;
        });
        dispoJ.value = total > 0 ? total.toLocaleString('fr-CA') : '0';
        decesCalc();
      }
    }
  }

  function decesInit() {
    // Update header with client name
    const clientPrenom = document.getElementById('client-prenom')?.value || 'le client';
    const hdr = document.getElementById('deces-dep-header');
    if (hdr) hdr.textContent = `Dépenses prévues si ${clientPrenom} décède`;

    // RRQ inputs
    const rrqBody = document.getElementById('deces-rrq-body');
    if (rrqBody) {
      const conjointChecked = document.getElementById('conjoint')?.checked;
      const conjointPrenom  = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
      let html = '';
      if (conjointChecked) {
        html = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          <div class="form-group" style="margin:0">
            <label class="form-label">${clientPrenom}</label>
            <div class="input-sfx"><input class="form-input" id="deces-rrq-client" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
          </div>
          <div class="form-group" style="margin:0">
            <label class="form-label">${conjointPrenom}</label>
            <div class="input-sfx"><input class="form-input" id="deces-rrq-conjoint" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
          </div>
        </div>`;
      } else {
        html = `<div class="form-group" style="max-width:220px">
          <label class="form-label">${clientPrenom}</label>
          <div class="input-sfx"><input class="form-input" id="deces-rrq-client" type="text" value="0" oninput="decesCalc()"/><span class="sfx">$</span></div>
        </div>`;
      }
      rrqBody.innerHTML = html;
      // Auto-remplir la prestation de décès RRQ (montant fixe maximal = 2 500 $)
      const rrqC = document.getElementById('deces-rrq-client');
      const rrqJ = document.getElementById('deces-rrq-conjoint');
      if (rrqC && !parseFloat(rrqC.value)) rrqC.value = '2500';
      if (rrqJ && !parseFloat(rrqJ.value)) rrqJ.value = '2500';
    }

    // Pre-populate frais funéraires if list empty
    const depList = document.getElementById('deces-dep-list');
    if (depList && depList.children.length === 0) {
      _decesDepActiveTab = 'client';
      addDecesDep('Frais funéraires', 25000);
    }

    // Couple-mode adaptations
    const isCouple = document.getElementById('conjoint')?.checked;
    if (isCouple) {
      const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
      const clientPrenom2 = document.getElementById('client-prenom')?.value || 'Client';

      // Show dep tabs with real names
      const tabsEl = document.getElementById('deces-dep-tabs');
      if (tabsEl) {
        tabsEl.style.display = 'flex';
        const tabClient = document.getElementById('deces-dep-tab-client');
        const tabConj = document.getElementById('deces-dep-tab-conjoint');
        if (tabClient) tabClient.textContent = clientPrenom2.toUpperCase();
        if (tabConj) tabConj.textContent = conjointPrenom.toUpperCase();
      }

      // Pre-populate conjoint dep list if empty
      const conjList = document.getElementById('deces-dep-list-conjoint');
      if (conjList && conjList.children.length === 0) {
        _decesDepActiveTab = 'conjoint';
        addDecesDep('Frais funéraires', 25000);
        _decesDepActiveTab = 'client';
      }

      // Show Familial radio and select it by default
      const famPill = document.getElementById('deces-rr-familial-pill');
      if (famPill) {
        famPill.style.display = '';
        const famRadio = famPill.querySelector('input[type=radio]');
        if (famRadio) famRadio.checked = true;
      }

      // Use tabs for RR panels in couple mode
      const rrTabs = document.getElementById('deces-rr-person-tabs');
      if (rrTabs) rrTabs.style.display = 'flex';
      const tabClient = document.getElementById('deces-rr-tab-client');
      const tabConj   = document.getElementById('deces-rr-tab-conjoint');
      if (tabClient) { tabClient.textContent = clientPrenom2.toUpperCase(); tabClient.classList.add('active'); }
      if (tabConj)   { tabConj.textContent   = conjointPrenom.toUpperCase(); tabConj.classList.remove('active'); }
      const panelC = document.getElementById('deces-rr-panel-c');
      const panelJ = document.getElementById('deces-rr-panel-j');
      if (panelC) panelC.style.display = '';   // show client tab by default
      if (panelJ) panelJ.style.display = 'none'; // hide conjoint tab
      const rrForm = document.getElementById('deces-rr-form');
      if (rrForm) { rrForm.style.display = ''; rrForm.style.gridTemplateColumns = ''; rrForm.style.gap = ''; rrForm.style.alignItems = ''; }
      // Hide the "Si X décède" title banners (tabs already show context)
      const titleC = document.getElementById('deces-rr-panel-c-title');
      const titleJ = document.getElementById('deces-rr-panel-j-title');
      if (titleC) titleC.style.display = 'none';
      if (titleJ) titleJ.style.display = 'none';
      // Restore panel-j labels to visible (each tab is independent)
      ['deces-lbl-j-actuels','deces-lbl-j-vises','deces-lbl-j-dispos'].forEach(id => {
        const el = document.getElementById(id); if (el) el.style.visibility = '';
      });

      // Update beneficiaire labels in both panels
      const benLabelC = document.getElementById('deces-rr-beneficiaire-label-c');
      if (benLabelC) benLabelC.textContent = 'Le conjoint survivant désire recevoir';
      const benLabelJ = document.getElementById('deces-rr-beneficiaire-label-j');
      if (benLabelJ) benLabelJ.textContent = 'Le conjoint survivant désire recevoir';
    } else {
      // Reset couple elements hidden
      const tabsEl = document.getElementById('deces-dep-tabs');
      if (tabsEl) tabsEl.style.display = 'none';
      const famPill = document.getElementById('deces-rr-familial-pill');
      if (famPill) famPill.style.display = 'none';
      const rrTabs = document.getElementById('deces-rr-person-tabs');
      if (rrTabs) rrTabs.style.display = 'none';
      const rrFormSolo = document.getElementById('deces-rr-form');
      if (rrFormSolo) { rrFormSolo.style.display = ''; rrFormSolo.style.gridTemplateColumns = ''; rrFormSolo.style.gap = ''; rrFormSolo.style.alignItems = ''; }
      const titleCSolo = document.getElementById('deces-rr-panel-c-title');
      if (titleCSolo) titleCSolo.style.display = 'none';
      const benLabelC = document.getElementById('deces-rr-beneficiaire-label-c');
      if (benLabelC) benLabelC.textContent = 'Le bénéficiaire désire recevoir';
    }

    // Populate assuré dropdown
    const ownerSel = document.getElementById('deces-av-owner');
    if (ownerSel) {
      const clientPrenom2 = document.getElementById('client-prenom')?.value || 'Client';
      ownerSel.innerHTML = `<option value="">Sélectionnez...</option><option value="client">${clientPrenom2}</option>`;
      const conjointChecked = document.getElementById('conjoint')?.checked;
      if (conjointChecked) {
        const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
        ownerSel.innerHTML += `<option value="conjoint">${conjointPrenom}</option>`;
      }
    }

    // Render actifs/passifs lists
    decesRenderActifs();
    decesRenderPassifs();

    // Render revenus actuels
    decesRenderRevenus();

    // Auto-suggérer la rente de conjoint survivant selon l'âge du survivant (RRQ)
    // Panneau-C : client décède → survivant = conjoint
    // Panneau-J : conjoint décède → survivant = client
    const isCouple2 = document.getElementById('conjoint')?.checked;
    const clientBirthYear  = parseInt(document.getElementById('client-naissance-annee')?.value) || 0;
    const conjBirthYear    = parseInt(document.getElementById('conjoint-naissance-annee')?.value) || 0;
    const renteC = document.getElementById('deces-rente-conjoint-c');
    const renteJ = document.getElementById('deces-rente-conjoint-j');
    rcUpdatePanelHelpers(); // show/hide invalide vs rente-defunt inputs
    if (renteC && (!parseFloat(renteC.value) || parseFloat(renteC.value) === 0)) {
      const defC = parseFloat((document.getElementById('deces-rente-defunt-c')?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
      const invC = document.getElementById('deces-invalide-c')?.checked || false;
      const sugg = isCouple2 && conjBirthYear ? getRenteConjSuggestion(conjBirthYear, survivorHasChildren('conjoint'), invC, defC) * 12 : 0;
      if (sugg) renteC.value = Math.round(sugg).toLocaleString('fr-CA');
    }
    if (renteJ && (!parseFloat(renteJ.value) || parseFloat(renteJ.value) === 0)) {
      const defJ = parseFloat((document.getElementById('deces-rente-defunt-j')?.value||'0').replace(/\s/g,'').replace(',','.')) || 0;
      const invJ = document.getElementById('deces-invalide-j')?.checked || false;
      const sugg = clientBirthYear ? getRenteConjSuggestion(clientBirthYear, survivorHasChildren('client'), invJ, defJ) * 12 : 0;
      if (sugg) renteJ.value = Math.round(sugg).toLocaleString('fr-CA');
    }

    decesCalc();
  }

  function decesRenderActifs() {
    const body = document.getElementById('deces-actifs-body');
    if (!body) return;
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const items = [];
    document.querySelectorAll('#actifs-list [data-aptype]').forEach(el => {
      const nom = el.dataset.nom || el.querySelector('.ap-item-name')?.textContent || el.dataset.aptype;
      const valeur = parseFloat(el.dataset.valeur) || 0;
      const owner = el.dataset.owner || 'both';
      items.push({ nom, valeur, owner });
    });
    if (items.length === 0) {
      body.innerHTML = '<p style="font-size:13px;color:var(--muted);padding:4px 0">Aucun actif disponible.</p>';
      return;
    }
    const propLabel = o => o === 'client' ? clientPrenom : o === 'conjoint' ? conjointPrenom : o === 'both' ? `${clientPrenom} et ${conjointPrenom}` : '—';
    if (isCouple) {
      let html = `<div style="overflow-x:auto;padding-bottom:4px">
        <table style="width:100%;border-collapse:collapse;font-size:12px">
          <thead>
            <tr style="border-bottom:2px solid var(--border)">
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Description</th>
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Propriétaire</th>
              <th style="padding:8px 12px;text-align:right;font-weight:700;color:var(--muted);white-space:nowrap">Valeur</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${clientPrenom}</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${conjointPrenom}</th>
            </tr>
          </thead>
          <tbody>`;
      items.forEach(it => {
        html += `<tr style="border-bottom:1px solid var(--border)">
          <td style="padding:8px 12px">${it.nom}</td>
          <td style="padding:8px 12px;color:var(--muted)">${propLabel(it.owner)}</td>
          <td style="padding:8px 12px;text-align:right;font-weight:600">${fmtMoney(it.valeur)}</td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-actif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()" style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-actif-chk-j" data-valeur="${it.valeur}" onchange="decesCalc()" style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
        </tr>`;
      });
      html += '</tbody></table></div>';
      body.innerHTML = html;
    } else {
      body.innerHTML = items.map(it =>
        `<div class="fu-actif-row">
          <input type="checkbox" class="fu-actif-check deces-actif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()"/>
          <label class="fu-actif-name">${it.nom}</label>
          <span class="fu-actif-valeur">${fmtMoney(it.valeur)}</span>
        </div>`
      ).join('');
    }
  }

  function decesRenderPassifs() {
    const body = document.getElementById('deces-passifs-body');
    if (!body) return;
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';

    const items = [];
    document.querySelectorAll('#passifs-list [data-valeur]').forEach(el => {
      const nom = el.querySelector('.ap-item-name')?.textContent?.trim() || el.dataset.aptype || 'Passif';
      const valeur = parseFloat(el.dataset.valeur) || 0;
      const owner = el.dataset.owner || 'both';
      items.push({ nom, valeur, owner });
    });

    if (items.length === 0) {
      body.innerHTML = '<p style="font-size:13px;color:var(--muted);padding:4px 0">Aucun passif disponible.</p>';
      return;
    }

    const propLabel = o => o === 'client' ? clientPrenom : o === 'conjoint' ? conjointPrenom : o === 'both' ? `${clientPrenom} et ${conjointPrenom}` : '—';

    if (isCouple) {
      let html = `<div style="overflow-x:auto;padding-bottom:4px">
        <table style="width:100%;border-collapse:collapse;font-size:12px">
          <thead>
            <tr style="border-bottom:2px solid var(--border)">
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Description</th>
              <th style="padding:8px 12px;text-align:left;font-weight:700;color:var(--muted);white-space:nowrap">Propriétaire</th>
              <th style="padding:8px 12px;text-align:right;font-weight:700;color:var(--muted);white-space:nowrap">Valeur</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${clientPrenom}</th>
              <th style="padding:8px 12px;text-align:center;font-weight:700;color:var(--muted);white-space:nowrap">Au décès de ${conjointPrenom}</th>
            </tr>
          </thead>
          <tbody>`;
      items.forEach(it => {
        html += `<tr style="border-bottom:1px solid var(--border)">
          <td style="padding:8px 12px">${it.nom}</td>
          <td style="padding:8px 12px;color:var(--muted)">${propLabel(it.owner)}</td>
          <td style="padding:8px 12px;text-align:right;font-weight:600">${fmtMoney(it.valeur)}</td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-passif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()" checked style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
          <td style="padding:8px 12px;text-align:center"><input type="checkbox" class="deces-passif-chk-j" data-valeur="${it.valeur}" onchange="decesCalc()" checked style="width:16px;height:16px;cursor:pointer;accent-color:var(--navy)"/></td>
        </tr>`;
      });
      html += '</tbody></table></div>';
      body.innerHTML = html;
    } else {
      body.innerHTML = items.map(it =>
        `<div class="fu-actif-row">
          <input type="checkbox" class="fu-actif-check deces-passif-chk-c" data-valeur="${it.valeur}" onchange="decesCalc()" checked/>
          <label class="fu-actif-name">${it.nom}</label>
          <span class="fu-actif-valeur">${fmtMoney(it.valeur)}</span>
        </div>`
      ).join('');
    }
  }

  function getRevenusByOwner(owner, isNet, excludeEmploi = false) {
    const items = [];
    document.querySelectorAll('#revenu-list tr[data-revenu-annuel]').forEach(tr => {
      const annuel = parseFloat(tr.dataset.revenuAnnuel) || 0;
      if (!annuel) return;
      const isConj = tr.dataset.owner === 'conjoint';
      if (owner === 'conjoint' ? !isConj : isConj) return;
      if (excludeEmploi && tr.dataset.revenuType === 'emploi') return;
      const desc = tr.querySelector('td:first-child')?.textContent || '';
      const val = isNet ? (computeImpot(annuel)?.net ?? annuel) : annuel;
      items.push({ desc, val });
    });
    return { items, total: items.reduce((s, r) => s + r.val, 0) };
  }

  function decesRenderRevenus() {
    const freq = document.querySelector('input[name="deces-rr-freq"]:checked')?.value || 'annuel';
    const brutNet = document.querySelector('input[name="deces-rr-brutnnet"]:checked')?.value || 'brut';
    const isNet = brutNet === 'net';
    const diviseur = freq === 'mensuel' ? 12 : 1;
    const isCouple = document.getElementById('conjoint')?.checked;
    const type = document.querySelector('input[name="deces-rr-type"]:checked')?.value || 'individuel';
    const isFamilial = type === 'familial';
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjPrenom   = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const labelBN  = isNet ? 'net' : 'brut';
    const labelFrq = freq === 'mensuel' ? 'mensuel' : 'annuel';

    const clientData = getRevenusByOwner('client',   isNet);
    const conjData   = isCouple ? getRevenusByOwner('conjoint', isNet) : { items: [], total: 0 };

    const rowStyle = 'display:flex;justify-content:space-between;padding:3px 0';
    const totalRowStyle = 'display:flex;justify-content:space-between;padding:6px 0 2px;font-weight:700;border-top:1px solid var(--border);margin-top:4px';

    // Render "Revenus actuels" table — Individuel: deceased only; Familial: both persons
    const renderIndivTable = (items, total) => {
      if (!items.length) return '<div style="color:var(--muted)">Aucun revenu enregistré.</div>';
      let h = items.map(r =>
        `<div style="${rowStyle}"><span>${r.desc}</span><span>${fmtMoney(r.val / diviseur)}</span></div>`
      ).join('');
      h += `<div style="${totalRowStyle}"><span>Revenu ${labelBN} ${labelFrq}</span><span>${fmtMoney(total / diviseur)}</span></div>`;
      return h;
    };
    const renderFamilialTable = (d1, name1, d2, name2) => {
      let h = '';
      const subHdr = n => `<div style="font-size:11px;font-weight:700;color:var(--muted);padding:4px 0 3px;text-transform:uppercase">${n}</div>`;
      if (d1.items.length) {
        h += subHdr(name1);
        h += d1.items.map(r => `<div style="${rowStyle}"><span>${r.desc}</span><span>${fmtMoney(r.val / diviseur)}</span></div>`).join('');
      }
      if (d2.items.length) {
        h += subHdr(name2);
        h += d2.items.map(r => `<div style="${rowStyle}"><span>${r.desc}</span><span>${fmtMoney(r.val / diviseur)}</span></div>`).join('');
      }
      if (!d1.items.length && !d2.items.length) return '<div style="color:var(--muted)">Aucun revenu enregistré.</div>';
      const familialTotal = d1.total + d2.total;
      h += `<div style="${totalRowStyle}"><span>Revenu familial ${labelBN} ${labelFrq}</span><span>${fmtMoney(familialTotal / diviseur)}</span></div>`;
      return h;
    };

    const tblC = document.getElementById('deces-revenus-table-c');
    const tblJ = document.getElementById('deces-revenus-table-j');
    if (isFamilial && isCouple) {
      const familialHtml = renderFamilialTable(clientData, clientPrenom, conjData, conjPrenom);
      if (tblC) tblC.innerHTML = familialHtml;
      if (tblJ) tblJ.innerHTML = familialHtml;
    } else {
      if (tblC) tblC.innerHTML = renderIndivTable(clientData.items, clientData.total);
      if (tblJ) tblJ.innerHTML = renderIndivTable(conjData.items,   conjData.total);
    }

    // Render "Revenus disponibles" auto-card
    // Familial+couple: show survivor's income (ex-emploi) as auto-card
    // Individuel: no auto-card
    const cardStyle = 'background:#eef2ff;border-radius:6px;padding:8px 10px;text-align:center';
    const nameStyle = 'font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px';
    const valStyle  = 'font-size:14px;font-weight:700;color:var(--navy)';
    const card = (name, val) =>
      `<div style="${cardStyle}"><div style="${nameStyle}">${name}</div><div style="${valStyle}">${fmtMoney(val / diviseur)}</div></div>`;

    const autoC = document.getElementById('deces-revenu-dispo-auto-c');
    const autoJ = document.getElementById('deces-revenu-dispo-auto-j');
    if (isFamilial && isCouple) {
      // Survivant = still alive → include all income (incl. emploi)
      const conjAllData   = getRevenusByOwner('conjoint', isNet, false);
      const clientAllData = getRevenusByOwner('client',   isNet, false);
      // panel-c = client dies → survivor is conjoint
      if (autoC) autoC.innerHTML = `<div style="padding:4px 0 6px">${card(`Revenu de ${conjPrenom}`, conjAllData.total)}</div>`;
      // panel-j = conjoint dies → survivor is client
      if (autoJ) autoJ.innerHTML = `<div style="padding:4px 0 6px">${card(`Revenu de ${clientPrenom}`, clientAllData.total)}</div>`;
    } else {
      if (autoC) autoC.innerHTML = '';
      if (autoJ) autoJ.innerHTML = '';
    }
  }

  function calcRrPanel(sfx) {
    const freq    = document.querySelector('input[name="deces-rr-freq"]:checked')?.value     || 'annuel';
    const brutNet = document.querySelector('input[name="deces-rr-brutnnet"]:checked')?.value || 'brut';
    const isNet   = brutNet === 'net';
    const isCouple = document.getElementById('conjoint')?.checked;
    const type = document.querySelector('input[name="deces-rr-type"]:checked')?.value || 'individuel';
    const isFamilial = type === 'familial';
    const pctEl = document.getElementById(`deces-rr-pct-${sfx}`);
    if (!pctEl) return 0;

    // Revenues of the person who died (this panel's owner)
    // panel-c = client dies → use client revenues as the base to replace
    // panel-j = conjoint dies → use conjoint revenues
    const deceasedOwner  = sfx === 'c' ? 'client'   : 'conjoint';
    const survivorOwner  = sfx === 'c' ? 'conjoint' : 'client';
    const deceasedData = getRevenusByOwner(deceasedOwner, isNet);
    const conjData     = isCouple ? getRevenusByOwner(survivorOwner, isNet) : { total: 0 };
    // Familial: base = total family income; Individuel: base = deceased only
    const annuelBase = (isFamilial && isCouple) ? deceasedData.total + conjData.total : deceasedData.total;

    // Update "du revenu" / "du revenu familial" text
    const duRevenuEl = document.getElementById(`deces-rr-du-revenu-${sfx}`);
    if (duRevenuEl) duRevenuEl.textContent = (isFamilial && isCouple) ? 'du revenu familial' : 'du revenu';

    const pct        = parseFloat(pctEl.value || '70') / 100;
    const targetType = document.querySelector(`input[name="deces-rr-target-${sfx}"]:checked`)?.value || 'pct';
    const revenuVise = targetType === 'pct'
      ? annuelBase * pct
      : (parseFloat((pctEl.value || '0').replace(/\s/g,'').replace(',','.')) || 0);
    const label = document.getElementById(`deces-rr-vise-label-${sfx}`);
    if (label) label.textContent = fmtMoney(freq === 'mensuel' ? revenuVise / 12 : revenuVise) + (freq === 'mensuel' ? '/mois' : '/an');

    // Survivor's income auto-computed — include emploi (survivor is alive and working)
    // Only in Familial mode (shown as auto-card); Individuel = 0 (manual entry only)
    const dispo = (isFamilial && isCouple) ? getRevenusByOwner(survivorOwner, isNet, false).total : 0;
    const autres    = parseFloat((document.getElementById(`deces-autres-revenus-${sfx}`)?.value  || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const disponible = dispo + autres;
    // Revenu annuel manquant = revenu visé moins revenus disponibles, minimum 0
    const manquantAnnuel = Math.max(0, revenuVise - disponible);
    const duree = parseFloat(document.getElementById(`deces-rr-duree-${sfx}`)?.value || '10') || 10;
    const taux  = parseFloat((document.getElementById(`deces-rr-taux-${sfx}`)?.value || '3.70').replace(',','.')) / 100 || 0;
    const inflation = parseFloat((document.getElementById('vd-inflation')?.value || '2,10').replace(',','.')) / 100 || 0;
    // Capital nécessaire = PV d'une rente croissante (inflation) au taux de rendement
    let pv;
    if (Math.abs(taux - inflation) < 0.0001) {
      // r ≈ g : cas limite
      pv = manquantAnnuel * duree / (1 + taux);
    } else {
      // Formule générale (fonctionne aussi quand taux=0 et inflation>0)
      pv = manquantAnnuel * (1 - Math.pow((1 + inflation) / (1 + taux), duree)) / (taux - inflation);
    }
    const m = document.getElementById(`deces-rr-manquant-${sfx}`);
    const p = document.getElementById(`deces-rr-projete-${sfx}`);
    const pd = document.getElementById(`deces-rr-projete-duree-${sfx}`);
    if (m) m.textContent = fmtMoney(manquantAnnuel);
    if (p) p.textContent = fmtMoney(pv);
    if (pd) pd.textContent = duree ? `pendant ${duree} an${duree > 1 ? 's' : ''}` : '';
    return pv;
  }

  function toggleDecesDep() {
    const dd = document.getElementById('deces-dep-dd');
    if (!dd) return;
    const wasOpen = dd.style.display === 'block';
    document.querySelectorAll('#deces-dep-dd').forEach(d => d.style.display = 'none');
    if (!wasOpen) {
      const btn = event.currentTarget;
      const r = btn.getBoundingClientRect();
      dd.style.position = 'fixed';
      dd.style.top = (r.bottom + 4) + 'px';
      dd.style.left = r.left + 'px';
      dd.style.display = 'block';
    }
  }

  function addDecesDep(desc, montantDefault) {
    document.getElementById('deces-dep-dd').style.display = 'none';
    const listId = _decesDepActiveTab === 'conjoint' ? 'deces-dep-list-conjoint' : 'deces-dep-list';
    const list = document.getElementById(listId);
    const uid = Math.random().toString(36).slice(2);
    const row = document.createElement('div');
    row.className = 'deces-dep-row';
    row.dataset.montant = montantDefault;
    row.dataset.desc = desc;
    row.dataset.indexed = 'oui';
    row.innerHTML = `
      <span style="flex:1;color:var(--text)">${desc}</span>
      <div class="input-sfx" style="max-width:140px">
        <input class="form-input" type="text" value="${montantDefault ? montantDefault.toLocaleString('fr-CA') : ''}" placeholder="0"
          oninput="this.closest('[data-montant]').dataset.montant=parseFloat(this.value.replace(/\\s/g,'').replace(',','.'))||0;decesCalc()"/>
        <span class="sfx">$</span>
      </div>
      <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
        <span style="font-size:10px;color:var(--muted);white-space:nowrap">Indexé à l'inflation</span>
        <div style="display:flex;gap:4px">
          <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="oui" checked onchange="this.closest('.deces-dep-row').dataset.indexed='oui'"/> Oui</label>
          <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="non" onchange="this.closest('.deces-dep-row').dataset.indexed='non'"/> Non</label>
        </div>
      </div>
      <button onclick="this.closest('.deces-dep-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>
    `;
    list.appendChild(row);
    decesCalc();
  }

  function openDecesAvModal() {
    // Reset
    ['deces-av-type','deces-av-owner','deces-av-assureur'].forEach(id => {
      const el = document.getElementById(id); if(el) el.value = '';
    });
    ['deces-av-montant','deces-av-prime','deces-av-date','deces-av-notes'].forEach(id => {
      const el = document.getElementById(id); if(el) el.value = '';
    });
    document.getElementById('deces-av-exclure').checked = false;
    document.querySelectorAll('input[name="deces-av-benef"]').forEach(r => r.checked = false);
    document.getElementById('modal-deces-av').style.display = 'flex';
  }

  function closeDecesAvModal() {
    document.getElementById('modal-deces-av').style.display = 'none';
  }

  function saveDecesAv() {
    const type     = document.getElementById('deces-av-type').value;
    const owner    = document.getElementById('deces-av-owner').options[document.getElementById('deces-av-owner').selectedIndex]?.text || '';
    const montant  = parseFloat((document.getElementById('deces-av-montant').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const prime    = parseFloat((document.getElementById('deces-av-prime').value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const assureur = document.getElementById('deces-av-assureur').value;
    const exclure  = document.getElementById('deces-av-exclure').checked;
    const benef    = document.querySelector('input[name="deces-av-benef"]:checked')?.value || '';

    if (!type || !owner || montant <= 0) { showToast('Type, assuré et montant sont requis'); return; }

    const list = document.getElementById('deces-av-list');
    const empty = document.getElementById('deces-av-empty');
    if (empty) empty.remove();

    const row = document.createElement('div');
    row.className = 'deces-av-row';
    row.dataset.montant = exclure ? 0 : montant;
    row.dataset.ownerVal = document.getElementById('deces-av-owner').value;
    row.dataset.formJson = JSON.stringify({
      type, ownerVal: document.getElementById('deces-av-owner').value, owner,
      montant, prime, assureur, exclure, benef,
      notes: document.getElementById('deces-av-notes')?.value.trim() || '',
      date: document.getElementById('deces-av-date')?.value.trim() || '',
    });
    row.innerHTML = `
      <div style="flex:1">
        <div style="font-weight:600">${type} — ${owner}</div>
        <div style="font-size:11px;color:var(--muted)">${assureur || ''}${benef ? ' · Bénéficiaire: '+benef : ''}${exclure ? ' · <em>Exclu de l\'analyse</em>' : ''}</div>
      </div>
      <div style="text-align:right;margin-right:12px">
        <div style="font-weight:700">${fmtMoney(montant)}</div>
        ${prime > 0 ? `<div style="font-size:11px;color:var(--muted)">Prime: ${fmtMoney(prime)}/an</div>` : ''}
      </div>
      <button onclick="this.closest('.deces-av-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>
    `;
    list.appendChild(row);
    closeDecesAvModal();
    decesCalc();
  }

  function decesCalc() {
    decesRenderRevenus();
    const type = document.querySelector('input[name="deces-rr-type"]:checked')?.value || 'individuel';
    const brutNet = document.querySelector('input[name="deces-rr-brutnnet"]:checked')?.value || 'brut';
    const freq = document.querySelector('input[name="deces-rr-freq"]:checked')?.value || 'annuel';

    // Dépenses ponctuelles (both lists)
    let depTotal = 0;
    document.querySelectorAll('#deces-dep-list [data-montant], #deces-dep-list-conjoint [data-montant]').forEach(el => {
      depTotal += parseFloat(el.dataset.montant) || 0;
    });

    // Prestation RRQ
    let rrqTotal = 0;
    const rrqClient = parseFloat((document.getElementById('deces-rrq-client')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    const rrqConjoint = parseFloat((document.getElementById('deces-rrq-conjoint')?.value || '0').replace(/\s/g,'').replace(',','.')) || 0;
    rrqTotal = rrqClient + rrqConjoint;

    // Remplacement du revenu (PV)
    let rrCapital = 0;
    if (type !== 'aucun') {
      const isCouple = document.getElementById('conjoint')?.checked;
      rrCapital = calcRrPanel('c');
      if (isCouple) rrCapital += calcRrPanel('j');
      const rrFormEl = document.getElementById('deces-rr-form');
      if (rrFormEl) rrFormEl.style.display = isCouple ? 'grid' : '';
    } else {
      document.getElementById('deces-rr-form').style.display = 'none';
    }

    // Per-person dep totals
    let depTotalC = 0, depTotalJ = 0;
    document.querySelectorAll('#deces-dep-list [data-montant]').forEach(el => depTotalC += parseFloat(el.dataset.montant)||0);
    document.querySelectorAll('#deces-dep-list-conjoint [data-montant]').forEach(el => depTotalJ += parseFloat(el.dataset.montant)||0);

    // Per-person RR capital
    const rrCapC = type !== 'aucun' ? (parseFloat(document.getElementById('deces-rr-projete-c')?.textContent?.replace(/\s/g,'').replace(',','.').replace('$','')) || 0) : 0;
    const rrCapJ = type !== 'aucun' ? (parseFloat(document.getElementById('deces-rr-projete-j')?.textContent?.replace(/\s/g,'').replace(',','.').replace('$','')) || 0) : 0;

    // Capital disponible: AV split by owner, actifs/passifs shared
    let avClient = 0, avConjoint = 0;
    document.querySelectorAll('.deces-av-row[data-montant]').forEach(r => {
      const m = parseFloat(r.dataset.montant) || 0;
      if (r.dataset.ownerVal === 'conjoint') avConjoint += m; else avClient += m;
    });
    let actifsTotalC = 0, actifsTotalJ = 0;
    document.querySelectorAll('.deces-actif-chk-c:checked').forEach(chk => actifsTotalC += parseFloat(chk.dataset.valeur)||0);
    document.querySelectorAll('.deces-actif-chk-j:checked').forEach(chk => actifsTotalJ += parseFloat(chk.dataset.valeur)||0);
    let passifsTotalC = 0, passifsTotalJ = 0;
    document.querySelectorAll('.deces-passif-chk-c:checked').forEach(chk => passifsTotalC += parseFloat(chk.dataset.valeur)||0);
    document.querySelectorAll('.deces-passif-chk-j:checked').forEach(chk => passifsTotalJ += parseFloat(chk.dataset.valeur)||0);

    // Besoin = capital revenu visé + passifs à rembourser + dépenses ponctuelles
    const besoinC = rrCapC + passifsTotalC + depTotalC;
    const besoinJ = rrCapJ + passifsTotalJ + depTotalJ;
    // Disponible = AV + actifs liquidables + prestation RRQ
    const dispoC  = avClient  + actifsTotalC + rrqClient;
    const dispoJ  = avConjoint + actifsTotalJ + rrqConjoint;

    // Render résumé
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const sections = isCouple
      ? [{name: clientPrenom, besoins: besoinC, disponibles: dispoC}, {name: conjointPrenom, besoins: besoinJ, disponibles: dispoJ}]
      : [{name: clientPrenom, besoins: besoinC, disponibles: dispoC}];
    decesRenderResume(sections);
  }

  function decesRenderResume(sections) {
    const body = document.getElementById('deces-resume-body');
    if (!body) return;
    let html = '';
    sections.forEach((s, i) => {
      const manque = Math.max(0, s.besoins - s.disponibles);
      const pct = s.besoins > 0 ? Math.min(100, Math.round(s.disponibles / s.besoins * 100)) : (s.disponibles > 0 ? 100 : 0);
      const color = pct >= 100 ? '#22c55e' : pct >= 50 ? '#f59e0b' : '#ef4444';
      const border = i < sections.length - 1 ? 'border-bottom:1px solid var(--border);padding-bottom:16px;margin-bottom:16px' : '';
      html += `<div style="${border}">
        <div style="font-size:12px;font-weight:700;color:var(--navy);letter-spacing:.5px;margin-bottom:10px;text-transform:uppercase">${s.name}</div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
          <div style="flex:1;height:8px;background:#e5e7eb;border-radius:4px;overflow:hidden">
            <div style="height:100%;background:${color};border-radius:4px;width:${pct}%;transition:width .3s"></div>
          </div>
          <span style="font-size:12px;font-weight:700;color:${color};min-width:38px;text-align:right">${pct}&nbsp;%</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0">
          <span style="color:var(--muted)">Besoins actuels</span>
          <span style="font-weight:600">${fmtMoney(s.besoins)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0">
          <span style="color:var(--muted)">Montants disponibles</span>
          <span style="font-weight:600">${fmtMoney(s.disponibles)}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0">
          <span style="color:var(--muted)">Manque à gagner</span>
          <span style="font-weight:600;color:${manque > 0 ? '#ef4444' : '#22c55e'}">${manque > 0 ? fmtMoney(manque) : '—'}</span>
        </div>
      </div>`;
    });
    body.innerHTML = html;
  }

  // Close dropdown on outside click
  document.addEventListener('click', e => {
    const dd = document.getElementById('deces-dep-dd');
    if (dd && !e.target.closest('#deces-dep-dd') && !e.target.closest('button[onclick*="toggleDecesDep"]')) {
      dd.style.display = 'none';
    }
  });

  /* ── INVALIDITÉ ─────────────────────────────────────── */
  let _invalAvList = [];

  function invalRrPanelHtml(owner, prenom, isNet) {
    const revMensuel = getRevenusByOwner(owner, isNet).total / 12;
    const pct = 70;
    const montant = Math.round(revMensuel * pct / 100);
    const brutNetLabel = isNet ? 'net' : 'brut';
    return `<div>
      <div style="font-weight:600;font-size:13px;margin-bottom:10px">${prenom}</div>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px;margin-bottom:8px">
        <input class="form-input" id="inval-rr-pct-${owner}" type="text" value="${pct}" style="width:60px;text-align:center" oninput="invaliditeCalc()"/>
        <span style="white-space:nowrap">% du revenu ${brutNetLabel} de <strong>${fmtMoney(Math.round(revMensuel))}/mois</strong>, soit</span>
      </div>
      <div style="padding:10px 14px;background:#eef2ff;border-radius:6px;font-size:14px;font-weight:700;color:var(--navy)">
        <span id="inval-rr-montant-${owner}">${fmtMoney(montant)}</span>/mois
      </div>
    </div>`;
  }

  function invaliditeInit() {
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const isNet = document.getElementById('inval-bn-net')?.classList.contains('active');

    // Autres revenus rows
    const autresRows = document.getElementById('inval-autres-revenus-rows');
    if (autresRows) {
      let html = `<div class="form-group" id="inval-rev-client-row">
        <label class="form-label">Revenus mensuels de ${clientPrenom}</label>
        <div class="input-sfx" style="max-width:200px"><input class="form-input" id="inval-rev-client" type="text" placeholder="0" oninput="invaliditeCalc()"/><span class="sfx">$/mois</span></div>
      </div>`;
      if (isCouple) {
        html += `<div class="form-group" id="inval-rev-conjoint-row">
          <label class="form-label">Revenus mensuels de ${conjointPrenom}</label>
          <div class="input-sfx" style="max-width:200px"><input class="form-input" id="inval-rev-conjoint" type="text" placeholder="0" oninput="invaliditeCalc()"/><span class="sfx">$/mois</span></div>
        </div>`;
      }
      autresRows.innerHTML = html;
    }

    // Remplacement du revenu body
    const rrBody = document.getElementById('inval-rr-body');
    if (rrBody) {
      if (isCouple) {
        rrBody.style.padding = '16px';
        rrBody.innerHTML = `<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
          ${invalRrPanelHtml('client', clientPrenom, isNet)}
          ${invalRrPanelHtml('conjoint', conjointPrenom, isNet)}
        </div>`;
      } else {
        rrBody.innerHTML = invalRrPanelHtml('client', clientPrenom, isNet);
      }
    }

    invalRenderAvList();
    invaliditeCalc();
  }

  function setInvalBrutNet(val) {
    document.getElementById('inval-bn-brut').classList.toggle('active', val === 'brut');
    document.getElementById('inval-bn-net').classList.toggle('active', val === 'net');
    invaliditeInit();
  }

  function invaliditeApproche() {
    const v = document.querySelector('input[name="inval-approche"]:checked')?.value;
    document.getElementById('inval-rr-section').style.display = v === 'remplacement' ? '' : 'none';
    document.getElementById('inval-dep-section').style.display = v === 'depenses' ? '' : 'none';
    invaliditeCalc();
  }

  function toggleInvalInfo() {
    const body = document.getElementById('inval-info-body');
    const chevron = document.getElementById('inval-info-chevron');
    const open = body.style.display !== 'none';
    body.style.display = open ? 'none' : '';
    chevron.style.transform = open ? '' : 'rotate(180deg)';
  }

  function invaliditeCalc() {
    const isCouple = document.getElementById('conjoint')?.checked;
    const clientPrenom = document.getElementById('client-prenom')?.value || 'Client';
    const conjointPrenom = document.getElementById('conjoint-prenom')?.value || 'Conjoint(e)';
    const approche = document.querySelector('input[name="inval-approche"]:checked')?.value || 'remplacement';
    const isNet = document.getElementById('inval-bn-net')?.classList.contains('active');

    let besoinClient = 0, besoinConjoint = 0;

    if (approche === 'remplacement') {
      const pctClient = parseFloat(document.getElementById('inval-rr-pct-client')?.value) || 70;
      const revClient = getRevenusByOwner('client', isNet).total / 12;
      besoinClient = Math.round(revClient * pctClient / 100);
      const elC = document.getElementById('inval-rr-montant-client');
      if (elC) elC.textContent = fmtMoney(besoinClient);

      if (isCouple) {
        const pctConj = parseFloat(document.getElementById('inval-rr-pct-conjoint')?.value) || 70;
        const revConj = getRevenusByOwner('conjoint', isNet).total / 12;
        besoinConjoint = Math.round(revConj * pctConj / 100);
        const elJ = document.getElementById('inval-rr-montant-conjoint');
        if (elJ) elJ.textContent = fmtMoney(besoinConjoint);
      }
    } else {
      const dep = parseFloat(document.getElementById('inval-dep-total')?.value?.replace(/\s/g,'').replace(',','.')) || 0;
      besoinClient = dep;
    }

    // Couverture existante par propriétaire
    let couvertureClient = 0, couvertureConjoint = 0;
    _invalAvList.forEach(av => {
      if (av.owner === 'client' || av.owner === 'both') couvertureClient += av.montant;
      if (av.owner === 'conjoint' || av.owner === 'both') couvertureConjoint += av.montant;
    });

    // Autres revenus
    const autresClient = parseFloat(document.getElementById('inval-rev-client')?.value?.replace(/\s/g,'').replace(',','.')) || 0;
    const autresConj = isCouple ? (parseFloat(document.getElementById('inval-rev-conjoint')?.value?.replace(/\s/g,'').replace(',','.')) || 0) : 0;

    const ecartClient = besoinClient - couvertureClient - autresClient;
    const ecartConj = besoinConjoint - couvertureConjoint - autresConj;

    const resume = document.getElementById('inval-resume-body');
    if (!resume) return;

    const rowHtml = (label, val) => `<div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border);font-size:13px"><span style="color:var(--muted)">${label}</span><strong>${val}</strong></div>`;

    const sectionHtml = (title, besoin, couverture, autres, ecart) => {
      const sign = ecart > 0;
      return `<div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:10px 0 4px">${title}</div>
        ${rowHtml('Besoin mensuel estimé', fmtMoney(besoin)+'/mois')}
        ${rowHtml('Couverture existante', fmtMoney(couverture)+'/mois')}
        ${autres ? rowHtml('Autres revenus', fmtMoney(autres)+'/mois') : ''}
        <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px">
          <span style="font-weight:600">${sign ? 'Besoin additionnel' : 'Excédent'}</span>
          <strong style="color:${sign ? '#ef4444' : '#22c55e'}">${fmtMoney(Math.abs(ecart))}/mois</strong>
        </div>`;
    };

    let html = sectionHtml(isCouple ? clientPrenom : 'Protection', besoinClient, couvertureClient, autresClient, ecartClient);
    if (isCouple) {
      html += `<div style="border-top:2px solid var(--border);margin:4px 0"></div>`;
      html += sectionHtml(conjointPrenom, besoinConjoint, couvertureConjoint, autresConj, ecartConj);
    }
    resume.innerHTML = html;
  }

  function invalRenderAvList() {
    const list = document.getElementById('inval-av-list');
    if (!list) return;
    if (!_invalAvList.length) {
      list.innerHTML = '<p style="padding:14px;font-size:13px;color:var(--muted);margin:0">Aucune assurance invalidité enregistrée.</p>';
      return;
    }
    list.innerHTML = _invalAvList.map((av, i) => `
      <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid var(--border);font-size:13px">
        <div>
          <div style="font-weight:600">${av.desc}</div>
          <div style="color:var(--muted);font-size:12px">${av.ownerTx}</div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
          <span style="font-weight:700;color:var(--navy)">${fmtMoney(av.montant)}/mois</span>
          <button onclick="_invalAvList.splice(${i},1);invalRenderAvList();invaliditeCalc()" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:18px;padding:0;line-height:1">×</button>
        </div>
      </div>`).join('');
  }

  function openInvalAvModal() {
    apFillBienProprietaire('inval-av-proprietaire');
    document.getElementById('inval-av-desc').value = '';
    document.getElementById('inval-av-montant').value = '';
    document.getElementById('modal-inval-av').style.display = 'flex';
    setTimeout(() => document.getElementById('inval-av-desc').focus(), 50);
  }
  function closeInvalAvModal() { document.getElementById('modal-inval-av').style.display = 'none'; }
  function saveInvalAv() {
    const desc = document.getElementById('inval-av-desc').value.trim() || 'Assurance invalidité';
    const montant = parseFloat(document.getElementById('inval-av-montant').value.replace(/\s/g,'').replace(',','.')) || 0;
    const prop = document.getElementById('inval-av-proprietaire');
    const owner = prop.value || 'client';
    const ownerTx = prop.options[prop.selectedIndex]?.text || owner;
    _invalAvList.push({ desc, montant, owner, ownerTx });
    invalRenderAvList();
    closeInvalAvModal();
    invaliditeCalc();
  }
  document.getElementById('modal-inval-av')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modal-inval-av')) closeInvalAvModal();
  });

  /* ── TOPBAR DYNAMIQUE ────────────────────────────────── */
  (function() {
    const dateEl = document.getElementById('abf-topbar-date');
    if (dateEl) {
      const now = new Date();
      const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
      dateEl.textContent = now.toLocaleDateString('fr-CA', opts);
    }
    const userEl = document.getElementById('abf-topbar-user');
    if (userEl && window.ABF_ADVISOR_NAME) {
      userEl.innerHTML = '👤 <strong>' + window.ABF_ADVISOR_NAME + '</strong>';
    }
  })();

  /* ── SÉRIALISATION / PERSISTANCE ─────────────────────── */
  function gatherPayload() {
    const v = id => (document.getElementById(id)?.value || '');
    const radio = name => document.querySelector(`input[name="${name}"]:checked`)?.value || '';

    const enfants = [];
    document.querySelectorAll('#enfants-list .enfant-item[data-charge]').forEach(el => {
      enfants.push({
        prenom: el.dataset.enfPrenom || '', nom: el.dataset.enfNom || '',
        sexe: el.dataset.enfSexe || '', jour: el.dataset.enfJour || '',
        mois: el.dataset.enfMois || '', annee: el.dataset.enfAnnee || '',
        relation: el.dataset.enfRelation || '', charge: el.dataset.charge || '',
      });
    });

    const revenus = [];
    document.querySelectorAll('#revenu-list tr[data-form-json]').forEach(tr => {
      try { revenus.push(JSON.parse(tr.dataset.formJson)); } catch {}
    });

    const actifs = [];
    document.querySelectorAll('#actifs-list [data-form-json]').forEach(el => {
      try {
        const obj = JSON.parse(el.dataset.formJson || '{}');
        actifs.push({ ...obj, _type: el.dataset.aptype || '', _valeur: parseFloat(el.dataset.valeur) || 0,
          _owner: el.dataset.owner || '', _modalType: el.dataset.modalType || '',
          _partClient: el.dataset.partClient !== undefined ? parseFloat(el.dataset.partClient) : undefined,
          _partConjoint: el.dataset.partConjoint !== undefined ? parseFloat(el.dataset.partConjoint) : undefined });
      } catch {}
    });

    const passifs = [];
    document.querySelectorAll('#passifs-list [data-form-json]').forEach(el => {
      try {
        const obj = JSON.parse(el.dataset.formJson || '{}');
        passifs.push({ ...obj, _type: el.dataset.aptype || '', _valeur: parseFloat(el.dataset.valeur) || 0,
          _owner: el.dataset.owner || '', _modalType: el.dataset.modalType || '',
          _partClient: el.dataset.partClient !== undefined ? parseFloat(el.dataset.partClient) : undefined,
          _partConjoint: el.dataset.partConjoint !== undefined ? parseFloat(el.dataset.partConjoint) : undefined });
      } catch {}
    });

    const legal = [];
    document.querySelectorAll('#legal-list [data-form-json]').forEach(el => {
      try { legal.push(JSON.parse(el.dataset.formJson)); } catch {}
    });

    const decesDeps = [];
    document.querySelectorAll('#deces-dep-list .deces-dep-row').forEach(row => {
      decesDeps.push({ desc: row.dataset.desc || '', montant: parseFloat(row.dataset.montant) || 0, indexed: row.dataset.indexed || 'oui' });
    });
    const decesDepsConj = [];
    document.querySelectorAll('#deces-dep-list-conjoint .deces-dep-row').forEach(row => {
      decesDepsConj.push({ desc: row.dataset.desc || '', montant: parseFloat(row.dataset.montant) || 0, indexed: row.dataset.indexed || 'oui' });
    });

    const decesAv = [];
    document.querySelectorAll('.deces-av-row[data-form-json]').forEach(el => {
      try { decesAv.push(JSON.parse(el.dataset.formJson)); } catch {}
    });

    return {
      client: {
        prenom: v('client-prenom'), nom: v('client-nom'), sexe: radio('sexe'),
        ddn_jour: v('client-ddn-jour'), ddn_mois: v('client-ddn-mois'),
        ddn_annee: v('client-naissance-annee'), etat_civil: v('client-etat-civil'),
        province: v('client-province'), canada_depuis: v('client-canada-depuis'),
        addr_civique: v('client-addr-civique'), addr_rue: v('client-addr-rue'),
        addr_type_unite: v('client-addr-type-unite'), addr_numero: v('client-addr-numero'),
        addr_case: v('client-addr-case'), addr_ville: v('client-addr-ville'),
        addr_province: v('client-addr-province'), addr_postal: v('client-addr-postal'),
      },
      has_spouse: document.querySelector('input[name="plan"][value="conjoint"]')?.checked || false,
      conjoint: {
        prenom: v('conjoint-prenom'), nom: v('conjoint-nom'), sexe: radio('co-sexe'),
        ddn_jour: v('conjoint-ddn-jour'), ddn_mois: v('conjoint-ddn-mois'),
        ddn_annee: v('conjoint-naissance-annee'), etat_civil: v('conjoint-etat-civil'),
        province: v('conjoint-province'), canada_depuis: v('conjoint-canada-depuis'),
        addr_civique: v('conjoint-addr-civique'), addr_rue: v('conjoint-addr-rue'),
        addr_type_unite: v('conjoint-addr-type-unite'), addr_numero: v('conjoint-addr-numero'),
        addr_case: v('conjoint-addr-case'), addr_ville: v('conjoint-addr-ville'),
        addr_province: v('conjoint-addr-province'), addr_postal: v('conjoint-addr-postal'),
      },
      enfants, revenus, actifs, passifs, legal,
      deces: {
        rrq_client: v('deces-rrq-client'), rrq_conjoint: v('deces-rrq-conjoint'),
        autres_revenus_c: v('deces-autres-revenus-c'), autres_revenus_j: v('deces-autres-revenus-j'),
        rr_pct_c: v('deces-rr-pct-c'), rr_pct_j: v('deces-rr-pct-j'),
        rr_duree_c: v('deces-rr-duree-c'), rr_duree_j: v('deces-rr-duree-j'),
        rr_taux_c: v('deces-rr-taux-c'), rr_taux_j: v('deces-rr-taux-j'),
        deps_client: decesDeps, deps_conjoint: decesDepsConj, av: decesAv,
      },
      invalidite: {
        dep_total: v('inval-dep-total'),
        av_list: typeof _invalAvList !== 'undefined' ? _invalAvList : [],
      },
      valeurs_defaut: {
        province: v('vd-province'), fu: radio('vd-fu'), fu_mois: v('vd-fu-mois'),
        funerailles: v('vd-funerailles'), deces_rr: radio('vd-deces-rr'),
        deces_pct: v('vd-deces-pct'), deces_sal: radio('vd-deces-sal'),
        deces_freq: radio('vd-deces-freq'), inv_type: radio('vd-inv-type'),
        inv_sal: radio('vd-inv-sal'), inv_pct: v('vd-inv-pct'), mg: radio('vd-mg'),
        ret_pct: v('vd-ret-pct'), ret_freq: radio('vd-ret-freq'), ret_calc: radio('vd-ret-calc'),
        inflation: v('vd-inflation'), p_prudent: v('vd-p-prudent'), p_modere: v('vd-p-modere'),
        p_equilibre: v('vd-p-equilibre'), p_croissance: v('vd-p-croissance'), p_audacieux: v('vd-p-audacieux'),
      },
      hypotheses: typeof hypotheses !== 'undefined' ? { ...hypotheses } : { evClient: 94, evConj: 96 },
    };
  }

  function populateFromPayload(p) {
    if (!p) return;
    const sv = (id, val) => { const el = document.getElementById(id); if (el && val !== undefined && val !== null) el.value = val; };
    const sr = (name, val) => { if (!val) return; const el = document.querySelector(`input[name="${name}"][value="${val}"]`); if (el) el.checked = true; };

    // Type de plan (individuel / conjoint)
    if (p.has_spouse) {
      const radio = document.querySelector('input[name="plan"][value="conjoint"]');
      if (radio) { radio.checked = true; document.getElementById('conjoint-section').style.display = 'block'; syncConjointInfo(); }
    } else {
      const radio = document.querySelector('input[name="plan"][value="individuel"]');
      if (radio) radio.checked = true;
    }

    // Client
    const c = p.client || {};
    sv('client-prenom', c.prenom); sv('client-nom', c.nom);
    sv('client-ddn-jour', c.ddn_jour); sv('client-ddn-mois', c.ddn_mois);
    sv('client-naissance-annee', c.ddn_annee); sv('client-etat-civil', c.etat_civil);
    sv('client-province', c.province); sv('client-canada-depuis', c.canada_depuis);
    sv('client-addr-civique', c.addr_civique); sv('client-addr-rue', c.addr_rue);
    sv('client-addr-type-unite', c.addr_type_unite); sv('client-addr-numero', c.addr_numero);
    sv('client-addr-case', c.addr_case); sv('client-addr-ville', c.addr_ville);
    sv('client-addr-province', c.addr_province); sv('client-addr-postal', c.addr_postal);
    if (c.sexe) sr('sexe', c.sexe);

    // Conjoint
    const j = p.conjoint || {};
    sv('conjoint-prenom', j.prenom); sv('conjoint-nom', j.nom);
    sv('conjoint-ddn-jour', j.ddn_jour); sv('conjoint-ddn-mois', j.ddn_mois);
    sv('conjoint-naissance-annee', j.ddn_annee); sv('conjoint-etat-civil', j.etat_civil);
    sv('conjoint-province', j.province); sv('conjoint-canada-depuis', j.canada_depuis);
    sv('conjoint-addr-civique', j.addr_civique); sv('conjoint-addr-rue', j.addr_rue);
    sv('conjoint-addr-type-unite', j.addr_type_unite); sv('conjoint-addr-numero', j.addr_numero);
    sv('conjoint-addr-case', j.addr_case); sv('conjoint-addr-ville', j.addr_ville);
    sv('conjoint-addr-province', j.addr_province); sv('conjoint-addr-postal', j.addr_postal);
    if (j.sexe) sr('co-sexe', j.sexe);

    // Enfants
    const enfList = document.getElementById('enfants-list');
    if (enfList && (p.enfants || []).length) {
      enfList.classList.remove('list-empty'); enfList.innerHTML = '';
      const REL = { child:'Enfant', dependent:'Autre', fathermother:'Père-Mère', grandparent:'Grand-parent', grandchild:'Petit-enfant', sibling:'Frère-Sœur', otherrelative:'Parenté', exspouse:'Ex-conjoint(e)' };
      const CHARGE = { client:'Client', conjoint:'Conjoint', both:'Les deux', none:'Non à charge' };
      (p.enfants || []).forEach(d => {
        const item = document.createElement('div');
        item.className = 'enfant-item';
        item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px;gap:12px';
        item.dataset.charge = d.charge || ''; item.dataset.enfPrenom = d.prenom || '';
        item.dataset.enfNom = d.nom || ''; item.dataset.enfSexe = d.sexe || '';
        item.dataset.enfJour = d.jour || ''; item.dataset.enfMois = d.mois || '';
        item.dataset.enfAnnee = d.annee || ''; item.dataset.enfRelation = d.relation || '';
        item.innerHTML = _buildEnfantItemHTML(
          [d.prenom, d.nom].filter(Boolean).join(' '),
          REL[d.relation] || d.relation || '—',
          [d.jour, d.mois, d.annee].filter(Boolean).join(' ') || '—',
          d.sexe === 'M' ? 'Masculin' : (d.sexe === 'F' ? 'Féminin' : '—'),
          CHARGE[d.charge] || d.charge || ''
        );
        enfList.appendChild(item);
      });
    }

    // Revenus
    const revTbody = document.getElementById('revenu-list');
    if (revTbody && (p.revenus || []).length) {
      revTbody.innerHTML = '';
      (p.revenus || []).forEach(d => {
        const annuel = d.annuel || 0;
        const r = computeImpot ? computeImpot(annuel) : null;
        const fmt = n => n.toLocaleString('fr-CA', { maximumFractionDigits: 0 }) + ' $';
        const netLabel = r ? `<span style="font-size:11px;color:#22c55e;margin-left:4px">(net ${fmt(r.net)})</span>` : '';
        const tr = document.createElement('tr');
        tr.dataset.revenuAnnuel = annuel;
        tr.dataset.owner = d.owner || 'client';
        tr.dataset.revenuType = d.isEmploi ? 'emploi' : 'autre';
        tr.dataset.formJson = JSON.stringify(d);
        const ownerTx = d.owner === 'conjoint' ? (getConjointPrenom() || 'Conjoint(e)') : getClientPrenom();
        tr.innerHTML = `
          <td>${ownerTx}</td><td>${d.isEmploi ? 'Emploi' : 'Autre'}</td>
          <td>${d.description || ''}</td>
          <td>${d.montant || '0'} $${netLabel}</td>
          <td>${d.frequence || 'Annuelle'}</td>
          <td class="col-action">
            <button class="re-action-btn" title="Détail fiscal" onclick="reToggleDetail(this)" style="color:var(--navy)">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2.5"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <button class="re-action-btn del" title="Supprimer" onclick="reDeleteRow(this)">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </td>`;
        revTbody.appendChild(tr);
        const trDetail = document.createElement('tr');
        trDetail.className = 're-detail-row'; trDetail.style.display = 'none';
        trDetail.innerHTML = r ? `<td colspan="6"><div class="re-detail-inner">
          <div class="re-detail-item"><span class="re-detail-lbl">RRQ</span><span class="re-detail-val deduction">${fmt(r.rrq)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">AE</span><span class="re-detail-val deduction">${fmt(r.ae)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">RQAP</span><span class="re-detail-val deduction">${fmt(r.rqap)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Impôt fédéral</span><span class="re-detail-val deduction">${fmt(r.fed)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Impôt Québec</span><span class="re-detail-val deduction">${fmt(r.qc)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Taux effectif</span><span class="re-detail-val">${r.taux.toFixed(1).replace('.', ',')} %</span></div>
          <div class="re-detail-item" style="grid-column:1/3"><span class="re-detail-lbl">Net annuel</span><span class="re-detail-val net">${fmt(r.net)}</span></div>
          <div class="re-detail-item"><span class="re-detail-lbl">Net mensuel</span><span class="re-detail-val net">${fmt(r.net / 12)}</span></div>
        </div></td>` : `<td colspan="6"><div style="padding:8px 14px;font-size:12px;color:var(--muted)">Calcul non disponible.</div></td>`;
        revTbody.appendChild(trDetail);
      });
      if (typeof updateReSidebar === 'function') updateReSidebar();
    }

    // Actifs
    if ((p.actifs || []).length) {
      const actifsList = document.getElementById('actifs-list');
      if (actifsList) { actifsList.classList.remove('list-empty'); actifsList.innerHTML = ''; }
      (p.actifs || []).forEach(d => {
        const valNum = d._valeur || 0;
        const valTxt = valNum.toLocaleString('fr-CA') + ' $';
        const sub = d.portefeuille ? (d.portefeuille + (d.rendement ? ' · ' + d.rendement + '%' : '')) : '';
        const { _type, _valeur, _owner, _modalType, _partClient, _partConjoint, ...formData } = d;
        apAddToList('actifs-list', _type, d.description || _type || '', valTxt, sub, valNum, _owner, _modalType, JSON.stringify(formData), _partClient, _partConjoint);
      });
    }

    // Passifs
    if ((p.passifs || []).length) {
      const passifsList = document.getElementById('passifs-list');
      if (passifsList) { passifsList.classList.remove('list-empty'); passifsList.innerHTML = ''; }
      (p.passifs || []).forEach(d => {
        const valNum = d._valeur || 0;
        const valTxt = valNum.toLocaleString('fr-CA') + ' $';
        const { _type, _valeur, _owner, _modalType, _partClient, _partConjoint, ...formData } = d;
        apAddToList('passifs-list', _type, d.description || _type || '', valTxt, '', valNum, _owner, _modalType, JSON.stringify(formData), _partClient, _partConjoint);
      });
    }

    // Documents légaux
    const legalList = document.getElementById('legal-list');
    if (legalList && (p.legal || []).length) {
      legalList.classList.remove('list-empty'); legalList.innerHTML = '';
      (p.legal || []).forEach(d => {
        const item = document.createElement('div');
        item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px;gap:8px';
        item.dataset.formJson = JSON.stringify(d);
        item.innerHTML = `
          <span style="display:flex;align-items:center;gap:8px">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
            <span style="color:var(--text);font-weight:500">${d.docType || ''}</span>
            ${d.propText ? `<span style="color:var(--muted);font-size:11px">· ${d.propText}</span>` : ''}
            ${d.typeText && d.legalType ? `<span style="color:var(--muted);font-size:11px">· ${d.typeText}</span>` : ''}
          </span>
          <button onclick="this.closest('div[style]').remove()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0 4px">×</button>`;
        legalList.appendChild(item);
      });
    }

    // Décès — champs simples
    const dd = p.deces || {};
    sv('deces-rrq-client', dd.rrq_client); sv('deces-rrq-conjoint', dd.rrq_conjoint);
    sv('deces-autres-revenus-c', dd.autres_revenus_c); sv('deces-autres-revenus-j', dd.autres_revenus_j);
    sv('deces-rr-pct-c', dd.rr_pct_c); sv('deces-rr-pct-j', dd.rr_pct_j);
    sv('deces-rr-duree-c', dd.rr_duree_c); sv('deces-rr-duree-j', dd.rr_duree_j);
    sv('deces-rr-taux-c', dd.rr_taux_c); sv('deces-rr-taux-j', dd.rr_taux_j);

    // Décès — dépenses
    ['client', 'conjoint'].forEach(who => {
      const listId = who === 'conjoint' ? 'deces-dep-list-conjoint' : 'deces-dep-list';
      const deps = who === 'conjoint' ? (dd.deps_conjoint || []) : (dd.deps_client || []);
      if (!deps.length) return;
      const depList = document.getElementById(listId);
      if (depList) depList.innerHTML = '';
      deps.forEach(d => {
        const uid = Math.random().toString(36).slice(2);
        const row = document.createElement('div');
        row.className = 'deces-dep-row';
        row.dataset.montant = d.montant || 0;
        row.dataset.desc = d.desc || '';
        row.dataset.indexed = d.indexed || 'oui';
        const mt = (+(d.montant || 0)).toLocaleString('fr-CA');
        row.innerHTML = `
          <span style="flex:1;color:var(--text)">${d.desc || ''}</span>
          <div class="input-sfx" style="max-width:140px">
            <input class="form-input" type="text" value="${mt}" placeholder="0"
              oninput="this.closest('[data-montant]').dataset.montant=parseFloat(this.value.replace(/\\s/g,'').replace(',','.'))||0;decesCalc()"/>
            <span class="sfx">$</span>
          </div>
          <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
            <span style="font-size:10px;color:var(--muted);white-space:nowrap">Indexé à l'inflation</span>
            <div style="display:flex;gap:4px">
              <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="oui" ${(d.indexed||'oui')==='oui'?'checked':''} onchange="this.closest('.deces-dep-row').dataset.indexed='oui'"/> Oui</label>
              <label class="fu-radio-pill" style="padding:4px 8px;font-size:11px"><input type="radio" name="deces-idx-${uid}" value="non" ${(d.indexed||'oui')==='non'?'checked':''} onchange="this.closest('.deces-dep-row').dataset.indexed='non'"/> Non</label>
            </div>
          </div>
          <button onclick="this.closest('.deces-dep-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>`;
        if (depList) depList.appendChild(row);
      });
    });

    // Décès — assurances vie
    if ((dd.av || []).length) {
      const avList = document.getElementById('deces-av-list');
      if (avList) {
        const empty = document.getElementById('deces-av-empty');
        if (empty) empty.remove();
        (dd.av || []).forEach(d => {
          const montant = d.montant || 0;
          const prime = d.prime || 0;
          const row = document.createElement('div');
          row.className = 'deces-av-row';
          row.dataset.montant = d.exclure ? 0 : montant;
          row.dataset.ownerVal = d.ownerVal || '';
          row.dataset.formJson = JSON.stringify(d);
          row.innerHTML = `
            <div style="flex:1">
              <div style="font-weight:600">${d.type || ''} — ${d.owner || ''}</div>
              <div style="font-size:11px;color:var(--muted)">${d.assureur || ''}${d.benef ? ' · Bénéficiaire: ' + d.benef : ''}${d.exclure ? ' · <em>Exclu de l\'analyse</em>' : ''}</div>
            </div>
            <div style="text-align:right;margin-right:12px">
              <div style="font-weight:700">${typeof fmtMoney === 'function' ? fmtMoney(montant) : montant}</div>
              ${prime > 0 ? `<div style="font-size:11px;color:var(--muted)">Prime: ${typeof fmtMoney === 'function' ? fmtMoney(prime) : prime}/an</div>` : ''}
            </div>
            <button onclick="this.closest('.deces-av-row').remove();decesCalc()" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:18px;padding:0 4px">×</button>`;
          avList.appendChild(row);
        });
      }
    }

    // Valeurs par défaut
    const vd = p.valeurs_defaut || {};
    sv('vd-province', vd.province); sv('vd-fu-mois', vd.fu_mois); sv('vd-funerailles', vd.funerailles);
    sv('vd-deces-pct', vd.deces_pct); sv('vd-inv-pct', vd.inv_pct); sv('vd-ret-pct', vd.ret_pct);
    sv('vd-inflation', vd.inflation); sv('vd-p-prudent', vd.p_prudent); sv('vd-p-modere', vd.p_modere);
    sv('vd-p-equilibre', vd.p_equilibre); sv('vd-p-croissance', vd.p_croissance); sv('vd-p-audacieux', vd.p_audacieux);
    if (vd.fu) sr('vd-fu', vd.fu);
    if (vd.deces_rr) sr('vd-deces-rr', vd.deces_rr);
    if (vd.deces_sal) sr('vd-deces-sal', vd.deces_sal);
    if (vd.deces_freq) sr('vd-deces-freq', vd.deces_freq);
    if (vd.inv_type) sr('vd-inv-type', vd.inv_type);
    if (vd.inv_sal) sr('vd-inv-sal', vd.inv_sal);
    if (vd.mg) sr('vd-mg', vd.mg);
    if (vd.ret_freq) sr('vd-ret-freq', vd.ret_freq);
    if (vd.ret_calc) sr('vd-ret-calc', vd.ret_calc);

    // Hypothèses
    if (p.hypotheses && typeof hypotheses !== 'undefined') {
      hypotheses.evClient = p.hypotheses.evClient ?? 94;
      hypotheses.evConj = p.hypotheses.evConj ?? 96;
    }

    // Invalidité
    const inv = p.invalidite || {};
    sv('inval-dep-total', inv.dep_total);
    if (typeof _invalAvList !== 'undefined' && Array.isArray(inv.av_list)) {
      _invalAvList = inv.av_list;
      if (typeof invalRenderAvList === 'function') invalRenderAvList();
    }

    // Recalculs
    setTimeout(() => {
      if (typeof syncConjointInfo === 'function') syncConjointInfo();
      if (typeof updateApSidebar === 'function') updateApSidebar();
      if (typeof updateReSidebar === 'function') updateReSidebar();
      if (typeof decesCalc === 'function') decesCalc();
      if (typeof invaliditeCalc === 'function') invaliditeCalc();
      if (typeof updateEpargneSection === 'function') updateEpargneSection();
    }, 100);
  }

  function autoSave(recordId, saveUrl, csrfToken, silent) {
    const payload = gatherPayload();
    fetch(saveUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ payload }),
    })
    .then(r => r.json())
    .then(data => { if (!silent && data.ok) showToast('Brouillon sauvegardé'); })
    .catch(() => {});
  }

  function initAutoSave(recordId, saveUrl, csrfToken) {
    setInterval(() => autoSave(recordId, saveUrl, csrfToken, true), 30000);
    window.addEventListener('beforeunload', () => autoSave(recordId, saveUrl, csrfToken, true));
  }

  /* ── INITIALISATION LARAVEL ──────────────────────────── */
  // Saute page-accueil seulement si le dossier a déjà des données
  if (window.ABF_INITIAL_PAYLOAD && window.ABF_INITIAL_PAYLOAD.client?.prenom) {
    demarrerABF();
    populateFromPayload(window.ABF_INITIAL_PAYLOAD);
  }
  if (window.ABF_SAVE_URL) {
    initAutoSave(window.ABF_RECORD_ID, window.ABF_SAVE_URL, window.ABF_CSRF_TOKEN);
  }
</script>
</body>
</html>
