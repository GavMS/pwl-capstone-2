@extends('dashboard.layout')
@section('title', 'Labeling Inventaris')
@section('page_title', 'Labeling Inventaris')

@section('content')
<style>
/* ─── Premium Page Styles ─────────────────────────── */
.page-header-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}
.page-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #344767;
    margin: 0 0 .25rem;
}
.page-subtitle {
    font-size: .875rem;
    color: #7b809a;
    margin: 0;
}

/* ─── Filters & Actions ───────────────────────────── */
.toolbar-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.toolbar-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}
.filter-group {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
}
.search-wrap { position: relative; }
.search-wrap .search-icon {
    position: absolute;
    left: .75rem; top: 50%;
    transform: translateY(-50%);
    color: #adb5bd; font-size: .75rem;
    pointer-events: none;
}
.search-wrap input {
    padding: .5rem .875rem .5rem 2.2rem;
    font-size: .8125rem;
    border: 1px solid #d2d6da;
    border-radius: .5rem;
    color: #344767; outline: none;
    width: 260px;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus {
    border-color: #2152ff;
    box-shadow: 0 0 0 2px rgba(33,82,255,.15);
}

.select-filter {
    padding: .5rem 1.75rem .5rem .875rem;
    font-size: .8125rem;
    border: 1px solid #d2d6da;
    border-radius: .5rem;
    color: #344767; outline: none;
    background-color: #fff;
    cursor: pointer;
    transition: border-color .2s;
}
.select-filter:focus { border-color: #2152ff; }

/* ─── Table Premium Styles ────────────────────────── */
.table-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    overflow: hidden;
}
.inventaris-table { width: 100%; border-collapse: collapse; }
.inventaris-table thead th {
    padding: .85rem 1.25rem;
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #adb5bd; border-bottom: 1px solid #f0f2f5;
    white-space: nowrap; background: #fff;
    text-align: left;
}
.inventaris-table tbody td {
    padding: .875rem 1.25rem;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
    color: #475569;
    font-size: .85rem;
}
.inventaris-table tbody tr:last-child td { border-bottom: none; }
.inventaris-table tbody tr:hover { background: #fafbfc; }

.item-title { font-size: .875rem; font-weight: 600; color: #344767; margin: 0; }
.item-draft-link {
    font-size: .75rem; color: #2152ff; text-decoration: none;
    font-weight: 600; display: inline-flex; align-items: center; gap: .25rem;
}
.item-draft-link:hover { text-decoration: underline; }

/* ─── Input & Generate Styles ─────────────────────── */
.label-input-wrap {
    display: flex;
    align-items: center;
    gap: .35rem;
}
.label-input {
    width: 145px;
    padding: .35rem .6rem;
    font-size: .75rem;
    border: 1px solid #d2d6da;
    border-radius: .375rem;
    color: #495057;
    transition: all .15s ease;
    font-family: 'Courier New', monospace;
    font-weight: 600;
}
.label-input:focus, .label-input.has-value {
    border-color: #2152ff;
    box-shadow: 0 0 0 2px rgba(33,82,255,.1);
    background-color: #fcfdff;
}
.btn-generate {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px;
    background: #e2e8f0; color: #475569;
    border: none; border-radius: .375rem; cursor: pointer;
    font-size: .75rem; transition: background .15s, color .15s;
}
.btn-generate:hover { background: #cbd5e1; color: #1e293b; }

.btn-qr {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .35rem .65rem; font-size: .7rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff0080);
    border: none; border-radius: .375rem; cursor: pointer;
    white-space: nowrap;
    transition: opacity .15s, transform .1s;
}
.btn-qr:hover { opacity: .88; transform: translateY(-1px); }
.btn-qr:disabled { opacity: .4; cursor: not-allowed; transform: none; }

/* ─── Status Badge ────────────────────────────────── */
.status-pill {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .2rem .6rem; border-radius: .375rem;
    font-size: .68rem; font-weight: 700;
}
.status-labeled { background: #dcfce7; color: #15803d; }
.status-unlabeled { background: #fef3c7; color: #d97706; }

/* ─── Print & Modal Styles ────────────────────────── */
.qr-modal-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(15, 23, 42, .55);
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.qr-modal-overlay.open { display: flex; }
.qr-modal {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 25px 60px rgba(0,0,0,.22);
    padding: 2rem;
    width: 340px; max-width: 95vw;
    animation: modalSlideIn .2s ease;
    text-align: center;
    position: relative;
}
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-18px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.qr-modal-close {
    position: absolute; top: .9rem; right: .9rem;
    width: 28px; height: 28px;
    border: none; border-radius: .4rem;
    background: #f0f2f5; color: #7b809a;
    font-size: .85rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.qr-modal-close:hover { background: #e2e8f0; color: #344767; }
.qr-modal-title {
    font-size: 1rem; font-weight: 800;
    color: #344767; margin-bottom: .25rem;
}
.qr-modal-subtitle {
    font-size: .75rem; color: #adb5bd;
    margin-bottom: 1.25rem;
}
.qr-canvas-wrap {
    display: flex; justify-content: center; align-items: center;
    background: #f8f9fa;
    border-radius: .75rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
}
.qr-label-code {
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-size: .9rem; font-weight: 700;
    color: #344767;
    background: #f0f2f5;
    border-radius: .4rem;
    padding: .35rem .85rem;
    margin-bottom: .25rem;
    letter-spacing: .05em;
}
.qr-empty-hint {
    font-size: .8rem; color: #f59e0b; font-weight: 600;
    margin-top: .5rem;
}
.btn-print-label {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .5rem 1.1rem; font-size: .8rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #17ad37, #98ec2c);
    border: none; border-radius: .45rem; cursor: pointer;
    width: 100%; justify-content: center;
    margin-top: .85rem;
    transition: opacity .15s, transform .1s;
    box-shadow: 0 4px 10px -2px rgba(23,173,55,.35);
}
.btn-print-label:hover { opacity: .88; transform: translateY(-1px); }
.btn-print-label:disabled { opacity: .4; cursor: not-allowed; transform: none; }

.btn-print-all {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .55rem 1.25rem; font-size: .8125rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff0080);
    border: none; border-radius: .5rem; cursor: pointer;
    transition: opacity .15s, transform .1s, box-shadow .15s;
    box-shadow: 0 4px 12px -2px rgba(121,40,202,.4);
    white-space: nowrap;
}
.btn-print-all:hover { opacity: .88; transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(121,40,202,.5); }
.btn-print-all:disabled { opacity: .4; cursor: not-allowed; transform: none; box-shadow: none; }

/* ─── Empty State ─────────────────────────────────── */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: #adb5bd;
}
</style>

<div class="flex flex-wrap -mx-3">
    {{-- Page Header --}}
    <div class="w-full px-3">
        <div class="page-header-card">
            <div>
                <h4 class="page-title">
                    <i class="fas fa-qrcode mr-2" style="color:#7928ca;"></i>
                    Pencetakan & Labeling Inventaris
                </h4>
                <p class="page-subtitle">Daftar seluruh aset barang inventaris dari draf pengadaan disetujui yang siap dilabeli.</p>
            </div>
            <div>
                <button
                    type="button"
                    class="btn-print-all"
                    id="btnPrintAll"
                    onclick="printAllLabels()"
                    disabled
                >
                    <i class="fas fa-print"></i>
                    Cetak Label Terpilih (<span id="btnPrintAllCount">0</span>)
                </button>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(isset($error) && $error)
    <div class="w-full px-3 mb-4">
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:.875rem 1.25rem; border-radius:.75rem; font-size:.875rem; font-weight:600; display:flex; align-items:center; gap:.5rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ $error }}
        </div>
    </div>
    @endif

    {{-- Toolbar --}}
    <div class="w-full px-3">
        <div class="toolbar-card">
            <div class="toolbar-wrap">
                <div class="filter-group">
                    <div class="search-wrap">
                        <i class="fas fa-search search-icon"></i>
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Cari barang atau nomor draf..."
                            onkeyup="filterItems()"
                        />
                    </div>

                    <select id="statusFilter" class="select-filter" onchange="filterItems()">
                        <option value="all">Semua Status</option>
                        <option value="labeled">Sudah Dilabeli</option>
                        <option value="unlabeled">Belum Dilabeli</option>
                    </select>

                    <select id="yearFilter" class="select-filter" onchange="filterItems()">
                        <option value="all">Semua Tahun Anggaran</option>
                        @php
                            $years = collect($inventarisItems)->pluck('draft_year')->unique()->sort()->values();
                        @endphp
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="font-size:.75rem; color:#adb5bd; font-weight:600;">
                    Total: <span id="displayedCount">{{ count($inventarisItems) }}</span> barang
                </div>
            </div>
        </div>
    </div>

    {{-- Table Grid --}}
    <div class="w-full px-3">
        <div class="table-card">
            <div style="overflow-x: auto;">
                <table class="inventaris-table" id="inventarisTable">
                    <thead>
                        <tr>
                            <th style="width:50px; text-align:center;">No</th>
                            <th style="width:110px;">Tahun</th>
                            <th style="min-width:220px;">Nama Barang / Pengadaan</th>
                            <th style="width:80px; text-align:center;">Qty</th>
                            <th style="width:230px;">Input Label</th>
                            <th style="width:140px; text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventarisItems as $idx => $item)
                        <tr
                            class="item-row"
                            data-year="{{ $item['draft_year'] }}"
                            data-draft-id="{{ $item['draft_id'] }}"
                            data-original-index="{{ $item['original_index'] }}"
                            data-name="{{ strtolower($item['name']) }} {{ strtolower($item['draft_title']) }}"
                        >
                            {{-- No --}}
                            <td style="text-align:center; font-weight:600;">{{ $idx + 1 }}</td>

                            {{-- Tahun --}}
                            <td>
                                <span style="font-weight:600; color:#344767;">{{ $item['draft_year'] }}</span>
                            </td>

                            {{-- Nama Barang --}}
                            <td>
                                <p class="item-title">{{ $item['name'] }}</p>
                                <a href="{{ route('stafadmin.procurement.show', $item['draft_id']) }}" class="item-draft-link">
                                    <i class="fas fa-file-invoice"></i> {{ $item['draft_title'] }}
                                </a>
                            </td>

                            {{-- Qty --}}
                            <td style="text-align:center;">
                                <span style="background:#f1f5f9; padding:.2rem .55rem; border-radius:.35rem; font-weight:700;">
                                    {{ $item['quantity'] ?? 1 }}
                                </span>
                            </td>

                            {{-- Input Label --}}
                            <td>
                                <div class="label-input-wrap">
                                    <input
                                        type="text"
                                        class="label-input"
                                        id="label-{{ $item['draft_id'] }}-{{ $item['original_index'] }}"
                                        data-draft-id="{{ $item['draft_id'] }}"
                                        data-index="{{ $item['original_index'] }}"
                                        data-year="{{ $item['draft_year'] }}"
                                        data-name="{{ addslashes($item['name']) }}"
                                        placeholder="INV-{{ $item['draft_year'] }}-..."
                                        autocomplete="off"
                                    />
                                    <button
                                        type="button"
                                        class="btn-generate"
                                        onclick="generateLabel('{{ $item['draft_id'] }}', {{ $item['original_index'] }}, {{ $item['draft_year'] }})"
                                        title="Auto-generate nomor label"
                                    >
                                        <i class="fas fa-magic"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-qr"
                                        id="btn-qr-{{ $item['draft_id'] }}-{{ $item['original_index'] }}"
                                        onclick="openQrModal('{{ $item['draft_id'] }}', {{ $item['original_index'] }})"
                                        title="Lihat QR Code"
                                    >
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </div>
                            </td>

                            {{-- Status Labeling --}}
                            <td style="text-align:center;">
                                <span
                                    class="status-pill status-unlabeled"
                                    id="status-pill-{{ $item['draft_id'] }}-{{ $item['original_index'] }}"
                                >
                                    Belum Dilabeli
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-box-open" style="font-size:2.5rem; color:#cbd5e1; display:block; margin-bottom:.75rem;"></i>
                                    <p>Tidak ada data inventaris yang ditemukan.</p>
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

{{-- ─── QR Modal ─── --}}
<div class="qr-modal-overlay" id="qrModalOverlay" onclick="closeQrModalOnBg(event)">
    <div class="qr-modal" role="dialog" aria-modal="true" aria-labelledby="qrModalTitle">
        <button class="qr-modal-close" onclick="closeQrModal()" title="Tutup">
            <i class="fas fa-times"></i>
        </button>
        <div class="qr-modal-title" id="qrModalTitle">QR Code Label</div>
        <div class="qr-modal-subtitle" id="qrModalItemName">Nama Barang</div>

        <div class="qr-canvas-wrap">
            <div id="qrCanvas"></div>
        </div>

        <div class="qr-label-code" id="qrLabelCode">&mdash;</div>
        <div class="qr-empty-hint" id="qrEmptyHint" style="display:none;">
            <i class="fas fa-exclamation-triangle"></i>
            Isi nomor label terlebih dahulu untuk generate QR.
        </div>

        <button
            type="button"
            class="btn-print-label"
            id="btnPrintLabel"
            onclick="printSingleLabel()"
        >
            <i class="fas fa-print"></i> Cetak Label
        </button>
    </div>
</div>

{{-- CDN qrcode.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
// ─── Label Numbering & Storage ─────────────────────────────────────────────
const labelInputs = document.querySelectorAll('.label-input');

// Simpan data label terpilih untuk single print
let _currentPrintLabel = '';
let _currentPrintName  = '';

// Load data saat halaman siap
document.addEventListener('DOMContentLoaded', () => {
    labelInputs.forEach(input => {
        const draftId = input.dataset.draftId;
        const index   = input.dataset.index;
        const saved   = localStorage.getItem('label_' + draftId + '_' + index);

        if (saved) {
            input.value = saved;
            input.classList.add('has-value');
            updateRowStatus(draftId, index, true);
        } else {
            updateRowStatus(draftId, index, false);
        }
    });
    updateBulkButtonState();
});

// Update status label pill row
function updateRowStatus(draftId, index, isLabeled) {
    const pill = document.getElementById(`status-pill-${draftId}-${index}`);
    if (!pill) return;

    if (isLabeled) {
        pill.textContent = 'Sudah Dilabeli';
        pill.className = 'status-pill status-labeled';
    } else {
        pill.textContent = 'Belum Dilabeli';
        pill.className = 'status-pill status-unlabeled';
    }
}

// Dengarkan input manual
labelInputs.forEach(input => {
    input.addEventListener('input', function () {
        const draftId = this.dataset.draftId;
        const index   = this.dataset.index;
        const val     = this.value.trim();

        if (val !== '') {
            this.classList.add('has-value');
            localStorage.setItem('label_' + draftId + '_' + index, val);
            updateRowStatus(draftId, index, true);
        } else {
            this.classList.remove('has-value');
            localStorage.removeItem('label_' + draftId + '_' + index);
            updateRowStatus(draftId, index, false);
        }
        updateBulkButtonState();
    });
});

// Auto-generate nomor label berdasarkan urutan index
function generateLabel(draftId, index, year) {
    const input = document.getElementById(`label-${draftId}-${index}`);
    if (!input) return;

    // Untuk auto-generate, gunakan format INV-{YEAR}-INDEX (1-based index barang di draf)
    const order = parseInt(index) + 1;
    const num   = String(order).padStart(3, '0');
    input.value = `INV-${year}-${num}`;
    input.classList.add('has-value');

    localStorage.setItem('label_' + draftId + '_' + index, input.value);
    updateRowStatus(draftId, index, true);
    updateBulkButtonState();
}

// Update status tombol bulk print
function updateBulkButtonState() {
    let filled = 0;
    labelInputs.forEach(input => {
        // Hanya hitung input yang berada dalam baris yang sedang terlihat (tidak di-filter hidden)
        const tr = input.closest('tr');
        if (tr && tr.style.display !== 'none' && input.value.trim() !== '') {
            filled++;
        }
    });

    const btn = document.getElementById('btnPrintAll');
    const countSpan = document.getElementById('btnPrintAllCount');
    if (btn && countSpan) {
        countSpan.textContent = filled;
        btn.disabled = (filled === 0);
    }
}

// ─── Filters & Search ──────────────────────────────────────────────────────
function filterItems() {
    const query  = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const year   = document.getElementById('yearFilter').value;
    const rows   = document.querySelectorAll('.item-row');

    let count = 0;

    rows.forEach(row => {
        const rowYear = row.dataset.year;
        const rowName = row.dataset.name;
        const draftId = row.dataset.draftId;
        const index   = row.dataset.originalIndex;
        const input   = document.getElementById(`label-${draftId}-${index}`);
        const hasLabel = input && input.value.trim() !== '';

        // Match Query
        const matchQuery = rowName.includes(query);

        // Match Status
        let matchStatus = true;
        if (status === 'labeled') matchStatus = hasLabel;
        if (status === 'unlabeled') matchStatus = !hasLabel;

        // Match Year
        let matchYear = true;
        if (year !== 'all') matchYear = (rowYear === year);

        if (matchQuery && matchStatus && matchYear) {
            row.style.display = '';
            count++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('displayedCount').textContent = count;
    updateBulkButtonState();
}

// ─── Modal Handling ────────────────────────────────────────────────────────
function openQrModal(draftId, index) {
    const input    = document.getElementById(`label-${draftId}-${index}`);
    const labelVal = input ? input.value.trim() : '';
    const itemName = input ? (input.dataset.name || 'Barang Inventaris') : 'Barang Inventaris';

    _currentPrintLabel = labelVal;
    _currentPrintName  = itemName;

    document.getElementById('qrModalItemName').textContent = itemName;
    document.getElementById('qrLabelCode').textContent     = labelVal || '—';

    const canvasContainer = document.getElementById('qrCanvas');
    const emptyHint       = document.getElementById('qrEmptyHint');
    const btnPrint        = document.getElementById('btnPrintLabel');

    // Bersihkan QR sebelumnya
    canvasContainer.innerHTML = '';

    if (labelVal) {
        emptyHint.style.display = 'none';
        btnPrint.disabled       = false;
        
        // Generate QR ke container
        new QRCode(canvasContainer, {
            text:          labelVal,
            width:         180,
            height:        180,
            colorDark:     '#344767',
            colorLight:    '#ffffff',
            correctLevel:  QRCode.CorrectLevel.H
        });
    } else {
        emptyHint.style.display = 'block';
        btnPrint.disabled       = true;
    }

    document.getElementById('qrModalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeQrModal() {
    document.getElementById('qrModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function closeQrModalOnBg(e) {
    if (e.target === document.getElementById('qrModalOverlay')) closeQrModal();
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeQrModal();
});

// ─── Print Label ───────────────────────────────────────────────────────────
function buildLabelCard(labelCode, itemName, qrSrc) {
    return `
    <div class="label-card">
        <div class="label-header">Inventaris Laboratorium</div>
        ${qrSrc ? `<img class="label-qr" src="${qrSrc}" alt="QR ${labelCode}"/>` : '<div style="width:4cm;height:4cm;background:#f0f2f5;border-radius:.3cm;margin:.4cm auto;display:flex;align-items:center;justify-content:center;font-size:8pt;color:#adb5bd;">No QR</div>'}
        <div class="label-code">${labelCode}</div>
        <div class="label-name">${itemName}</div>
        <div class="label-footer">Dicetak ${new Date().toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})}</div>
    </div>`;
}

function getLabelPrintStyles() {
    return `
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background:#f0f2f5; padding: 1cm; }
        .labels-grid {
            display: flex; flex-wrap: wrap; gap: .5cm;
            justify-content: flex-start;
        }
        .label-card {
            width: 7cm; padding: .8cm;
            border: 2px solid #344767;
            border-radius: .4cm;
            background: #fff;
            text-align: center;
            page-break-inside: avoid;
        }
        .label-header {
            font-size: 7pt; font-weight: 800;
            text-transform: uppercase; letter-spacing: .08em;
            color: #7b809a; margin-bottom: .35cm;
            border-bottom: 1px solid #e2e8f0; padding-bottom: .25cm;
        }
        .label-qr { margin: .35cm auto; display: block; width: 3.5cm; height: 3.5cm; }
        .label-code {
            font-family: 'Courier New', monospace;
            font-size: 11pt; font-weight: 900;
            color: #1e293b; letter-spacing: .06em;
            margin: .25cm 0 .15cm;
        }
        .label-name {
            font-size: 8pt; color: #475569;
            margin-bottom: .25cm; font-weight: 600;
            word-break: break-word;
        }
        .label-footer {
            font-size: 6.5pt; color: #adb5bd;
            border-top: 1px solid #e2e8f0;
            padding-top: .2cm; margin-top: .15cm;
        }
        @media print {
            body { background:#fff; padding:.5cm; }
            .label-card { border-color: #000; }
        }
    `;
}

function printSingleLabel() {
    if (!_currentPrintLabel) return;

    const qrContainer = document.getElementById('qrCanvas');
    const qrImg       = qrContainer ? qrContainer.querySelector('img') : null;
    const qrSrc       = qrImg ? qrImg.src : '';

    const html = `<!DOCTYPE html><html lang="id"><head>
        <meta charset="UTF-8">
        <title>Label — ${_currentPrintLabel}</title>
        <style>${getLabelPrintStyles()}<\/style>
    </head><body>
        <div class="labels-grid">${buildLabelCard(_currentPrintLabel, _currentPrintName, qrSrc)}</div>
    </body></html>`;

    const pw = window.open('', '_blank', 'width=420,height=520');
    if (!pw) { alert('Izinkan popup browser untuk mencetak label.'); return; }
    pw.document.write(html);
    pw.document.close();
    pw.focus();
    setTimeout(() => { pw.print(); }, 400);
}

async function printAllLabels() {
    const cards = [];
    const tmpDiv = document.createElement('div');
    tmpDiv.style.cssText = 'position:fixed;left:-9999px;top:-9999px;visibility:hidden;';
    document.body.appendChild(tmpDiv);

    for (const input of labelInputs) {
        const tr = input.closest('tr');
        if (tr && tr.style.display !== 'none' && input.value.trim() !== '') {
            const labelCode = input.value.trim();
            const itemName  = input.dataset.name || 'Barang Inventaris';

            const qrHolder = document.createElement('div');
            tmpDiv.appendChild(qrHolder);

            await new Promise(resolve => {
                new QRCode(qrHolder, {
                    text:         labelCode,
                    width:        160, height: 160,
                    colorDark:    '#344767',
                    colorLight:   '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
                setTimeout(resolve, 250);
            });

            const qrImg = qrHolder.querySelector('img');
            const qrSrc = qrImg ? qrImg.src : '';
            cards.push(buildLabelCard(labelCode, itemName, qrSrc));
        }
    }

    document.body.removeChild(tmpDiv);

    if (cards.length === 0) {
        alert('Tidak ada label yang dapat dicetak.');
        return;
    }

    const html = `<!DOCTYPE html><html lang="id"><head>
        <meta charset="UTF-8">
        <title>Cetak Semua Label Inventaris</title>
        <style>${getLabelPrintStyles()}<\/style>
    </head><body>
        <div class="labels-grid">${cards.join('')}</div>
    </body></html>`;

    const pw = window.open('', '_blank', 'width=900,height=700');
    if (!pw) { alert('Izinkan popup browser untuk mencetak label.'); return; }
    pw.document.write(html);
    pw.document.close();
    pw.focus();
    setTimeout(() => { pw.print(); }, 500);
}
</script>
@endsection
