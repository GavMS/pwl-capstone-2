@extends('dashboard.layout')
@section('title', 'Labeling Inventaris')
@section('page_title', 'Labeling Inventaris')

@section('content')
<style>
/* ─── Base ─────────────────────────────────────────── */
.page-header-card{background:#fff;border-radius:1rem;box-shadow:0 20px 27px 0 rgba(0,0,0,.05);padding:1.5rem;margin-bottom:1.5rem;}
.page-title{font-size:1.25rem;font-weight:700;color:#344767;margin:0 0 .25rem;}
.page-subtitle{font-size:.875rem;color:#7b809a;margin:0;}
.alert-box{padding:.875rem 1.25rem;border-radius:.75rem;font-size:.875rem;font-weight:600;display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;}
.alert-success{background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
.info-banner{display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;background:#f5f3ff;border:1px solid #ddd6fe;border-radius:.75rem;margin-bottom:1.25rem;font-size:.8125rem;color:#6d28d9;font-weight:600;}
.toolbar-card{background:#fff;border-radius:1rem;box-shadow:0 20px 27px 0 rgba(0,0,0,.05);padding:1.25rem;margin-bottom:1.5rem;display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between;}
.search-input{padding:.5rem .875rem;font-size:.8125rem;border:1px solid #d2d6da;border-radius:.5rem;color:#344767;outline:none;width:260px;}
.search-input:focus{border-color:#7928ca;box-shadow:0 0 0 2px rgba(121,40,202,.12);}
.select-filter{padding:.5rem .875rem;font-size:.8125rem;border:1px solid #d2d6da;border-radius:.5rem;color:#344767;background:#fff;cursor:pointer;}
.table-card{background:#fff;border-radius:1rem;box-shadow:0 20px 27px 0 rgba(0,0,0,.05);overflow:hidden;}
.inv-table{width:100%;border-collapse:collapse;}
.inv-table thead th{padding:.85rem 1rem;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#adb5bd;border-bottom:1px solid #f0f2f5;text-align:left;white-space:nowrap;}
.inv-table tbody td{padding:.75rem 1rem;border-bottom:1px solid #f0f2f5;vertical-align:middle;color:#475569;font-size:.85rem;}
.inv-table tbody tr.asset-row{cursor:default;}
.inv-table tbody tr.asset-row:hover td{background:#fafbfc;}
.item-title{font-size:.875rem;font-weight:700;color:#344767;margin:0;}
.item-draft-link{font-size:.72rem;color:#2152ff;text-decoration:none;font-weight:600;}
.item-draft-link:hover{text-decoration:underline;}
.cell-input{padding:.4rem .6rem;font-size:.78rem;border:1px solid #d2d6da;border-radius:.4rem;color:#495057;outline:none;transition:border-color .2s,box-shadow .2s;width:100%;box-sizing:border-box;}
.cell-input:focus{border-color:#7928ca;box-shadow:0 0 0 2px rgba(121,40,202,.12);}
.label-input{font-family:'Courier New',monospace;font-weight:600;}

/* ─── Status pills ──────────────────────────────── */
.status-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .6rem;border-radius:.375rem;font-size:.68rem;font-weight:700;text-transform:uppercase;}
.status-recv{background:#dcfce7;color:#15803d;}
.status-part{background:#e0f2fe;color:#0369a1;}
.status-wait{background:#fef3c7;color:#d97706;}

/* ─── Buttons ────────────────────────────────────── */
.btn-gen{width:32px;height:32px;background:#e2e8f0;color:#475569;border:none;border-radius:.375rem;cursor:pointer;font-size:.72rem;transition:background .15s;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;}
.btn-gen:hover{background:#cbd5e1;}

.btn-save-date{display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .85rem;font-size:.75rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#0369a1,#0ea5e9);border:none;border-radius:.5rem;cursor:pointer;transition:opacity .15s,transform .1s;box-shadow:0 2px 6px -1px rgba(3,105,161,.35);white-space:nowrap;}
.btn-save-date:hover{opacity:.88;transform:translateY(-1px);}
.btn-save-date:disabled{opacity:.45;cursor:not-allowed;transform:none;}

.btn-labeli{display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .85rem;font-size:.75rem;font-weight:700;color:#7928ca;background:#f5f3ff;border:1px solid #ddd6fe;border-radius:.5rem;cursor:pointer;transition:all .2s;white-space:nowrap;}
.btn-labeli:hover:not(:disabled){background:#ede9fe;transform:translateY(-1px);}
.btn-labeli.active{background:#7928ca;color:#fff;border-color:#7928ca;}
.btn-labeli:disabled{opacity:.38;cursor:not-allowed;}

.btn-save{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;font-size:.78rem;font-weight:700;color:#fff;background:linear-gradient(310deg,#7928ca,#ff0080);border:none;border-radius:.5rem;cursor:pointer;transition:opacity .15s,transform .1s,box-shadow .15s;box-shadow:0 3px 8px -2px rgba(121,40,202,.4);white-space:nowrap;}
.btn-save:hover{opacity:.88;transform:translateY(-1px);}
.btn-save.saving{opacity:.6;cursor:not-allowed;}
.btn-save-wrap{transition:opacity .2s,max-height .25s;}
.btn-save-wrap.hidden{display:none;}

/* ─── Tgl.Diterima column ────────────────────────── */
.date-input-wrapper{display:flex;gap:.4rem;align-items:center;}

/* ─── Accordion panel ───────────────────────────── */
.label-panel-row{background:#f8fafc !important;}
.label-panel-row td{padding:0 !important;border-bottom:2px solid #7928ca !important;}
.panel-inner{padding:1.5rem;background:#f8fafc;}
.panel-header{display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:1px solid #e2e8f0;}
.panel-title{font-size:.95rem;font-weight:700;color:#344767;margin:0;}
.units-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1rem;}

/* ─── Unit card ─────────────────────────────────── */
.unit-card{background:#fff;border:1px solid #e2e8f0;border-radius:.875rem;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,.04);transition:box-shadow .2s,transform .2s;}
.unit-card:hover{box-shadow:0 10px 20px -3px rgba(0,0,0,.08);transform:translateY(-2px);}
.unit-card-head{display:flex;justify-content:space-between;align-items:center;padding:.75rem 1rem;background:linear-gradient(135deg,#faf5ff,#f5f3ff);border-bottom:1px solid #e9d5ff;}
.unit-badge{font-size:.75rem;font-weight:700;color:#7928ca;background:#ede9fe;padding:.2rem .6rem;border-radius:.375rem;}
.unit-card-body{padding:1rem;}
.form-label{display:block;font-size:.67rem;font-weight:700;color:#64748b;margin-bottom:.35rem;text-transform:uppercase;letter-spacing:.04em;}
.qr-row{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.875rem;background:#f8fafc;border-radius:.5rem;padding:.75rem;}
.qr-col{display:flex;flex-direction:column;align-items:center;gap:.4rem;}
.qr-label{font-size:.6rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;}
.qr-preview{width:68px;height:68px;border:2px dashed #cbd5e1;border-radius:.375rem;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;}
.qr-preview img{width:100%;height:100%;object-fit:contain;}
.muted{color:#cbd5e1;font-size:.8rem;}

/* Upload */
.upload-wrap{position:relative;overflow:hidden;display:inline-block;}
.btn-upload{border:1px solid #cbd5e1;color:#475569;background:#fff;padding:.22rem .55rem;border-radius:.375rem;font-size:.68rem;font-weight:700;cursor:pointer;transition:all .2s;}
.btn-upload:hover{background:#f1f5f9;}
.upload-wrap input[type=file]{font-size:100px;position:absolute;left:0;top:0;opacity:0;cursor:pointer;width:100%;height:100%;}

.empty-state{padding:3rem 1rem;text-align:center;color:#adb5bd;}
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">
        <div class="page-header-card">
            <h4 class="page-title"><i class="fas fa-qrcode mr-2" style="color:#7928ca;"></i> Pencetakan & Labeling Inventaris</h4>
            <p class="page-subtitle">
                <strong>Alur:</strong>
                Semua barang diterima &rarr; Staf Admin isi <em>Tgl. Diterima</em> per jenis barang &rarr; Klik <em>Simpan Tgl</em> &rarr; Baru bisa klik <em>Labeli</em> untuk memberi nomor label &amp; QR per unit.
            </p>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="w-full px-3">
        <div class="info-banner">
            <i class="fas fa-info-circle" style="font-size:1rem;flex-shrink:0;"></i>
            <span>Proses labeling <strong>hanya dapat dilakukan di halaman ini</strong>. Halaman detail draf pengadaan bersifat <em>view-only</em>.</span>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="w-full px-3"><div class="alert-box alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div></div>
    @endif
    @if($errors->any())
    <div class="w-full px-3"><div class="alert-box alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div></div>
    @endif
    @if(isset($error) && $error)
    <div class="w-full px-3"><div class="alert-box alert-error"><i class="fas fa-exclamation-circle"></i>{{ $error }}</div></div>
    @endif

    {{-- Toolbar --}}
    <div class="w-full px-3">
        <div class="toolbar-card">
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
                <input type="text" id="searchInput" class="search-input" placeholder="Cari barang atau pengadaan..." onkeyup="filterRows()">
                <select id="yearFilter" class="select-filter" onchange="filterRows()">
                    <option value="all">Semua Tahun</option>
                    @foreach(collect($assets)->pluck('draft_year')->unique()->sort()->values() as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div style="font-size:.75rem;color:#adb5bd;font-weight:600;">Total: <span id="displayedCount">{{ collect($assets)->groupBy('source_item_id')->count() }}</span> jenis barang</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="w-full px-3">
        <div class="table-card">
            <div style="overflow-x:auto;">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width:42px;text-align:center;">No</th>
                            <th style="width:70px;">Tahun</th>
                            <th style="min-width:220px;">Nama Barang / Pengadaan</th>
                            <th style="width:52px;text-align:center;">Qty</th>
                            <th style="width:220px;">Tgl. Diterima</th>
                            <th style="width:130px;text-align:center;">Status</th>
                            <th style="width:110px;text-align:center;">Labeli</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedAssets = collect($assets)->groupBy('source_item_id');
                            $rowIdx = 0;
                        @endphp

                        @forelse($groupedAssets as $sourceItemId => $units)
                        @php
                            $rowIdx++;
                            $firstUnit   = $units->first();
                            $totalUnits  = $units->count();

                            // Semua unit pakai received_date yang sama (satu per procurement item)
                            $sharedDate  = $firstUnit['received_date'] ?? null;
                            $isReceived  = !empty($sharedDate);

                            $labeledUnits = $units->filter(fn($u) => !empty($u['label_number']))->count();
                            if (!$isReceived) {
                                $statusText  = 'Belum Diterima';
                                $statusClass = 'status-wait';
                            } elseif ($labeledUnits === $totalUnits) {
                                $statusText  = 'Sudah Dilabeli';
                                $statusClass = 'status-recv';
                            } elseif ($labeledUnits > 0) {
                                $statusText  = 'Sedang Dilabeli';
                                $statusClass = 'status-part';
                            } else {
                                $statusText  = 'Belum Dilabeli';
                                $statusClass = 'status-part';
                            }
                        @endphp

                        {{-- Main row --}}
                        <tr class="asset-row"
                            id="row-{{ $sourceItemId }}"
                            data-id="{{ $sourceItemId }}"
                            data-year="{{ $firstUnit['draft_year'] }}"
                            data-name="{{ strtolower($firstUnit['name'].' '.($firstUnit['draft_title'] ?? '')) }}">

                            <td style="text-align:center;font-weight:700;">{{ $rowIdx }}</td>
                            <td style="font-weight:700;color:#344767;">{{ $firstUnit['draft_year'] }}</td>
                            <td>
                                <p class="item-title">{{ $firstUnit['name'] }}</p>
                                <a href="{{ route('stafadmin.procurement.show', $firstUnit['draft_id']) }}" class="item-draft-link">
                                    <i class="fas fa-file-invoice"></i> {{ $firstUnit['draft_title'] }}
                                </a>
                                @if(!empty($firstUnit['replaced_asset_name']))
                                <div style="font-size:.7rem;color:#f59e0b;margin-top:.15rem;">
                                    <i class="fas fa-exchange-alt"></i> Menggantikan: {{ $firstUnit['replaced_asset_name'] }}
                                </div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span style="background:#f1f5f9;padding:.2rem .55rem;border-radius:.35rem;font-weight:700;">{{ $totalUnits }}</span>
                            </td>

                            {{-- Tgl. Diterima — satu input per procurement item --}}
                            <td>
                                <form method="POST"
                                      action="{{ route('stafadmin.inventaris.setReceived', $sourceItemId) }}"
                                      id="date-form-{{ $sourceItemId }}"
                                      onsubmit="onSaveDateClick(event, {{ $sourceItemId }})">
                                    @csrf
                                    <div class="date-input-wrapper">
                                        <input type="date" name="received_date"
                                               id="date-input-{{ $sourceItemId }}"
                                               class="cell-input"
                                               value="{{ $sharedDate ?? '' }}"
                                               max="{{ date('Y-m-d') }}"
                                               style="width:140px;flex-shrink:0;"
                                               onchange="onDateChange({{ $sourceItemId }})">
                                        <button type="submit" class="btn-save-date" id="save-date-btn-{{ $sourceItemId }}">
                                            <i class="fas fa-calendar-check"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </td>

                            <td style="text-align:center;">
                                <span class="status-pill {{ $statusClass }}">{{ $statusText }}</span>
                            </td>

                            {{-- Tombol Labeli — disabled kalau belum ada tgl diterima --}}
                            <td style="text-align:center;">
                                <button type="button"
                                        class="btn-labeli"
                                        id="btn-labeli-{{ $sourceItemId }}"
                                        onclick="toggleLabeliPanel({{ $sourceItemId }})"
                                        {{ !$isReceived ? 'disabled' : '' }}
                                        title="{{ !$isReceived ? 'Isi tanggal diterima terlebih dahulu' : 'Buka panel labeling' }}">
                                    <i class="fas fa-tags"></i> Labeli
                                </button>
                            </td>
                        </tr>

                        {{-- Accordion labeling panel --}}
                        <tr id="label-panel-{{ $sourceItemId }}" class="label-panel-row" style="display:none;">
                            <td colspan="7">
                                <div class="panel-inner">
                                    <div class="panel-header">
                                        <i class="fas fa-tags" style="color:#7928ca;font-size:1.1rem;"></i>
                                        <h5 class="panel-title">Labeling Unit — {{ $firstUnit['name'] }}</h5>
                                        <span style="font-size:.75rem;color:#7b809a;">{{ $totalUnits }} unit</span>
                                    </div>
                                    <form id="bulk-form-{{ $sourceItemId }}" method="POST"
                                          action="{{ route('stafadmin.inventaris.bulkLabel') }}"
                                          onsubmit="onBulkSaveClick(event, '{{ $sourceItemId }}')">
                                        @csrf
                                        <div class="units-grid">
                                            @foreach($units as $unitIdx => $unit)
                                            @php $uid = $unit['id']; @endphp
                                            <div class="unit-card">
                                                <div class="unit-card-head">
                                                    <span class="unit-badge">Unit #{{ $unitIdx + 1 }}</span>
                                                    <span class="status-pill {{ !empty($unit['label_number']) ? 'status-recv' : 'status-wait' }}">
                                                        {{ !empty($unit['label_number']) ? 'Sudah Dilabel' : 'Belum Dilabel' }}
                                                    </span>
                                                </div>
                                                <div class="unit-card-body">
                                                    <input type="hidden" name="qr_data_urls[{{ $uid }}]" id="qr-data-{{ $uid }}">
                                                    <input type="hidden" name="univ_qr_data_urls[{{ $uid }}]" id="univ-qr-data-{{ $uid }}">

                                                    {{-- Nomor Label --}}
                                                    <div style="margin-bottom:.875rem;">
                                                        <label class="form-label">Nomor Label</label>
                                                        <div style="display:flex;gap:.4rem;align-items:center;">
                                                            <input type="text" name="labels[{{ $uid }}]"
                                                                   id="label-{{ $uid }}"
                                                                   class="cell-input label-input label-trigger"
                                                                   data-id="{{ $uid }}"
                                                                   data-group="{{ $sourceItemId }}"
                                                                   data-year="{{ $unit['draft_year'] }}"
                                                                   value="{{ $unit['label_number'] ?? '' }}"
                                                                   placeholder="INV-{{ $unit['draft_year'] }}-..."
                                                                   autocomplete="off">
                                                            <button type="button" class="btn-gen" title="Auto-generate"
                                                                    onclick="genLabel({{ $uid }}, {{ $unit['draft_year'] }})">
                                                                <i class="fas fa-magic"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    {{-- QR --}}
                                                    <div class="qr-row">
                                                        <div class="qr-col">
                                                            <span class="qr-label">QR Prodi (Auto)</span>
                                                            <div class="qr-preview" id="qrprev-{{ $uid }}">
                                                                @if(!empty($unit['qr_path']))
                                                                    <img src="{{ $unit['qr_path'] }}" alt="QR Prodi">
                                                                @else
                                                                    <span class="muted">—</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="qr-col">
                                                            <span class="qr-label">QR Universitas</span>
                                                            <div class="qr-preview" id="univ-qrprev-{{ $uid }}">
                                                                @if(!empty($unit['univ_qr_path']))
                                                                    <img src="{{ $unit['univ_qr_path'] }}" alt="QR Univ">
                                                                @else
                                                                    <span class="muted">—</span>
                                                                @endif
                                                            </div>
                                                            <div class="upload-wrap" style="margin-top:.35rem;">
                                                                <button type="button" class="btn-upload">
                                                                    <i class="fas fa-upload"></i> Upload
                                                                </button>
                                                                <input type="file" accept="image/*"
                                                                       onchange="handleUnivQrUpload(this, {{ $uid }})">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            @endforeach
                                        </div>{{-- /units-grid --}}

                                        {{-- Bulk save button --}}
                                        @php
                                            $hasAnyLabel = $units->contains(fn($u) => !empty($u['label_number']));
                                        @endphp
                                        <div class="btn-save-wrap {{ !$hasAnyLabel ? 'hidden' : '' }}" id="bulk-save-wrap-{{ $sourceItemId }}" style="text-align:right;margin-top:1.5rem;padding-top:1rem;border-top:1px solid #e2e8f0;">
                                            <button type="submit" class="btn-save" id="bulk-save-btn-{{ $sourceItemId }}">
                                                <i class="fas fa-save"></i> Simpan Semua Label
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-box-open" style="font-size:2.5rem;color:#cbd5e1;display:block;margin-bottom:.75rem;"></i>
                                    <p>Belum ada aset inventaris dari pengadaan yang difinalisasi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
/* ── Accordion ──────────────────────────────────────── */
function toggleLabeliPanel(id) {
    const panel = document.getElementById('label-panel-' + id);
    const btn   = document.getElementById('btn-labeli-' + id);
    if (!panel) return;
    const open = panel.style.display !== 'none';
    panel.style.display = open ? 'none' : 'table-row';
    btn && btn.classList.toggle('active', !open);
}

/* ── Tanggal Diterima — enable/disable Labeli btn ───── */
function onDateChange(id) {
    const dateInput = document.getElementById('date-input-' + id);
    const labeli    = document.getElementById('btn-labeli-' + id);
    if (!labeli) return;
    const hasDate = dateInput && dateInput.value.trim() !== '';
    labeli.disabled = !hasDate;
    labeli.title = hasDate ? 'Buka panel labeling' : 'Isi tanggal diterima terlebih dahulu';
}

function onSaveDateClick(event, id) {
    const btn = document.getElementById('save-date-btn-' + id);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    }
}

/* ── QR generate ────────────────────────────────────── */
function genQRDataUrl(text) {
    if (!text) return '';
    const tmp = document.createElement('div');
    tmp.style.cssText = 'display:none;position:fixed;left:-9999px;';
    document.body.appendChild(tmp);
    new QRCode(tmp, { text, width: 200, height: 200, correctLevel: QRCode.CorrectLevel.H });
    let url = '';
    const canvas = tmp.querySelector('canvas');
    if (canvas) { url = canvas.toDataURL('image/png'); }
    else { const img = tmp.querySelector('img'); if (img) url = img.src; }
    document.body.removeChild(tmp);
    return url;
}

function refreshQR(id) {
    const input  = document.getElementById('label-' + id);
    const hidden = document.getElementById('qr-data-' + id);
    const prev   = document.getElementById('qrprev-' + id);
    if (!input || !hidden) return;
    const val = input.value.trim();
    if (val) {
        const url = genQRDataUrl(val);
        hidden.value = url;
        if (prev) prev.innerHTML = '<img src="' + url + '" alt="QR">';
    } else {
        hidden.value = '';
        if (prev) prev.innerHTML = '<span class="muted">&mdash;</span>';
    }
}

function revealBulkSave(groupId) {
    const wrap = document.getElementById('bulk-save-wrap-' + groupId);
    if (wrap) wrap.classList.remove('hidden');
}

function genLabel(id, year) {
    const input = document.getElementById('label-' + id);
    if (!input) return;
    input.value = 'INV-' + year + '-' + String(id).padStart(3, '0');
    refreshQR(id);
    const groupId = input.dataset.group;
    if (groupId) revealBulkSave(groupId);
}

/* ── QR Universitas upload ──────────────────────────── */
function handleUnivQrUpload(inputEl, id) {
    const file = inputEl.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const dataUrl = e.target.result;
        const hidden  = document.getElementById('univ-qr-data-' + id);
        if (hidden) hidden.value = dataUrl;
        const prev = document.getElementById('univ-qrprev-' + id);
        if (prev) prev.innerHTML = '<img src="' + dataUrl + '" alt="QR Univ">';
    };
    reader.readAsDataURL(file);
}

/* ── Bulk Save group ──────────────────────────────────────── */
function onBulkSaveClick(event, groupId) {
    // Refresh all QRs in this group before submit
    const inputs = document.querySelectorAll('.label-input[data-group="' + groupId + '"]');
    inputs.forEach(inp => {
        refreshQR(inp.dataset.id);
    });
    
    const btn = document.getElementById('bulk-save-btn-' + groupId);
    if (btn) {
        btn.classList.add('saving');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    }
}

/* ── DOMContentLoaded ───────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    // Inisialisasi hidden QR untuk label yang sudah tersimpan
    document.querySelectorAll('.label-trigger').forEach(inp => {
        const id = inp.dataset.id;
        // Tampilkan Simpan kalau user mengetik manual di kolom label
        inp.addEventListener('input', () => {
            refreshQR(id);
            if (inp.value.trim() !== '') {
                const groupId = inp.dataset.group;
                if (groupId) revealBulkSave(groupId);
            }
        });
        if (inp.value.trim() !== '') {
            const hidden = document.getElementById('qr-data-' + id);
            if (hidden) hidden.value = genQRDataUrl(inp.value.trim());
        }
    });
});

/* ── Filter pencarian + tahun ────────────────────────── */
function filterRows() {
    const q    = document.getElementById('searchInput').value.toLowerCase();
    const year = document.getElementById('yearFilter').value;
    let count  = 0;
    document.querySelectorAll('.asset-row').forEach(row => {
        const matchQ    = row.dataset.name.includes(q);
        const matchYear = (year === 'all') || (row.dataset.year === year);
        const visible   = matchQ && matchYear;
        row.style.display = visible ? '' : 'none';
        const panel = document.getElementById('label-panel-' + row.dataset.id);
        if (panel && !visible) panel.style.display = 'none';
        if (visible) count++;
    });
    document.getElementById('displayedCount').textContent = count;
}
</script>
@endsection
